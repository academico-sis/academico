<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\ScheduledPaymentRequest;
use App\Models\EnrollmentStatusType;
use App\Models\Invoice;
use App\Models\Paymentmethod;
use App\Models\ScheduledPayment;
use App\Models\Teacher;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

/**
 * Class ScheduledPaymentCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class ScheduledPaymentCrudController extends CrudController
{
    use \Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;

    /**
     * Configure the CrudPanel object. Apply settings to all operations.
     *
     * @return void
     */
    public function setup()
    {
        CRUD::setModel(\App\Models\ScheduledPayment::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/scheduled-payment');
        CRUD::setEntityNameStrings(__('scheduled payment'), __('scheduled payments'));
        CRUD::enableExportButtons();
    }

    /**
     * Define what happens when the List operation is loaded.
     *
     * @see  https://backpackforlaravel.com/docs/crud-operation-list-entries
     * @return void
     */
    protected function setupListOperation()
    {
        CRUD::addColumn([
            'name' => 'enrollment.student',
            'key'       => 'student_lastname',
            'attribute' => 'lastname',
            'label' => __('Last Name'),
            'type' => 'relationship',
            'searchLogic' => function ($query, $column, $searchTerm) {
                $query->orWhereHas('enrollment', function ($q) use ($searchTerm) {
                    $q->whereHas('user', function ($q) use ($searchTerm) {
                        $q->where('lastname', 'like', '%'.$searchTerm.'%');
                    });
                });
            },
        ]);

        CRUD::addColumn([
            'name' => 'enrollment.student',
            'key'       => 'student_firstname',
            'attribute' => 'firstname',
            'label' => __('First Name'),
            'type' => 'relationship',
            'searchLogic' => function ($query, $column, $searchTerm) {
                $query->orWhereHas('enrollment', function ($q) use ($searchTerm) {
                    $q->whereHas('user', function ($q) use ($searchTerm) {
                        $q->where('firstname', 'like', '%'.$searchTerm.'%');
                    });
                });
            },
        ]);


        CRUD::addColumn([
            'name' => 'enrollment.student',
            'key'       => 'student_email',
            'attribute' => 'email',
            'label' => __('Email'),
            'type' => 'relationship',
            'searchLogic' => function ($query, $column, $searchTerm) {
                $query->orWhereHas('enrollment', function ($q) use ($searchTerm) {
                    $q->whereHas('user', function ($q) use ($searchTerm) {
                        $q->where('email', 'like', '%'.$searchTerm.'%');
                    });
                });
            },
        ]);

        if (config('app.currency_position') === 'before') {
            $currency = array('prefix' => config('app.currency_symbol'));
        } else {
            $currency = array('suffix' => config('app.currency_symbol'));
        }

        CRUD::addColumn(array_merge([
            'name'  => 'value',
            'label' => __('Value'),
            'type'  => 'number'], $currency));

        CRUD::addColumn([
            'name' => 'status_name',
            'type' => 'model_function',
            'function_name' => 'getStatusTypeNameAttribute',
        ]);

        CRUD::column('date');

        CRUD::addFilter([
            'name' => 'status_id',
            'type' => 'select2',
            'label'=> __('Status'),
        ], function () {
            return EnrollmentStatusType::all()->pluck('name', 'id')->toArray();
        },
            function ($value) { // if the filter is active
                CRUD::addClause('where', 'status', $value);
            });
    }

    /**
     * Define what happens when the Create operation is loaded.
     *
     * @see https://backpackforlaravel.com/docs/crud-operation-create
     * @return void
     */
    protected function setupCreateOperation()
    {
        CRUD::setValidation(ScheduledPaymentRequest::class);

        CRUD::field('value');
        CRUD::field('date');

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
