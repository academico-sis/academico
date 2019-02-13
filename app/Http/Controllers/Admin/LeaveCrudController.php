<?php

namespace App\Http\Controllers\Admin;

use Backpack\CRUD\app\Http\Controllers\CrudController;

// VALIDATION: change the requests to match your own file names if you need form validation
use App\Http\Requests\LeaveRequest as StoreRequest;
use App\Http\Requests\LeaveRequest as UpdateRequest;

/**
 * Class LeaveCrudController
 * @package App\Http\Controllers\Admin
 * @property-read CrudPanel $crud
 */
class LeaveCrudController extends CrudController
{
    public function setup()
    {
        /*
        |--------------------------------------------------------------------------
        | CrudPanel Basic Information
        |--------------------------------------------------------------------------
        */
        $this->crud->setModel('App\Models\Leave');
        $this->crud->setRoute(config('backpack.base.route_prefix') . '/leave');
        $this->crud->setEntityNameStrings('leave', 'leaves');

        /*
        |--------------------------------------------------------------------------
        | CrudPanel Configuration
        |--------------------------------------------------------------------------
        */

        $this->crud->setColumns([
            [
                // 1-n relationship
                'label' => "Teacher", // Table column heading
                'type' => "select",
                'name' => 'teacher_id', // the column that contains the ID of that connected entity;
                'entity' => 'teacher', // the method that defines the relationship in your Model
                'attribute' => "name", // foreign key attribute that is shown to user
                'model' => "App\Models\Teacher", // foreign key model
             ],

             [
                // 1-n relationship
                'label' => "Type", // Table column heading
                'type' => "select",
                'name' => 'leave_type_id', // the column that contains the ID of that connected entity;
                'entity' => 'leaveType', // the method that defines the relationship in your Model
                'attribute' => "name", // foreign key attribute that is shown to user
                'model' => "App\Models\LeaveType", // foreign key model
             ],

             [
                'name' => "date", // The db column name
                'label' => "Date", // Table column heading
                'type' => "date",
             ],
        ]);


        $this->crud->addFilter([ // select2 filter
            'name' => 'teacher_id',
            'type' => 'select2',
            'label'=> __('Teacher')
          ], function() {
              return \App\Models\Teacher::all()->pluck('name', 'id')->toArray();
          }, function($value) { // if the filter is active
                  $this->crud->addClause('where', 'teacher_id', $value);
          },
          function () { // if the filter is NOT active (the GET parameter "checkbox" does not exit)
            
        });


        $this->crud->addFilter([ // daterange filter
            'type' => 'date_range',
            'name' => 'from_to',
            'label'=> __('Date range')
          ],
          false,
          function($value) { // if the filter is active, apply these constraints
            $dates = json_decode($value);
            $this->crud->addClause('where', 'date', '>=', $dates->from);
            $this->crud->addClause('where', 'date', '<=', $dates->to . ' 23:59:59');
          });



        $this->crud->addFields([
            [
                // 1-n relationship
                'label' => "Teacher", // Table column heading
                'type' => "select",
                'name' => 'teacher_id', // the column that contains the ID of that connected entity;
                'entity' => 'teacher', // the method that defines the relationship in your Model
                'attribute' => "name", // foreign key attribute that is shown to user
                'model' => "App\Models\Teacher", // foreign key model
             ],

             [
                // 1-n relationship
                'label' => "Type", // Table column heading
                'type' => "select",
                'name' => 'leave_type_id', // the column that contains the ID of that connected entity;
                'entity' => 'leaveType', // the method that defines the relationship in your Model
                'attribute' => "name", // foreign key attribute that is shown to user
                'model' => "App\Models\LeaveType", // foreign key model
             ],

             [
                'name' => "date", // The db column name
                'label' => "Date", // Table column heading
                'type' => "date",
             ],
        ]);

        // add asterisk for fields that are required in LeaveRequest
        $this->crud->setRequiredFields(StoreRequest::class, 'create');
        $this->crud->setRequiredFields(UpdateRequest::class, 'edit');
    }

    public function store(StoreRequest $request)
    {
        // your additional operations before save here
        $redirect_location = parent::storeCrud($request);
        // your additional operations after save here
        // use $this->data['entry'] or $this->crud->entry
        return $redirect_location;
    }

    public function update(UpdateRequest $request)
    {
        // your additional operations before save here
        $redirect_location = parent::updateCrud($request);
        // your additional operations after save here
        // use $this->data['entry'] or $this->crud->entry
        return $redirect_location;
    }
}
