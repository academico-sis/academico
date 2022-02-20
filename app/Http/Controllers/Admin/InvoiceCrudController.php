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
use Carbon\Carbon;

class InvoiceCrudController extends CrudController
{
    use ListOperation;
    use UpdateOperation { update as traitUpdate; }
    use DeleteOperation;
    use ShowOperation { show as traitShow; }

    private array $currency;

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

        if (config('academico.currency_position') === 'before') {
            $this->currency = ['prefix' => config('academico.currency_symbol')];
        } else {
            $this->currency = ['suffix' => config('academico.currency_symbol')];
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
                'attribute' => 'name',
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

        CRUD::addFilter(
            [
                'type' => 'date_range',
                'name' => 'from_to',
                'label' => __('Date range'),
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
                'attribute' => 'name',
                'tab' => __('Invoice'),
            ]);
        }

        CRUD::field('client_name')->label(__('Client name'))->tab(__('Invoice'));
        CRUD::field('client_idnumber')->label(__('Client ID Number'))->tab(__('Invoice'));
        CRUD::field('client_address')->label(__('Client address'))->tab(__('Invoice'));
        CRUD::field('client_email')->label(__('Client email'))->tab(__('Invoice'));

        CRUD::addField([
            'tab' => __('Products'),
            'name' => 'alert',
            'type' => 'view',
            'view' => 'invoices/invoice-editing-alert',
        ]);

        CRUD::addField([
            'name' => 'invoiceDetails',
            'label' => __('Products'),
            'type' => 'repeatable',
            'subfields' => [
                [
                    'name' => 'id',
                    'type' => 'hidden',
                ],
                [
                    'name' => 'product_name',
                    'type' => 'text',
                    'label' => __('Product'),
                    'wrapper' => ['class' => 'form-group col-md-8'],
                ],
                [
                    'name' => 'quantity',
                    'type' => 'number',
                    'label' => __('Quantity'),
                    'attributes' => ['step' => '0.01', 'min' => 1],
                    'wrapper' => ['class' => 'form-group col-md-2'],
                ],
                array_merge([
                    'name' => 'price',
                    'type' => 'number',
                    'attributes' => ['step' => '0.01'],
                    'label' => __('Price'),
                    'wrapper' => ['class' => 'form-group col-md-2'],
                ], $this->currency),
            ],
            'tab' => __('Products'),
            'init_rows' => 0,
        ]);

        CRUD::addField([
            'name' => 'payments',
            'label' => __('Payments'),
            'type' => 'repeatable',
            'subfields' => [
                [
                    'name' => 'id',
                    'type' => 'hidden',
                ],
                [
                    'name' => 'payment_method',
                    'label' => __('Payment method'),
                    'type'        => 'select2_from_array',
                    'options'     => Paymentmethod::all()->pluck('name', 'code')->toArray(),
                    'allows_null' => false,
                    'wrapper' => ['class' => 'form-group col-md-6'],
                ],
                [
                    'name' => 'date',
                    'type' => 'date',
                    'label' => __('Date'),
                    'wrapper' => ['class' => 'form-group col-md-3'],
                ],
                array_merge([
                    'name' => 'value',
                    'type' => 'number',
                    'attributes' => ['step' => '0.01'],
                    'label' => __('Value'),
                    'wrapper' => ['class' => 'form-group col-md-3'],
                ], $this->currency),
                [
                    'name' => 'comment',
                    'type' => 'text',
                    'label' => __('Comment'),
                    'wrapper' => ['class' => 'form-group col-md-12'],
                ],
            ],
            'tab' => __('Payments'),
            'init_rows' => 0,
        ]);
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

    public function update()
    {
        // update model an run validator
        $response = $this->traitUpdate();

        /** @var Invoice $invoice */
        $invoice = $this->crud->getCurrentEntry();
        $newInvoiceDetails = collect(json_decode($this->crud->getRequest()->input('invoiceDetails'), null, 512, JSON_THROW_ON_ERROR));
        $newPaymentsList = collect(json_decode($this->crud->getRequest()->input('payments'), null, 512, JSON_THROW_ON_ERROR));

        $this->saveInvoiceDetails($invoice, $newInvoiceDetails);
        $this->savePayments($invoice, $newPaymentsList);

        return $response;
    }

    private function saveInvoiceDetails(Invoice $invoice, $newInvoiceDetails)
    {
        // delete entries that have been removed.
        foreach ($invoice->invoiceDetails as $oldInvoiceDetail) {
            if ($newInvoiceDetails->where('id', $oldInvoiceDetail->id)->count() === 0) {
                $oldInvoiceDetail->delete();
            }
        }

        // sync new entries
        foreach ($newInvoiceDetails as $invoiceDetail) {
            $invoice->invoiceDetails()->updateOrCreate(
                ['id' => $invoiceDetail->id],
                [
                    'product_name' => $invoiceDetail->product_name,
                    'price' => $invoiceDetail->price,
                    'quantity' => $invoiceDetail->quantity,
                ]
            );
        }
    }

    private function savePayments(Invoice $invoice, $newPayments)
    {
        // delete entries that have been removed.
        foreach ($invoice->payments as $oldPayment) {
            if ($newPayments->where('id', $oldPayment->id)->count() === 0) {
                $oldPayment->delete();
            }
        }

        // sync new entries
        foreach ($newPayments as $payment) {
            $invoice->payments()->updateOrCreate(
                ['id' => $payment->id],
                [
                    'payment_method' => $payment->payment_method,
                    'date' => $payment->date,
                    'value' => $payment->value,
                    'comment' => $payment->comment,
                    'responsable_id' => backpack_user()->id,
                ]
            );
        }
    }
}
