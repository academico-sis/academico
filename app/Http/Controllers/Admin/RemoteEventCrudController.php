<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\RemoteEventRequest as StoreRequest;
use App\Http\Requests\RemoteEventRequest as UpdateRequest;
// VALIDATION: change the requests to match your own file names if you need form validation
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

/**
 * Class RemoteEventCrudController.
 * @property-read CrudPanel $crud
 */
class RemoteEventCrudController extends CrudController
{
    use \Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;

    public function __construct()
    {
        parent::__construct();
        $this->middleware('permission:courses.edit');
    }

    public function setup()
    {
        /*
        |--------------------------------------------------------------------------
        | CrudPanel Basic Information
        |--------------------------------------------------------------------------
        */
        CRUD::setModel('App\Models\RemoteEvent');
        CRUD::setRoute(config('backpack.base.route_prefix').'/remoteevent');
        CRUD::setEntityNameStrings('remoteevent', 'remote_events');
    }
        /*
        |--------------------------------------------------------------------------
        | CrudPanel Configuration
        |--------------------------------------------------------------------------
        */
        public function setupListOperation()
        {
        CRUD::setColumns([
            [
                // 1-n relationship
                'label' => 'Period', // Table column heading
                'type' => 'select',
                'name' => 'period_id', // the column that contains the ID of that connected entity;
                'entity' => 'period', // the method that defines the relationship in your Model
                'attribute' => 'name', // foreign key attribute that is shown to user
                'model' => "App\Models\Period", // foreign key model
             ],

             [
                // 1-n relationship
                'label' => 'Teacher', // Table column heading
                'type' => 'select',
                'entity' => 'teacher', // the method that defines the relationship in your Model
                'attribute' => 'name', // foreign key attribute that is shown to user
             ],

             [
                'name' => 'worked_hours', // The db column name
                'label' => 'Worked Hours', // Table column heading
                'type' => 'number',
                'suffix' => ' h',
                'decimals' => 2,
             ],

             [
                'name' => 'name', // The db column name
                'label' => 'Project', // Table column heading
             ],

        ]);
        }

        public function setupCreateOperation()
        {
        CRUD::addFields([
            [
                // 1-n relationship
                'label' => 'Period', // Table column heading
                'type' => 'select',
                'name' => 'period_id', // the column that contains the ID of that connected entity;
                'entity' => 'period', // the method that defines the relationship in your Model
                'attribute' => 'name', // foreign key attribute that is shown to user
                'model' => "App\Models\Period", // foreign key model
             ],

             [
                // 1-n relationship
                'label' => 'Teacher', // Table column heading
                'type' => 'select',
                'name' => 'teacher_id', // the column that contains the ID of that connected entity;
                'entity' => 'teacher', // the method that defines the relationship in your Model
                'attribute' => 'name', // foreign key attribute that is shown to user
                'model' => "App\Models\Teacher", // foreign key model
             ],

             [
                'name' => 'worked_hours', // The db column name
                'label' => 'Worked Hours', // Table column heading
                'type' => 'number',
                'suffix' => ' h',
                'decimals' => 2,
             ],

             [
                'name' => 'name', // The db column name
                'label' => 'Project', // Table column heading
             ],

        ]);
        // add asterisk for fields that are required in RemoteEventRequest
        CRUD::setRequiredFields(StoreRequest::class, 'create');
        }

    public function setupUpdateOperation()
    {
       $this->setupCreateOperation(); // if it's the same as Create
    }
}
