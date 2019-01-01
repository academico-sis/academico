<?php

namespace App\Http\Controllers;

use App\Models\PreInvoice;
use App\Models\UserData;
use App\Models\Cart;
use App\Models\PreInvoiceDetail;

use Illuminate\Http\Request;

class PreInvoiceController extends Controller
{
    /**
     * Display a list of all preinvoices generated
     * 
     * Here we also should allow some filtering (preinvoices without invoice number, etc)
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $invoices = PreInvoice::all();
        return view('invoices.index', compact('invoices'));
    }

    
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
        // if there are any products in the cart
        
        // check if we are using custom invoice data

        if ($request->input('invoice_data') !== null)
        {
            $invoice_data = UserData::findOrFail($request->input('invoice_data'));
        }
        else
        {
            $invoice_data = UserData::where('user_id', $student)->firstOrFail();
        }

        dump($invoice_data);

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

        dump($cart);
        // for each product in the cart
        foreach ($cart as $product)
        {
            // generate a preinvoice product (detail)
            $detail = new PreInvoiceDetail;
            $detail->pre_invoice_id = $preinvoice->id;
            $detail->product_name = $product->product->name;
            $detail->price = $product->product->price;

            $detail->save();
        }

        // todo clear the cart
        
        dump($student);
        dump($request->all());
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\PreInvoice  $preInvoice
     * @return \Illuminate\Http\Response
     */
    public function show(PreInvoice $preInvoice)
    {
        return view('invoices.show', compact('preInvoice'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\PreInvoice  $preInvoice
     * @return \Illuminate\Http\Response
     */
    public function edit(PreInvoice $preInvoice)
    {
        //
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
