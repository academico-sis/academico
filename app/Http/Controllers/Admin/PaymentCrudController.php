<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\PaymentRequest;
use App\Models\Payment;
use App\Models\Paymentmethod;
use App\Models\Scholarship;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
use Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;
use Illuminate\Support\Carbon;

/**
 * Class PaymentCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class PaymentCrudController extends CrudController
{
    use ListOperation;
    use ShowOperation { show as traitShow; }

    public function __construct()
    {
        parent::__construct();
        $this->middleware('permission:enrollments.edit');
    }

    /**
     * Configure the CrudPanel object. Apply settings to all operations.
     *
     */
    public function setup()
    {
        CRUD::setModel(\App\Models\Payment::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/payment');
        CRUD::setEntityNameStrings('payment', 'payments');

        $this->crud->enableExportButtons();

        $this->crud->addButtonFromView('top', 'createInvoice', 'createInvoice', 'start');
    }

    /**
     * Define what happens when the List operation is loaded.
     */
    protected function setupListOperation()
    {
        $this->crud->addFilter([
            'type'  => 'date',
            'name'  => 'date',
            'label' => __('Due Date'),
        ],
            false,
            function ($value) { // if the filter is active, apply these constraints
                $this->crud->addClause('where', 'date', '>=', Carbon::parse($value)->firstOfMonth());
                $this->crud->addClause('where', 'date', '<=', Carbon::parse($value)->lastOfMonth());
            });

        CRUD::column('month');

        if (config('app.currency_position' === 'before')) {
            $currency = array('prefix' => config('app.currency_symbol'));
        } else {
            $currency = array('suffix' => config('app.currency_symbol'));
        }

        CRUD::addColumn([
            'name' => 'enrollment_name',
            'type' => 'attribute',
            'label' => __('Enrollment'),
        ]);

        CRUD::addColumn(array_merge([
            'name'  => 'value',
            'label' => __('Value'),
            'type'  => 'number'], $currency));

        CRUD::addColumn([
            'name' => 'display_status',
            'type' => 'attribute',
            'label' => __('Status'),
        ]);
        CRUD::addColumn([
            'name' => 'iban',
            'type' => 'attribute',
            'label' => 'IBAN',
        ]);

        CRUD::addColumn([
            'name' => 'bic',
            'type' => 'attribute',
            'label' => 'BIC',
        ]);
    }

    public function show($id)
    {
        $payment = Payment::findOrFail($id);

        if (! backpack_user()->can('enrollments.edit')) {
            abort(403);
        }

        if (! $payment->invoice || ! $payment->invoice->enrollment)
        {
            abort(404, 'No enrollment found for this payment');
        }

        return view('enrollments.show', [
            'enrollment' => $payment->invoice->enrollment->load('invoice')->load('invoice.payments'),
            'products' => $payment->invoice()->with('invoiceDetails')->get(),
            'comments' => $payment->invoice->enrollment->comments,
            'scholarships' => Scholarship::all(),
            'availablePaymentMethods' => Paymentmethod::all(),
            'writeaccess' => $payment->invoice->enrollment->status_id !== 2 && backpack_user()->can('enrollments.edit'),
        ]);
    }
}
