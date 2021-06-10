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
    use \Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;

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
        CRUD::column('invoice_number');
        CRUD::addColumn([
            'name'         => 'invoiceType', // name of relationship method in the model
            'type'         => 'relationship',
            'label'        => 'Type',
            'searchLogic'  => false,
            'attribute'    => 'name',
        ]);
        CRUD::column('client_name');
        CRUD::column('client_idnumber');
        CRUD::column('client_address');
        CRUD::column('client_email');
        CRUD::column('total_price');
        CRUD::column('created_at');
    }

    protected function setupUpdateOperation()
    {
        CRUD::setValidation(InvoiceRequest::class);
        CRUD::field('invoice_number');
        CRUD::addField([
            'name'         => 'invoiceType', // name of relationship method in the model
            'type'         => 'relationship',
            'label'        => 'Type',
            'searchLogic'  => false,
            'attribute'    => 'name',
        ]);
        CRUD::field('client_name');
        CRUD::field('client_idnumber');
        CRUD::field('client_address');
        CRUD::field('client_email');
        CRUD::field('total_price');
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
