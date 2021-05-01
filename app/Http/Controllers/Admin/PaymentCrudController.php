<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\PaymentRequest;
use App\Models\Payment;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;
use Illuminate\Support\Carbon;

/**
 * Class PaymentCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class PaymentCrudController extends CrudController
{
    use \Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation;

    /**
     * Configure the CrudPanel object. Apply settings to all operations.
     *
     * @return void
     */
    public function setup()
    {
        CRUD::setModel(\App\Models\Payment::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/payment');
        CRUD::setEntityNameStrings('payment', 'payments');

        $this->crud->enableExportButtons();
    }

    /**
     * Define what happens when the List operation is loaded.
     *
     * @see  https://backpackforlaravel.com/docs/crud-operation-list-entries
     * @return void
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

        CRUD::column('status');

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

    /**
     * Define what happens when the Create operation is loaded.
     *
     * @see https://backpackforlaravel.com/docs/crud-operation-create
     * @return void
     */
    protected function setupCreateOperation()
    {
        CRUD::setValidation(PaymentRequest::class);

        CRUD::field('id');
        CRUD::field('responsable_id');
        CRUD::field('invoice_id');
        CRUD::field('payment_method');
        CRUD::field('date');
        CRUD::field('value');
        CRUD::field('status');
        CRUD::field('comment');
        CRUD::field('created_at');
        CRUD::field('updated_at');

        /**
         * Fields can be defined using the fluent syntax or array syntax:
         * - CRUD::field('price')->type('number');
         * - CRUD::addField(['name' => 'price', 'type' => 'number']));
         */
    }

    /**
     * Define what happens when the Update operation is loaded.
     *
     * @see https://backpackforlaravel.com/docs/crud-operation-update
     * @return void
     */
    protected function setupUpdateOperation()
    {
        $this->setupCreateOperation();
    }
}
