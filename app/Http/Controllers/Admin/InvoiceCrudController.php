<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\InvoiceRequest;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\Paymentmethod;
use App\Models\Scholarship;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

/**
 * Class InvoiceCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class InvoiceCrudController extends CrudController
{
    use \Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
    use ShowOperation { show as traitShow; }

    /**
     * Configure the CrudPanel object. Apply settings to all operations.
     *
     * @return void
     */
    public function setup()
    {
        CRUD::setModel(\App\Models\Invoice::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/invoice');
        CRUD::setEntityNameStrings('invoice', 'invoices');
        $this->crud->addButtonFromView('top', 'createInvoice', 'createInvoice', 'start');
    }

    /**
     * Define what happens when the List operation is loaded.
     *
     * @see  https://backpackforlaravel.com/docs/crud-operation-list-entries
     * @return void
     */
    protected function setupListOperation()
    {
        CRUD::column('id');
        CRUD::column('invoice_number');
        CRUD::column('invoice_type_id');
        CRUD::column('client_name');
        CRUD::column('client_idnumber');
        CRUD::column('client_address');
        CRUD::column('client_email');
        CRUD::column('client_phone');
        CRUD::column('total_price');
        CRUD::column('company_id');
        CRUD::column('receipt_number');
        CRUD::column('created_at');
        CRUD::column('updated_at');
        CRUD::column('deleted_at');

        /**
         * Columns can be defined using the fluent syntax or array syntax:
         * - CRUD::column('price')->type('number');
         * - CRUD::addColumn(['name' => 'price', 'type' => 'number']);
         */
    }

    public function show($id)
    {
        $invoice = Invoice::findOrFail($id)->load('payments');

        if (! backpack_user()->can('enrollments.edit')) {
            abort(403);
        }

        return view('invoices.show', [
            'invoice' => $invoice,
            'availablePaymentMethods' => Paymentmethod::all(),
            'editable' => false,
        ]);
    }
}
