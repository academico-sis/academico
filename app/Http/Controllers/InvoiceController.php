<?php

namespace App\Http\Controllers;

use App\Interfaces\InvoicingInterface;
use App\Models\Book;
use App\Models\Comment;
use App\Models\Discount;
use App\Models\Enrollment;
use App\Models\Fee;
use App\Models\Payment;
use App\Models\Invoice;
use App\Models\InvoiceDetail;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Prologue\Alerts\Facades\Alert;

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

    /**
     * Create a invoice based on the cart contents for the specified user
     * Receive in the request: the user ID + the invoice data.
     */
    public function store(Request $request)
    {
        $enrollment = Enrollment::find($request->enrollment_id);

        // receive the client data and create a invoice with status = pending
        $invoice = Invoice::create([
            'client_name' => $request->client_name,
            'client_idnumber' => $request->client_idnumber,
            'client_address' => $request->client_address,
            'client_email' => $request->client_email,
            'client_phone' => $request->client_phone,
            'total_price' => $request->total_price,
        ]);

        $enrollment->invoice()->associate($invoice);
        $enrollment->save();

        // persist the products
        InvoiceDetail::create([
            'invoice_id' => $invoice->id,
            'product_name' => $enrollment->course->name,
            'product_code' => $enrollment->course->product_code,
            'product_id' => $enrollment->id,
            'product_type' => Enrollment::class,
            'price' => $enrollment->course->price ?? 0,
        ]);

        if (isset($request->comment)) {
            Comment::create([
                'commentable_id' => $enrollment->id,
                'commentable_type' => Enrollment::class,
                'body' => $request->comment,
                'author_id' => backpack_user()->id,
            ]);
        }

        foreach ($request->fees as $f => $fee) {
            InvoiceDetail::create([
                'invoice_id' => $invoice->id,
                'product_name' => $fee['name'],
                'product_code' => $fee['product_code'],
                'product_id' => $fee['id'],
                'product_type' => Fee::class,
                'price' => $fee['price'],
            ]);
        }

        foreach ($request->books as $b => $book) {
            InvoiceDetail::create([
                'invoice_id' => $invoice->id,
                'product_name' => $book['name'],
                'product_code' => $book['product_code'],
                'product_id' => $book['id'],
                'product_type' => Book::class,
                'price' => $book['price'],
            ]);
        }

        foreach ($request->discounts as $d => $discount) {
            InvoiceDetail::create([
                'invoice_id' => $invoice->id,
                'product_name' => $discount['name'],
                'product_id' => $discount['id'],
                'product_type' => Discount::class,
                'price' => - $discount['value'],
            ]);
        }

        foreach ($request->payments as $p => $payment) {
            Payment::create([
                'responsable_id' => backpack_user()->id,
                'invoice_id' => $invoice->id,
                'payment_method' => isset($payment['method']) ? $payment['method'] : null ,
                'value' => $payment['value'],
            ]);
        }

        // send the details to Accounting
        // and receive and store the invoice number
        if ($request->sendinvoice == true && config('invoicing.invoicing_system')) {
            try {
                $invoiceNumber = $this->invoicingService->saveInvoice($invoice);
                if ($invoiceNumber) {
                    $invoice->receipt_number = $invoiceNumber;
                    $invoice->save();
                }
            } catch (Exception $exception) {
                Log::error('Data could not be sent to accounting');
                Log::error($exception);
            }
        }

        // if the value of payments matches the total due price,
        // mark the invoice and associated enrollments as paid.
        foreach ($invoice->enrollments as $enrollment) {
            if ($invoice->total_price == $invoice->paidTotal()) {
                $enrollment->markAsPaid();
            }
        }
    }

    public function edit(Invoice $invoice)
    {
        return view('invoices.edit', compact('invoice'));
    }

    /**
     * Update the specified invoice (with the invoice number).
     */
    public function saveReceiptNumber(Invoice $invoice, Request $request)
    {
        $request->validate(['number' => 'string|required']);

        $invoice->receipt_number = $request->input('number');

        $invoice->save();

        return $invoice->fresh();
    }

    public function savePayments(Request $request, Invoice $invoice)
    {
        $invoice->payments()->delete();

        foreach ($request->payments as $payment)
        {
            $invoice->payments()->create([
                'payment_method' => isset($payment['payment_method']) ? $payment['payment_method'] : null,
                'value' => $payment['value'],
                'date' => isset($payment['date']) ? $payment['date'] : Carbon::now(),
                'status' => isset($payment['status']) ? $payment['status'] : null,
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

    public function show(Invoice $invoice)
    {
        return view('invoices.show', compact('invoice'));
    }
}
