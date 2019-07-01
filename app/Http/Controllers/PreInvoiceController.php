<?php

namespace App\Http\Controllers;

use App\Models\PreInvoice;
use App\Models\Contact;
use App\Models\User;
use App\Models\Cart;
use App\Models\PreInvoiceDetail;
use App\Models\Course;
use App\Models\Enrollment;

use Illuminate\Http\Request;

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
        dump($request);

        return response()->json([
            'name' => 'Abigail',
            'state' => 'CA'
        ]);
        
        // receive the client data and generate the preinvoice with status = pending
        // receive the list of products and generate the preinvoice details
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
