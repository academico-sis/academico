<?php

namespace App\Http\Controllers\Admin;

use Backpack\CRUD\app\Http\Controllers\CrudController;
use App\Models\PreInvoice;

// VALIDATION: change the requests to match your own file names if you need form validation
use App\Http\Requests\PreInvoiceRequest as StoreRequest;
use App\Http\Requests\PreInvoiceRequest as UpdateRequest;

/**
 * Class PreInvoiceCrudController
 * @package App\Http\Controllers\Admin
 * @property-read CrudPanel $crud
 */
class PreInvoiceCrudController extends CrudController
{
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

        /*
        |--------------------------------------------------------------------------
        | CrudPanel Configuration
        |--------------------------------------------------------------------------
        */

        // TODO: remove setFromDb() and manually define Fields and Columns
        $this->crud->setFromDb();


        $permissions = backpack_user()->getAllPermissions();
        $this->crud->allowAccess('show');

        $this->crud->denyAccess('update');
        $this->crud->denyAccess('delete');
        $this->crud->denyAccess('create');


        // add asterisk for fields that are required in PreInvoiceRequest
        $this->crud->setRequiredFields(StoreRequest::class, 'create');
        $this->crud->setRequiredFields(UpdateRequest::class, 'edit');
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
        return view('invoices.show', compact('preInvoice'));
    }
}
