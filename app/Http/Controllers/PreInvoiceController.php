<?php

namespace App\Http\Controllers;

use App\Models\PreInvoice;
use App\Models\User;
use Illuminate\Http\Request;
use Prologue\Alerts\Facades\Alert;

class PreInvoiceController extends Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->middleware(['permission:enrollments.edit']);
    }

    /**
     * Create a preinvoice based on the cart contents for the specified user
     * Receive in the request: the user ID + the invoice data.
     */
    public function store(Request $request)
    {
        // receive the client data and create a preinvoice with status = pending
        $preinvoice = new PreInvoice;
        $preinvoice->fill([
            'client_name' => $request->client_name,
            'client_idnumber' => $request->client_idnumber,
            'client_address' => $request->client_address,
            'client_email' => $request->client_email,
            'client_phone' => $request->client_phone,
            'total_price' => $request->total_price,
        ]);

        // mark the preinvoice and associated enrollments as paid.
        foreach ($preinvoice->enrollments as $enrollment) {
            $enrollment->markAsPaid();
        }

        // receive the list of products and generate the preinvoice details

        foreach ($request->enrollments as $e => $enrollment) {
            $enrollment = Enrollment::find($enrollment['id']);
            if ($enrollment->status_id != 1) {
                abort(422, 'Esta matricula no esta pendiente');
            }

            PreInvoiceDetail::create([
                'pre_invoice_id' => $preinvoice->id,
                'product_name' => $enrollment['course']['name'],
                'product_code' => $enrollment['course']['product_code'],
                'product_id' => $enrollment['id'],
                'product_type' => Enrollment::class,
                'price' => $enrollment['course']['price'],
            ]);

            $preinvoice->enrollments()->attach($enrollment);

            if (isset($request->comment)) {
                Comment::create([
                    'commentable_id' => $enrollment->id,
                    'commentable_type' => Enrollment::class,
                    'body' => $request->comment,
                    'author_id' => backpack_user()->id,
                ]);
            }
        }

        foreach ($request->fees as $f => $fee) {
            PreInvoiceDetail::create([
                'pre_invoice_id' => $preinvoice->id,
                'product_name' => $fee['name'],
                'product_code' => $fee['product_code'],
                'product_id' => $fee['id'],
                'product_type' => Fee::class,
                'price' => $fee['price'],
            ]);
        }

        foreach ($request->books as $b => $book) {
            PreInvoiceDetail::create([
                'pre_invoice_id' => $preinvoice->id,
                'product_name' => $book['name'],
                'product_code' => $book['product_code'],
                'product_id' => $book['id'],
                'product_type' => Book::class,
                'price' => $book['price'],
            ]);
        }

        foreach ($request->payments as $p => $payment) {
            Payment::create([
                'responsable_id' => backpack_user()->id,
                'pre_invoice_id' => $preinvoice->id,
                'payment_method' => $payment['method'],
                'value' => $payment['value'],
                'comment' => $payment['comment'],
            ]);
        }
    }

    public function edit(PreInvoice $preInvoice)
    {
        return view('invoices.edit', compact('preInvoice'));
    }

    /**
     * Update the specified preinvoice (with the invoice number).
     */
    public function update(Request $request, PreInvoice $preInvoice)
    {
        $preInvoice->invoice_number = $request->input('invoice_number');
        $preInvoice->save();
        Alert::success(__('The invoice number has been saved'))->flash();

        return redirect()->back();
    }
}
