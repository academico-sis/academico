<?php

namespace App\Http\Controllers\Admin;

use App\Models\Payment;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
use Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Redirect;

class PaymentCrudController extends CrudController
{
    use ListOperation;
    use ShowOperation { show as traitShow; }

    public function __construct()
    {
        parent::__construct();
        $this->middleware('permission:enrollments.edit');
    }

    public function setup()
    {
        CRUD::setModel(Payment::class);
        CRUD::setRoute(config('backpack.base.route_prefix').'/payment');
        CRUD::setEntityNameStrings('payment', 'payments');

        $this->crud->enableExportButtons();

        $this->crud->addButtonFromView('top', 'createInvoice', 'createInvoice', 'start');
    }

    protected function setupListOperation()
    {
        $this->crud->addFilter(
            [
                'type' => 'date',
                'name' => 'date',
                'label' => __('Due Date'),
            ],
            false,
            function ($value) { // if the filter is active, apply these constraints
                $this->crud->addClause('where', 'date', '>=', Carbon::parse($value)->firstOfMonth());
                $this->crud->addClause('where', 'date', '<=', Carbon::parse($value)->lastOfMonth());
            }
        );

        CRUD::column('month');

        if (config('academico.currency_position') === 'before') {
            $currency = [
                'prefix' => config('academico.currency_symbol'),
                'type' => 'number',
                'decimals' => 2,
                'dec_point' => ',',
            ];
        } else {
            $currency = [
                'suffix' => config('academico.currency_symbol'),
                'type' => 'number',
                'decimals' => 2,
                'dec_point' => ',',
            ];
        }

        CRUD::addColumn([
            'name' => 'enrollment_name',
            'type' => 'attribute',
            'label' => __('Enrollment'),
        ]);

        CRUD::addColumn(array_merge([
            'name' => 'value',
            'label' => __('Value'),
        ], $currency));

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

        if (! $payment->invoice) {
            abort(404, 'No enrollment found for this payment');
        }

        return Redirect::route('invoice.show', ['id' => $payment->invoice_id]);
    }
}
