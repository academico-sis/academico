<?php

namespace App\Http\Controllers;

use App\Models\Fee;
use App\Models\Book;
use App\Models\Cart;
use App\Models\User;
use App\Models\Course;
use App\Models\Contact;
use App\Models\Enrollment;

use App\Models\PreInvoice;
use Illuminate\Http\Request;
use App\Models\PreInvoiceDetail;

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
/*         dump($request);

        return response()->json([
            'name' => 'Abigail',
            'state' => 'CA'
        ]); */
        
        // receive the client data and generate the preinvoice with status = pending

        $preinvoice = PreInvoice::create([
            'client_name' => $request->client_name,
            'client_idnumber' => $request->client_idnumber,
            'client_address' => $request->client_address,
            'client_email' => $request->client_email,
            'total_price' => $request->total_price,
        ]);

        // receive the list of products and generate the preinvoice details

        foreach($request->enrollments as $enrollment)
        {
            PreInvoiceDetail::create([
                'pre_invoice_id' => $preinvoice->id,
                'product_name' => $enrollment['course']['name'], // todo
                'product_code' => $enrollment['course']['id'], // todo
                'product_id' => $enrollment['id'],
                'product_type' => Enrollment::class,
                'price' => $enrollment['course']['price']
            ]);
        }

        foreach($request->fees as $fee)
        {
            PreInvoiceDetail::create([
                'pre_invoice_id' => $preinvoice->id,
                'product_name' => $fee['name'],
                'product_code' => $fee['id'], // todo
                'product_id' => $fee['id'],
                'product_type' => Fee::class,
                'price' => $fee['price']
            ]);
        }

        foreach($request->books as $book)
        {
            PreInvoiceDetail::create([
                'pre_invoice_id' => $preinvoice->id,
                'product_name' => $book['name'],
                'product_code' => $book['id'], // todo
                'product_id' => $book['id'],
                'product_type' => Book::class,
                'price' => $book['price']
            ]);
        }


        // send the details to Accounting
        // receive the confirmation
        // mark the preinvoice and associated enrollments as paid.
        // show a confirmation
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
