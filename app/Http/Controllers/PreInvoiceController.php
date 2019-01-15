<?php

namespace App\Http\Controllers;

use App\Models\PreInvoice;
use App\Models\UserData;
use App\Models\User;
use App\Models\Cart;
use App\Models\PreInvoiceDetail;
use App\Models\Course;
use App\Models\Enrollment;

use Illuminate\Http\Request;

class PreInvoiceController extends Controller
{


    
    /**
     * Create a preinvoice based on the cart contents for the specified user
     * 
     * Receive in the request: the user ID + the invoice data.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store($student, Request $request)
    {

        // ensure that there are any products in the cart
        if (Cart::where('user_id', $student)->count() == 0)
        {
            return "The cart has no item";
        }

        // retrieve which data to use for the invoice

        if ($request->input('invoice_data') !== null)
        {
            $invoice_data = UserData::findOrFail($request->input('invoice_data'));
        }
        else
        {
            $invoice_data = User::findOrFail($student);
        }


        // generate a new preinvoice
        $preinvoice = new PreInvoice;
        $preinvoice->user_id = $student;
        //$preinvoice->user_data_id = $invoice_data->id;
        $preinvoice->client_name = $invoice_data->name;
        $preinvoice->client_idnumber =  $invoice_data->idnumber;
        $preinvoice->client_address = $invoice_data->address;
        $preinvoice->client_email = $invoice_data->email; // todo if null look for student email.
        $preinvoice->total_price = 0;
        $preinvoice->save();

        $cart = Cart::get_user_cart($student);

        //return $cart;
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
                
                $preinvoice->enrollments()->attach($enrollment);
            }
        }


        // clear the cart
        Cart::clear_cart_for_user($student);

        return redirect("/preinvoice/" . $preinvoice->id);
    }




    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\PreInvoice  $preInvoice
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, PreInvoice $preInvoice)
    {
        $preInvoice->invoice_number = $request->input('invoice_number');
        $preInvoice->save();
        return redirect()->back();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\PreInvoice  $preInvoice
     * @return \Illuminate\Http\Response
     */
    public function destroy(PreInvoice $preInvoice)
    {
        //
    }
}
