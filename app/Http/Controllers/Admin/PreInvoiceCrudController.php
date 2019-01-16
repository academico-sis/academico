<?php

namespace App\Http\Controllers\Admin;

use Backpack\CRUD\app\Http\Controllers\CrudController;
use App\Models\PreInvoice;

// VALIDATION: change the requests to match your own file names if you need form validation
//use App\Http\Requests\PreInvoiceRequest as StoreRequest;
//use App\Http\Requests\PreInvoiceRequest as UpdateRequest;

/**
 * Class PreInvoiceCrudController
 * @package App\Http\Controllers\Admin
 * @property-read CrudPanel $crud
 */
class PreInvoiceCrudController extends CrudController
{

    public function __construct()
    {
        parent::__construct();
        $this->middleware(['permission:enrollments.view']);
    }


    public function setup()
    {
        /*
        |--------------------------------------------------------------------------
        | CrudPanel Basic Information
        |--------------------------------------------------------------------------
        */
        $this->crud->setModel('App\Models\PreInvoice');
        $this->crud->setRoute(config('backpack.base.route_prefix') . '/preinvoice');
        $this->crud->setEntityNameStrings('preinvoice', 'pre_invoices');
        $this->crud->orderBy('created_at', 'desc');

        /*
        |--------------------------------------------------------------------------
        | CrudPanel Configuration
        |--------------------------------------------------------------------------
        */

        $this->crud->setColumns([
            // client name
            ['label' => "Client Name", 'type' => "text", 'name' => 'client_name'],

            // client ID number
            ['label' => "Client ID number", 'type' => "text", 'name' => 'client_idnumber'],

            // client email
            ['label' => "Client Email", 'type' => "text", 'name' => 'client_email'],

            // student
            ['label' => 'Student', 'type'  => 'select', 'entity' => 'user', 'attribute' => 'name'],

            // total price
            ['label' => "Total", 'type' => "numeric", 'name' => 'total_price', 'prefix' => '$ '],

            // date
            ['label' => "Client Email", 'type' => "text", 'name' => 'client_email'],

            // real invoice number
            ['label' => "Invoice Number", 'type' => "text", 'name' => 'invoice_number'],

        ]);


        //$permissions = backpack_user()->getAllPermissions();
        $this->crud->allowAccess('show');
        $this->crud->denyAccess('update');
        $this->crud->denyAccess('delete');
        $this->crud->denyAccess('create');


        // add asterisk for fields that are required in PreInvoiceRequest
        //$this->crud->setRequiredFields(StoreRequest::class, 'create');
        //$this->crud->setRequiredFields(UpdateRequest::class, 'edit');
    }

    public function store(StoreRequest $request)
    {
        // your additional operations before save here
        $redirect_location = parent::storeCrud($request);
        // your additional operations after save here
        // use $this->data['entry'] or $this->crud->entry
        return $redirect_location;
    }

    public function update(UpdateRequest $request)
    {
        // your additional operations before save here
        $redirect_location = parent::updateCrud($request);
        // your additional operations after save here
        // use $this->data['entry'] or $this->crud->entry
        return $redirect_location;
    }

    public function show($preInvoice)
    {
        $preInvoice = PreInvoice::findOrFail($preInvoice);
        $products = $preInvoice->pre_invoice_details;
        return view('invoices.show', compact('preInvoice', 'products'));
    }
}
