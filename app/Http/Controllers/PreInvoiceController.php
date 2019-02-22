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
    public function store($student, Request $request)
    {

        // ensure that there are any products in the cart
        if (Cart::where('user_id', $student)->count() == 0)
        {
            return "The cart has no item"; // todo translate this
        }

        // retrieve which data to use for the invoice

        if ($request->input('invoice_data') !== null)
        {
            $invoice_data = Contact::findOrFail($request->input('invoice_data'));
        }
        else
        {
            $invoice_data = User::findOrFail($student);
        }


        // generate a new preinvoice
        $preinvoice = new PreInvoice;
        $preinvoice->user_id = $student;
        $preinvoice->client_name = $invoice_data->name;
        $preinvoice->client_idnumber =  $invoice_data->idnumber;
        $preinvoice->client_address = $invoice_data->address;
        $preinvoice->client_email = $invoice_data->email;
        $preinvoice->total_price = 0;
        $preinvoice->save();

        $cart = Cart::get_user_cart($student);

        // for each product in the cart
        foreach ($cart as $product)
        {
            // generate a preinvoice product (detail)
            $detail = new PreInvoiceDetail;
            $detail->pre_invoice_id = $preinvoice->id;
            $detail->product_name = $product->product->name ?? 'product without name'; // todo fix this
            $detail->price = $product->product->price ?? 0; // todo fix this

            $detail->save();

            // mark the enrollment(s) as paid.
            // link the enrollment(s) to the newly ceated preinvoice
            if($product->product_type == Enrollment::class)
            {
                $enrollment = Enrollment::find($product->product_id);
                $enrollment->status_id = 2;
                $enrollment->save();

                if($enrollment->childrenEnrollments->count() > 0)
                {
                    foreach ($enrollment->childrenEnrollments as $child_enrollment)
                    {
                        $child_enrollment->status_id = 2;
                        $child_enrollment->save();
                    }
                }

                
                $preinvoice->enrollments()->attach($enrollment);
            }
        }

        // clear the cart
        // TODO clear cart only if every preceding step has been completed (try-catch)
        Cart::clear_cart_for_user($student);

        return redirect("/preinvoice/" . $preinvoice->id);
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
