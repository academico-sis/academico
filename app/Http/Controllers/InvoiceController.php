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
use Illuminate\Support\Str;
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
        if (config('invoicing.price_categories_enabled')) {
            abort(403, 'Unable to create an invoice because price categories are enabled in your setup.');
        }

        return view('carts.show', ['enrollment' => null,
            'products' => [],
            'invoicetypes' => InvoiceType::all(),
            'clients' => [],
            'availableBooks' => Book::all(),
            'availableFees' => Fee::all(),
            'availableDiscounts' => Discount::all(),
            'availableTaxes' => Tax::all(),
            'availablePaymentMethods' => Paymentmethod::all(), ]);
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
            'invoice_type_id' => $request->invoicetype,
            'receipt_number' => $request->receiptnumber,
            'date' => $request->has('date') ? Carbon::parse($request->date) : Carbon::now(),
        ]);

        $invoice->setNumber(); // TODO extract this to model events.

        // persist the products
        foreach ($request->products as $f => $product) {
            $productType = match ($product['type']) {
                'enrollment' => Enrollment::class,
                'scheduledPayment' => ScheduledPayment::class,
                'fee' => Fee::class,
                'book' => Book::class,
            };

            $productFinalPrice = 0; // used to compute the final price with taxes and discounts

            $productFinalPrice += $product['price'] * ($product['quantity'] ?? 1) * 100;

            // The front end sends the discounts value as percent, but  for the invoice we want to store their actual value relative to the product they were applied on
            if (isset($product['discounts'])) {
                foreach ($product['discounts'] as $d => $discount) {
                    InvoiceDetail::create([
                        'invoice_id' => $invoice->id,
                        'product_name' => $discount['name'],
                        'product_id' => $discount['id'],
                        'product_type' => Discount::class,
                        'price' => -($discount['value'] / 100) * $product['price'] * ($product['quantity'] ?? 1),
                    ]);

                    $productFinalPrice -= (($discount['value']) * $product['price']) * ($product['quantity'] ?? 1); // no need to multiply by 100 because discount is in %
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
                'comment' => isset($product['comment']) ? Str::limit($product['comment'], 150) : null,
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
        if ($request->sendinvoice && config('invoicing.invoicing_system')) {
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
            $this->ifTheInvoiceIsFullyPaidMarkItsProductsAsSuch($invoice);

            if (isset($request->comment)) {
                Comment::create([
                    'commentable_id' => $invoice->id,
                    'commentable_type' => Invoice::class,
                    'body' => $request->comment,
                    'author_id' => backpack_user()->id,
                ]);
            }
        } else {
            Invoice::where('id', $invoice->id)->delete();
            abort(500);
        }
    }

    public function download(Invoice $invoice)
    {
        App::setLocale(config('app.locale'));

        $customer = new Buyer([
            'name' => $invoice->client_name,
            'custom_fields' => [
                'nif/cif' => $invoice->client_idnumber,
                'domicilio' => $invoice->client_address,
                'email' => $invoice->client_email,
            ],
        ]);

        $notes = [$invoice->invoiceType->notes];

        foreach ($invoice->comments as $comment) {
            $notes[] = $comment->body;
        }

        $notes = implode('<br><br>', $notes);

        $currencyFormat = config('academico.currency_position') === 'before' ? '{SYMBOL}{VALUE}' : '{VALUE}{SYMBOL}';
        $generatedInvoice = InvoiceAlias::make()
            ->buyer($customer)
            ->series($invoice->invoice_series)
            ->sequence($invoice->invoice_number ?? $invoice->id)
            ->dateFormat('d/m/Y')
            ->date($invoice->date)
            ->logo(storage_path('logo2.png'))
            ->currencySymbol(config('academico.currency_symbol'))
            ->currencyCode(config('academico.currency_code'))
            ->currencyFormat($currencyFormat)
            ->notes($notes ?? '');

        foreach ($invoice->invoiceDetails as $product) {
            $item = (new InvoiceItem())->title($product->product_name)->pricePerUnit($product->price)->quantity($product->quantity);

            $description = null;

            if ($product->product instanceof Enrollment) {
                if ($product->product->course->name) {
                    $description .= "Curso: ".$product->product->course->name;
                }

                if ($product->product->course?->level?->name) {
                    $description .= "<br>Nivel: ".$product->product->course->level->name;
                }
            }

            if ($product->comment) {
                $description .= "<br><br>".$product->comment;
            }

            if ($description) {
                $item->description($description);
            }

            $generatedInvoice->addItem($item);
        }

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

        $this->ifTheInvoiceIsFullyPaidMarkItsProductsAsSuch($invoice);

        return $invoice->fresh()->payments;
    }

    public function ifTheInvoiceIsFullyPaidMarkItsProductsAsSuch(Invoice $invoice): void
    {
        foreach ($invoice->scheduledPayments as $scheduledPayment) {
            if ($invoice->totalPrice() == $invoice->paidTotal()) {
                $scheduledPayment->product->markAsPaid();

                /** @var Enrollment $relatedEnrollment */
                $relatedEnrollment = $scheduledPayment->product->enrollment;
                if ($relatedEnrollment && $relatedEnrollment->scheduledPayments->where('status', '!==', 2)->count() === 0) {
                    $relatedEnrollment->markAsPaid();
                }
            }
        }

        foreach ($invoice->enrollments as $enrollment) {
            if ($invoice->totalPrice() == $invoice->paidTotal() && $enrollment->product->price <= $enrollment->product->totalPaidPrice()) {
                $enrollment->product->markAsPaid();
            }
        }
    }
}
