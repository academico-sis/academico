<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\InvoiceRequest;
use App\Models\Invoice;
use App\Models\Paymentmethod;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;
use Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
use Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation;
use Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

class InvoiceCrudController extends CrudController
{
    use ListOperation;
    use UpdateOperation;
    use DeleteOperation;
    use ShowOperation { show as traitShow; }

    /**
     * Configure the CrudPanel object. Apply settings to all operations.
     *
     * @return void
     */
    public function setup()
    {
        CRUD::setModel(Invoice::class);
        CRUD::setRoute(config('backpack.base.route_prefix').'/invoice');
        CRUD::setEntityNameStrings(__('invoice'), __('invoices'));
        if (! config('invoicing.price_categories_enabled')) {
            $this->crud->addButtonFromView('top', 'createInvoice', 'createInvoice', 'start');
        }
    }

    /**
     * Define what happens when the List operation is loaded.
     *
     * @see  https://backpackforlaravel.com/docs/crud-operation-list-entries
     * @return void
     */
    protected function setupListOperation()
    {
        if (config('app.currency_position') === 'before') {
            $currency = ['prefix' => config('app.currency_symbol')];
        } else {
            $currency = ['suffix' => config('app.currency_symbol')];
        }

        if (config('invoicing.invoice_numbering') === 'manual') {
            CRUD::column('receipt_number');
        } else {
            CRUD::column('invoice_number');

            CRUD::addColumn([
                'name'         => 'invoiceType',
                'type'         => 'relationship',
                'label'        => __('Type'),
                'searchLogic'  => false,
                'attribute'    => 'name',
            ]);
        }

        $this->crud->addColumn([
            'name' => 'created_at',
            'key' => 'year',
            'label' => __('Year'),
            'type' => 'date',
            'format' => 'Y',
        ]);

        $this->crud->addColumn([
            'name' => 'created_at',
            'key' => 'date',
            'label' => __('Date'),
            'type' => 'date',
        ]);

        CRUD::column('client_name')->label(__('Client name'));
        CRUD::column('client_idnumber')->label(__('Client ID Number'));
        CRUD::column('client_address')->label(__('Client address'));
        CRUD::column('client_email')->label(__('Client email'));
        $this->crud->addColumn(
            array_merge([
            'label' => __('Total price'),
            'type'  => 'model_function',
            'function_name' => 'totalPrice',
            ], $currency)
        );

        $this->crud->addColumn(
            array_merge([
                'name' => 'balance',
                'label' => __('Remaining balance'),
                'type' => 'number',
            ], $currency)
        );

        CRUD::addFilter(
            [
                'type' => 'date_range',
                'name' => 'from_to',
                'label'=> __('Date range'),
            ],
            false,
            function ($value) { // if the filter is active, apply these constraints
                $dates = json_decode($value, null, 512, JSON_THROW_ON_ERROR);

                if ($dates->from) {
                    CRUD::addClause('where', 'date', '>=', $dates->from);
                }
                if ($dates->to) {
                    CRUD::addClause('where', 'date', '<=', $dates->to.' 23:59:59');
                }
            }
        );
    }

    protected function setupUpdateOperation()
    {
        CRUD::setValidation(InvoiceRequest::class);

        CRUD::field('date');

        if (config('invoicing.invoice_numbering') === 'manual') {
            CRUD::field('receipt_number');
        } else {
            CRUD::field('invoice_number');

            CRUD::addField([
                'name'         => 'invoiceType',
                'type'         => 'relationship',
                'label'        => 'Type',
                'searchLogic'  => false,
                'attribute'    => 'name',
            ]);
        }

        CRUD::field('client_name');
        CRUD::field('client_idnumber');
        CRUD::field('client_address');
        CRUD::field('client_email');
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
            'editable' => true,
            'afterSuccessUrl' => $invoice->enrollments->count() > 0 ? "/enrollment/{$invoice->enrollments->first()->id}/show" : '/invoice', // TODO fix this, an invoice can theoretically contain several enrollments
        ]);
    }
}
