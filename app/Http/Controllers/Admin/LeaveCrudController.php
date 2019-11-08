<?php

namespace App\Http\Controllers\Admin;

use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

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

    use \Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;

    
    public function setup()
    {
        /*
        |--------------------------------------------------------------------------
        | CrudPanel Basic Information
        |--------------------------------------------------------------------------
        */
        CRUD::setModel('App\Models\Leave');
        CRUD::setRoute(config('backpack.base.route_prefix') . '/leave');
        CRUD::setEntityNameStrings('leave', 'leaves');

        /*
        |--------------------------------------------------------------------------
        | CrudPanel Configuration
        |--------------------------------------------------------------------------
        */

        CRUD::setColumns([
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


        CRUD::addFilter([ // select2 filter
            'name' => 'teacher_id',
            'type' => 'select2',
            'label'=> __('Teacher')
          ], function() {
              return \App\Models\Teacher::all()->pluck('name', 'id')->toArray();
          }, function($value) { // if the filter is active
                  CRUD::addClause('where', 'teacher_id', $value);
          },
          function () { // if the filter is NOT active (the GET parameter "checkbox" does not exit)
            
        });

/* BP4 check if this is working */
        CRUD::addFilter([ // daterange filter
            'type' => 'date_range',
            'name' => 'from_to',
            'label'=> __('Date range')
          ],
          false,
          function($value) { // if the filter is active, apply these constraints
            $dates = json_decode($value);
            CRUD::addClause('where', 'date', '>=', $dates->from);
            CRUD::addClause('where', 'date', '<=', $dates->to . ' 23:59:59');
          });



        CRUD::addFields([
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
                'type' => "date",  // TODO MOVE TO DATERANGE!!!
             ],
        ]);

        // add asterisk for fields that are required in LeaveRequest
        CRUD::setRequiredFields(StoreRequest::class, 'create');
        CRUD::setRequiredFields(UpdateRequest::class, 'edit');
    }

    protected function setupCreateOperation()
    {
        CRUD::setValidation(StoreRequest::class);
    }

    protected function setupUpdateOperation()
    {
        CRUD::setValidation(UpdateRequest::class);
    }
}
