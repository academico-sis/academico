<?php

namespace App\Http\Controllers;

use App\Models\Config;
use App\Models\PreInvoice;
use App\Models\User;
use App\Services\Ecuasolutions;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Prologue\Alerts\Facades\Alert;

class PreInvoiceController extends Controller
{
    public function __construct(Ecuasolutions $ecuasolutions)
    {
        parent::__construct();
        $this->middleware(['permission:enrollments.edit']);
        $this->ecuasolutions = $ecuasolutions;
    }

    public function accountingServiceStatus()
    {
        return $this->ecuasolutions->checkServerStatus();
    }

    /**
     * Create a preinvoice based on the cart contents for the specified user
     * Receive in the request: the user ID + the invoice data.
     */
    public function store(Request $request)
    {
        $ivkardex = [];
        $pckardex = [];

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

        // send the details to Accounting
        // and receive and store the invoice number
        if ($request->sendinvoice == true && config('settings.external_accounting_enabled') == true) {
            $this->ecuasolutions->sendInvoiceToAccountingSystem($request, $preinvoice);
        }

        // mark the preinvoice and associated enrollments as paid.
        foreach ($preinvoice->enrollments as $enrollment) {
            $enrollment->markAsPaid();
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
