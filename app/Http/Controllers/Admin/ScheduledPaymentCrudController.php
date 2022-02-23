<?php

namespace App\Http\Controllers\Admin;

use App\Models\ScheduledPayment;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;
use Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
use Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

class ScheduledPaymentCrudController extends CrudController
{
    use ListOperation;
    use UpdateOperation;
    use DeleteOperation;

    public function setup()
    {
        CRUD::setModel(ScheduledPayment::class);
        CRUD::setRoute(config('backpack.base.route_prefix').'/scheduled-payment');
        CRUD::setEntityNameStrings(__('scheduled payment'), __('scheduled payments'));
        CRUD::enableExportButtons();
    }

    protected function setupListOperation()
    {
        CRUD::addColumn([
            'name' => 'enrollment.student',
            'key' => 'student_lastname',
            'attribute' => 'lastname',
            'label' => __('Last Name'),
            'type' => 'select',
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
            'key' => 'student_firstname',
            'attribute' => 'firstname',
            'label' => __('First Name'),
            'type' => 'select',
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
            'key' => 'student_email',
            'attribute' => 'email',
            'label' => __('Email'),
            'type' => 'select',
            'searchLogic' => function ($query, $column, $searchTerm) {
                $query->orWhereHas('enrollment', function ($q) use ($searchTerm) {
                    $q->whereHas('user', function ($q) use ($searchTerm) {
                        $q->where('email', 'like', '%'.$searchTerm.'%');
                    });
                });
            },
        ]);

        if (config('academico.currency_position') === 'before') {
            $currency = ['prefix' => config('academico.currency_symbol')];
        } else {
            $currency = ['suffix' => config('academico.currency_symbol')];
        }

        CRUD::addColumn(array_merge([
            'name' => 'value',
            'label' => __('Value'),
            'type' => 'number',
        ], $currency));

        CRUD::addColumn([
            'name' => 'status_name',
            'type' => 'model_function',
            'function_name' => 'getStatusTypeNameAttribute',
        ]);

        CRUD::column('date');

        CRUD::addFilter(
            [
                'name' => 'status_id',
                'type' => 'select2',
                'label' => __('Status'),
            ],
            fn () => [
                1 => __('Pending'),
                2 => __('Paid'),
            ],
            function ($value) {
                CRUD::addClause('status', $value);
            }
        );
    }

    protected function setupUpdateOperation()
    {
        CRUD::setValidation([
            'date' => 'required|date',
            'value' => 'required|numeric|min:0',
            'status' => 'required|integer',
            'enrollment_id' => 'required|integer',
        ]);

        CRUD::field('value');
        CRUD::field('date');
    }
}
