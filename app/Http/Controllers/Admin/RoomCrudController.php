<?php

namespace App\Http\Controllers\Admin;

use Backpack\CRUD\app\Http\Controllers\CrudController;

// VALIDATION: change the requests to match your own file names if you need form validation
use App\Http\Requests\RoomRequest as StoreRequest;
use App\Http\Requests\RoomRequest as UpdateRequest;

/**
 * Class RoomCrudController
 * @package App\Http\Controllers\Admin
 * @property-read CrudPanel $crud
 */
class RoomCrudController extends CrudController
{

    public function __construct()
    {
        parent::__construct();
        $this->middleware(['role:admin']);
    }


    public function setup()
    {
        /*
        |--------------------------------------------------------------------------
        | CrudPanel Basic Information
        |--------------------------------------------------------------------------
        */
        $this->crud->setModel('App\Models\Room');
        $this->crud->setRoute(config('backpack.base.route_prefix') . '/room');
        $this->crud->setEntityNameStrings('room', 'rooms');

        /*
        |--------------------------------------------------------------------------
        | CrudPanel Configuration
        |--------------------------------------------------------------------------
        */

        $this->crud->setColumns([
            [
                // 1-n relationship
                'label' => "Campus",
                'type' => "select",
                'entity' => 'campus',
                'attribute' => "name",
             ],

             [
                'name' => "name", // The db column name
                'label' => "Name", // Table column heading
                'type' => "text",
             ],

             [
                'name' => "capacity", // The db column name
                'label' => "Capacity", // Table column heading
                'type' => "number",
             ],
        ]);

        // add asterisk for fields that are required in RoomRequest
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
