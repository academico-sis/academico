<?php

namespace App\Http\Controllers\Admin;

use Backpack\CRUD\app\Http\Controllers\CrudController;

// VALIDATION: change the requests to match your own file names if you need form validation
use App\Http\Requests\EventRequest as StoreRequest;
use App\Http\Requests\EventRequest as UpdateRequest;

/**
 * Class EventCrudController
 * @package App\Http\Controllers\Admin
 * @property-read CrudPanel $crud
 */
class EventCrudController extends CrudController
{
    public function __construct()
    {
        parent::__construct();
        $this->middleware(['permission:courses.view']);
    }

    public function setup()
    {
        /*
        |--------------------------------------------------------------------------
        | CrudPanel Basic Information
        |--------------------------------------------------------------------------
        */
        $this->crud->setModel('App\Models\Event');
        $this->crud->setRoute(config('backpack.base.route_prefix') . '/event');
        $this->crud->setEntityNameStrings('event', 'events');

        /*
        |--------------------------------------------------------------------------
        | CrudPanel Configuration
        |--------------------------------------------------------------------------
        */

        $this->crud->setColumns([
            
            [
            'name' => 'name', // The db column name
            'label' => "Name", // Table column heading
            'type' => 'text'
            ],

            [
            'name' => "volume",
            'label' => "Volume", // Table column heading
            'type' => "model_function",
            'function_name' => 'getVolumeAttribute', // the method in your Model
            'suffix' => "h",
            ],

            [
            // TEACHER
            'label' => "Teacher", // Table column heading
            'type' => "select",
            'name' => 'teacher_id', // the column that contains the ID of that connected entity;
            'entity' => 'teacher', // the method that defines the relationship in your Model
            'attribute' => "name", // foreign key attribute that is shown to user
            'model' => "App\Models\User", // foreign key model
            ],

            [
            // ROOM
            'label' => "Room", // Table column heading
            'type' => "select",
            'name' => 'room_id', // the column that contains the ID of that connected entity;
            'entity' => 'room', // the method that defines the relationship in your Model
            'attribute' => "name", // foreign key attribute that is shown to user
            'model' => "App\Models\Room", // foreign key model
            ],


            [
            'name' => "start", // The db column name
            'label' => "Start Date", // Table column heading
            'type' => "datetime",
                // 'format' => 'l j F Y', // use something else than the base.defauormat config value
            ],

            [
            'name' => "end", // The db column name
            'label' => "End Date", // Table column heading
            'type' => "datetime",
                // 'format' => 'l j F Y', // use something else than the base.default_date_format config value
            ],
             
        ]);


        $this->crud->addFields([
            
            [
            'name' => 'name', // The db column name
            'label' => "Name", // Table column heading
            'type' => 'text'
            ],

            [
            // TEACHER
            'label' => "Teacher", // Table column heading
            'type' => "select",
            'name' => 'teacher_id', // the column that contains the ID of that connected entity;
            'entity' => 'teacher', // the method that defines the relationship in your Model
            'attribute' => "name", // foreign key attribute that is shown to user
            'model' => "App\Models\User", // foreign key model
            'options'   => (function ($query) {
                return $query->teacher();
            }),
            ],

            [
            // ROOM
            'label' => "Room", // Table column heading
            'type' => "select",
            'name' => 'room_id', // the column that contains the ID of that connected entity;
            'entity' => 'room', // the method that defines the relationship in your Model
            'attribute' => "name", // foreign key attribute that is shown to user
            'model' => "App\Models\Room", // foreign key model
            ],


            [
            'name' => "start", // The db column name
            'label' => "Start Date", // Table column heading
            'type' => "datetime",
            ],

            [
            'name' => "end", // The db column name
            'label' => "End Date", // Table column heading
            'type' => "datetime",
            ],
             
        ]);

        // add asterisk for fields that are required in EventRequest
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
