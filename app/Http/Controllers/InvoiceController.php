<?php

namespace App\Http\Controllers;

use App\Interfaces\InvoicingInterface;
use App\Models\Book;
use App\Models\Comment;
use App\Models\Config;
use App\Models\Discount;
use App\Models\Enrollment;
use App\Models\Fee;
use App\Models\Invoice;
use App\Models\InvoiceDetail;
use App\Models\InvoiceType;
use App\Models\Payment;
use App\Models\Paymentmethod;
use App\Models\ScheduledPayment;
use App\Models\Tax;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;
use LaravelDaily\Invoices\Classes\Buyer;
use LaravelDaily\Invoices\Classes\InvoiceItem;
use LaravelDaily\Invoices\Invoice as InvoiceAlias;

class InvoiceController extends Controller
{
    public function __construct(public InvoicingInterface $invoicingService)
    {
        parent::__construct();
        $this->middleware(['permission:enrollments.edit']);
    }

    public function accountingServiceStatus()
    {
        return $this->invoicingService->status();
    }

    public function create()
    {
        $data = ['enrollment' => null, 'products' => [], 'invoicetypes' => InvoiceType::all(), 'clients' => [], 'availableBooks' => Book::all(), 'availableFees' => Fee::all(), 'availableDiscounts' => Discount::all(), 'availableTaxes' => Tax::all(), 'availablePaymentMethods' => Paymentmethod::all()];
        if (config('invoicing.price_categories_enabled')) {
            abort(403, 'Unable to create an invoice because price categories are enabled in your setup.');
        }

        return view('carts.show', $data);
    }

    /**
     * Create a invoice based on the cart contents for the specified user
     * Receive in the request: the user ID + the invoice data.
     */
    public function store(Request $request)
    {

        // receive the client data and create a invoice with status = pending
        $invoice = Invoice::create([
            'client_name' => $request->client_name,
            'client_idnumber' => $request->client_idnumber,
            'client_address' => $request->client_address,
            'client_email' => $request->client_email,
            'client_phone' => $request->client_phone,
            'total_price' => $request->total_price,
            'invoice_type_id' => $request->invoicetype,
            'receipt_number' => $request->receiptnumber,
            'date' => $request->has('date') ? Carbon::parse($request->date) : Carbon::now(),
        ]);

        $invoice->setNumber(); // TODO extract this to model events.

        // persist the products
        foreach ($request->products as $f => $product) {
            $productType = match ($product['type']) {
                'enrollment' => Enrollment::class,
                'scheduledPayment' => Enrollment::class,
                'fee' => Fee::class,
                'book' => Book::class,
            };

            $productFinalPrice = 0; // used to compute the final price with taxes and discounts

            if ($product['type'] === 'enrollment') {
                Enrollment::find($product['id'])->invoices()->attach($invoice, ['scheduled_payment_id' => $request->scheduled_payment_id ?? null]);
            }

            if ($product['type'] === 'scheduledPayment') {
                $scheduledPayment = ScheduledPayment::find($product['id']);
                $scheduledPayment->enrollment->invoices()->attach($invoice, ['scheduled_payment_id' => $product['id']]);
                // Reset status to default value
                $scheduledPayment->update(['status' => null]);
            }

            $productFinalPrice += $product['price'] * ($product['quantity'] ?? 1) * 100;

            if (isset($product['discounts'])) {
                foreach ($product['discounts'] as $d => $discount) {
                    $productFinalPrice -= (($discount['value']) * $product['price']) * ($product['quantity'] ?? 1); // no need to multiply by 100 because discount is in %

                    InvoiceDetail::create([
                        'invoice_id' => $invoice->id,
                        'product_name' => $discount['name'],
                        'product_id' => $discount['id'],
                        'product_type' => Discount::class,
                        'price' => -$discount['value'] * ($product['quantity'] ?? 1),
                    ]);
                }
            }

            if (isset($product['taxes'])) {
                foreach ($product['taxes'] as $d => $tax) {
                    $productFinalPrice += (($tax['value']) * $product['price']) * ($product['quantity'] ?? 1); // no need to multiply by 100 because discount is in %

                    InvoiceDetail::create([
                        'invoice_id' => $invoice->id,
                        'product_name' => $tax['name'],
                        'product_id' => $tax['id'],
                        'product_type' => Tax::class,
                        'price' => $product['price'] * ($tax['value'] / 100) * ($product['quantity'] ?? 1),
                    ]);
                }
            }

            InvoiceDetail::create([
                'invoice_id' => $invoice->id,
                'product_name' => $product['name'],
                'product_code' => $product['product_code'],
                'product_id' => $product['id'],
                'product_type' => $productType,
                'price' => $product['price'],
                'final_price' => $productFinalPrice,
                'quantity' => $product['quantity'] ?? 1,
                //'tax_rate' => collect($product['taxes'] ?? [])->sum('value'),
            ]);
        }

        foreach ($request->payments as $p => $payment) {
            Payment::create([
                'responsable_id' => backpack_user()->id,
                'invoice_id' => $invoice->id,
                'payment_method' => $payment['method'] ?? null,
                'value' => $payment['value'],
                'date' => isset($payment['date']) ? Carbon::parse($payment['date']) : Carbon::now(),
            ]);
        }

        // send the details to Accounting
        // and receive and store the invoice number
        if ($request->sendinvoice == true && config('invoicing.invoicing_system')) {
            try {
                $invoiceNumber = $this->invoicingService->saveInvoice($invoice);
                Log::info($invoiceNumber);
                if ($invoiceNumber !== null) {
                    $invoice->receipt_number = $invoiceNumber;
                    $invoice->save();
                    $success = true;
                } else {
                    Invoice::where('id', $invoice->id)->delete();
                    abort(500);
                }
            } catch (Exception $exception) {
                Log::error('Data could not be sent to accounting');
                Log::error($exception);
            }
        } else {
            $success = true;
        }
        if (isset($success)) {
            // if the value of payments matches the total due price,
            // mark the invoice and associated enrollments as paid.
            foreach ($invoice->enrollments as $enrollment) {
                if ($enrollment->price == $invoice->paidTotal()) {
                    $enrollment->markAsPaid();
                } elseif ($enrollment->scheduledPayments->where('computed_status', '!==', 2)->count() === 0) {
                    $enrollment->markAsPaid();
                }
                if (isset($request->comment)) {
                    Comment::create([
                        'commentable_id' => $invoice->id,
                        'commentable_type' => Invoice::class,
                        'body' => $request->comment,
                        'author_id' => backpack_user()->id,
                    ]);
                }
            }
        } else {
            Invoice::where('id', $invoice->id)->delete();
            abort(500);
        }
    }

    public function edit(Invoice $invoice)
    {
        return view('invoices.edit', compact('invoice'));
    }

    public function download(Invoice $invoice)
    {
        App::setLocale(config('app.locale'));

        $customer = new Buyer([
            'name'          => $invoice->client_name,
            'custom_fields' => [
                'nif/cif' => $invoice->client_idnumber,
                'domicilio' => $invoice->client_address,
                'email' => $invoice->client_email,
            ],
        ]);

        $notes = $invoice->invoiceType->notes;

        $currencyFormat = config('app.currency_position') === 'before' ? '{SYMBOL}{VALUE}' : '{VALUE}{SYMBOL}';
        $generatedInvoice = InvoiceAlias::make()
            ->buyer($customer)
            ->series($invoice->invoice_series)
            ->sequence($invoice->invoice_number ?? $invoice->id)
            ->dateFormat('d/m/Y')
            ->date($invoice->date)
            ->logo(storage_path('logo2.png'))
            ->currencySymbol(config('app.currency_symbol'))
            ->currencyCode(config('app.currency_code'))
            ->currencyFormat($currencyFormat)
            ->notes($notes ?? '');

        //$taxIsGlobal = $invoice->products->pluck('tax_rate')->unique()->count() === 1;
        //$taxRate = $invoice->taxes->pluck('tax_rate')->unique()->first();

        foreach ($invoice->invoiceDetails as $product) {
            $item = (new InvoiceItem())->title($product->product_name)->pricePerUnit($product->price)->quantity($product->quantity);

            /*if (!$taxIsGlobal)
            {
                $item->taxByPercent($product->tax_rate);
            }*/

            $generatedInvoice->addItem($item);
        }

        /*if ($taxRate > 0 && $taxIsGlobal)
        {
            $generatedInvoice->taxRate($taxRate);
        }*/

        $generatedInvoice->footer = Config::firstWhere('name', 'invoice_footer')->value ?? '';

        return $generatedInvoice->stream();
    }

    public function savePayments(Request $request, Invoice $invoice)
    {
        $invoice->payments()->delete();

        foreach ($request->payments as $payment) {
            $invoice->payments()->create([
                'payment_method' => $payment['payment_method'] ?? null,
                'value' => $payment['value'],
                'date' => isset($payment['date']) ? Carbon::parse($payment['date']) : Carbon::now(),
                'status' => $payment['status'] ?? 1,
                'responsable_id' => backpack_user()->id,
            ]);
        }

        // if the payments match the enrollment price, mark as paid.
        foreach ($invoice->enrollments as $enrollment) {
            if ($invoice->total_price == $invoice->paidTotal() && $invoice->payments->where('status', '!==', 2)->count() === 0) {
                $enrollment->markAsPaid();
            }
        }

        return $invoice->fresh()->payments;
    }
}
