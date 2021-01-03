<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\LeaveRequest as StoreRequest;
use App\Http\Requests\LeaveUpdateRequest as UpdateRequest;
// VALIDATION: change the requests to match your own file names if you need form validation
use App\Models\Leave;
use App\Models\LeaveType;
use App\Models\Teacher;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
use Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;
use Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
use Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;
use Carbon\Carbon;

class LeaveCrudController extends CrudController
{
    use ListOperation;
    use CreateOperation;
    use UpdateOperation;
    use DeleteOperation;

    public function setup()
    {
        /*
        |--------------------------------------------------------------------------
        | CrudPanel Basic Information
        |--------------------------------------------------------------------------
        */
        CRUD::setModel(Leave::class);
        CRUD::setRoute(config('backpack.base.route_prefix').'/leave');
        CRUD::setEntityNameStrings(__('leave'), __('leaves'));

        /*
        |--------------------------------------------------------------------------
        | CrudPanel Configuration
        |--------------------------------------------------------------------------
        */

        CRUD::setColumns([
            [
                // 1-n relationship
                'label' => 'Teacher', // Table column heading
                'type' => 'select',
                'name' => 'teacher_id', // the column that contains the ID of that connected entity;
                'entity' => 'teacher', // the method that defines the relationship in your Model
                'attribute' => 'name', // foreign key attribute that is shown to user
                'model' => Teacher::class, // foreign key model
            ],

            [
                // 1-n relationship
                'label' => 'Type', // Table column heading
                'type' => 'select',
                'name' => 'leave_type_id', // the column that contains the ID of that connected entity;
                'entity' => 'leaveType', // the method that defines the relationship in your Model
                'attribute' => 'name', // foreign key attribute that is shown to user
                'model' => LeaveType::class, // foreign key model
            ],

            [
                'name' => 'date', // The db column name
                'label' => 'Date', // Table column heading
                'type' => 'date',
            ],
        ]);

        CRUD::addFilter([ // select2 filter
            'name' => 'teacher_id',
            'type' => 'select2',
            'label'=> __('Teacher'),
        ], function () {
            return Teacher::all()->pluck('name', 'id')->toArray();
        }, function ($value) { // if the filter is active
            CRUD::addClause('where', 'teacher_id', $value);
        },
          function () { // if the filter is NOT active (the GET parameter "checkbox" does not exit)
          });

        CRUD::addFilter([ // daterange filter
            'type' => 'date_range',
            'name' => 'from_to',
            'label'=> __('Date range'),
        ],
          false,
          function ($value) { // if the filter is active, apply these constraints
              $dates = json_decode($value);
              CRUD::addClause('where', 'date', '>=', $dates->from);
              CRUD::addClause('where', 'date', '<=', $dates->to.' 23:59:59');
          });
    }

    protected function setupCreateOperation()
    {
        CRUD::setValidation(StoreRequest::class);

        CRUD::addFields([
            [
                // 1-n relationship
                'label' => 'Teacher', // Table column heading
                'type' => 'select_multiple',
                'name' => 'teacher_id', // the column that contains the ID of that connected entity;
                'entity' => 'teacher', // the method that defines the relationship in your Model
                'attribute' => 'name', // foreign key attribute that is shown to user
                'model' => Teacher::class, // foreign key model
            ],

            [
                // 1-n relationship
                'label' => 'Type', // Table column heading
                'type' => 'select',
                'name' => 'leave_type_id', // the column that contains the ID of that connected entity;
                'entity' => 'leaveType', // the method that defines the relationship in your Model
                'attribute' => 'name', // foreign key attribute that is shown to user
                'model' => LeaveType::class, // foreign key model
            ],

            [   // date_range
                'name' => ['start_date', 'end_date'], // db columns for start_date & end_date
                'label' => 'Event Date Range',
                'type' => 'date_range',
                'default' => [Carbon::now()->format('Y-m-d 00:00'), Carbon::now()->addDays(2)->format('Y-m-d 00:00')], // default value for start_date and end_date
            ],
        ]);
    }

    public function store(StoreRequest $request)
    {
        foreach ($request->teacher_id as $teacher_id) {
            $start = Carbon::parse($request->start_date);
            $end = Carbon::parse($request->end_date);

            while ($start <= $end) {
                Leave::create([
                    'teacher_id' => $teacher_id,
                    'date' => $start,
                    'leave_type_id' => $request->leave_type_id,
                ]);

                $start->addDay();
            }
        }

        return redirect()->to('/leave/teachers');
    }

    protected function setupUpdateOperation()
    {
        CRUD::setValidation(UpdateRequest::class);

        CRUD::addFields([
            [
                // 1-n relationship
                'label' => 'Type', // Table column heading
                'type' => 'select',
                'name' => 'leave_type_id', // the column that contains the ID of that connected entity;
                'entity' => 'leaveType', // the method that defines the relationship in your Model
                'attribute' => 'name', // foreign key attribute that is shown to user
                'model' => LeaveType::class, // foreign key model
            ],

            [   // datepicker
                'name' => 'date',
                'label' => 'Event Date',
                'type' => 'date',
            ],
        ]);
    }
}
