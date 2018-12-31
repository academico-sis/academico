<?php

namespace App\Http\Controllers;

use App\Models\PreInvoice;
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
        // create a preinvoice for an enrollment

        $preinvoice = new PreInvoice;
        
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
        //
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
        //
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
