<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\InvoiceRequest;
use App\Models\Invoice;
use App\Models\InvoiceType;
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

    private array $currency;

    public function setup()
    {
        CRUD::setModel(Invoice::class);
        CRUD::setRoute(config('backpack.base.route_prefix').'/invoice');
        CRUD::setEntityNameStrings(__('invoice'), __('invoices'));
        if (! config('invoicing.price_categories_enabled')) {
            $this->crud->addButtonFromView('top', 'createInvoice', 'createInvoice', 'start');
        }

        if (config('academico.currency_position') === 'before') {
            $this->currency = ['prefix' => config('academico.currency_symbol')];
        } else {
            $this->currency = ['suffix' => config('academico.currency_symbol')];
        }
    }

    protected function setupListOperation()
    {
        if (config('invoicing.invoice_numbering') === 'manual') {
            CRUD::column('receipt_number');
        } else {
            CRUD::column('invoice_number');

            CRUD::addColumn([
                'name' => 'invoiceType',
                'label' => __('Type'),
                'type'      => 'select',
                'entity'    => 'invoiceType',
                'attribute' => 'name',
                'model'     => InvoiceType::class,
                'searchLogic' => false,
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
                'type' => 'model_function',
                'function_name' => 'totalPrice',
            ], $this->currency)
        );

        $this->crud->addColumn(
            array_merge([
                'name' => 'balance',
                'label' => __('Remaining balance'),
                'type' => 'number',
            ], $this->currency)
        );
    }

    protected function setupUpdateOperation()
    {
        CRUD::setValidation(InvoiceRequest::class);

        CRUD::field('date')->label(__('Date'))->tab(__('Invoice'));

        if (config('invoicing.invoice_numbering') === 'manual') {
            CRUD::field('receipt_number')->tab(__('Invoice'));
        } else {
            CRUD::field('invoice_number')->tab(__('Invoice'));

            CRUD::addField([
                'label' => __('Type'),
                'type'      => 'select',
                'name' => 'invoice_type_id',
                'entity'    => 'invoiceType',
                'model'     => InvoiceType::class,
                'attribute' => 'description',
                'searchLogic' => false,
                'tab' => __('Invoice'),
            ]);
        }

        CRUD::field('client_name')->label(__('Client name'))->tab(__('Invoice'));
        CRUD::field('client_idnumber')->label(__('Client ID Number'))->tab(__('Invoice'));
        CRUD::field('client_address')->label(__('Client address'))->tab(__('Invoice'));
        CRUD::field('client_email')->label(__('Client email'))->tab(__('Invoice'));
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
            'enrollment' => $invoice->enrollments->first()?->product,
            'comments' => $invoice->comments,
            'afterSuccessUrl' => $invoice->enrollments->count() > 0 ? "/enrollment/{$invoice->enrollments->first()->product_id}/show" : '/invoice', // TODO fix this, an invoice can theoretically contain several enrollments
        ]);
    }
}
