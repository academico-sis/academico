<?php

namespace App\Http\Controllers\Admin;

use Backpack\CRUD\app\Http\Controllers\CrudController;

// VALIDATION: change the requests to match your own file names if you need form validation
use App\Http\Requests\RemoteEventRequest as StoreRequest;
use App\Http\Requests\RemoteEventRequest as UpdateRequest;

/**
 * Class RemoteEventCrudController
 * @package App\Http\Controllers\Admin
 * @property-read CrudPanel $crud
 */
class RemoteEventCrudController extends CrudController
{
    public function setup()
    {
        /*
        |--------------------------------------------------------------------------
        | CrudPanel Basic Information
        |--------------------------------------------------------------------------
        */
        $this->crud->setModel('App\Models\RemoteEvent');
        $this->crud->setRoute(config('backpack.base.route_prefix') . '/remoteevent');
        $this->crud->setEntityNameStrings('remoteevent', 'remote_events');

        /*
        |--------------------------------------------------------------------------
        | CrudPanel Configuration
        |--------------------------------------------------------------------------
        */

        $this->crud->setColumns([
            [
                // 1-n relationship
                'label' => "Period", // Table column heading
                'type' => "select",
                'name' => 'period_id', // the column that contains the ID of that connected entity;
                'entity' => 'period', // the method that defines the relationship in your Model
                'attribute' => "name", // foreign key attribute that is shown to user
                'model' => "App\Models\Period", // foreign key model
             ],

             [
                // 1-n relationship
                'label' => "Teacher", // Table column heading
                'type' => "select",
                'name' => 'user_id', // the column that contains the ID of that connected entity;
                'entity' => 'user', // the method that defines the relationship in your Model
                'attribute' => "name", // foreign key attribute that is shown to user
                'model' => "App\Models\User", // foreign key model
             ],

             [
                'name' => 'worked_hours', // The db column name
                'label' => "Worked Hours", // Table column heading
                'type' => "number",
                'suffix' => " h",
                'decimals' => 2,
             ],

             [
                'name' => 'name', // The db column name
                'label' => "Project", // Table column heading
             ],

        ]);


        $this->crud->addFields([
            [
                // 1-n relationship
                'label' => "Period", // Table column heading
                'type' => "select",
                'name' => 'period_id', // the column that contains the ID of that connected entity;
                'entity' => 'period', // the method that defines the relationship in your Model
                'attribute' => "name", // foreign key attribute that is shown to user
                'model' => "App\Models\Period", // foreign key model
             ],

             [
                // 1-n relationship
                'label' => "Teacher", // Table column heading
                'type' => "select",
                'name' => 'user_id', // the column that contains the ID of that connected entity;
                'entity' => 'user', // the method that defines the relationship in your Model
                'attribute' => "name", // foreign key attribute that is shown to user
                'model' => "App\Models\User", // foreign key model
             ],

             [
                'name' => 'worked_hours', // The db column name
                'label' => "Worked Hours", // Table column heading
                'type' => "number",
                'suffix' => " h",
                'decimals' => 2,
             ],

             [
                'name' => 'name', // The db column name
                'label' => "Project", // Table column heading
             ],

        ]);
        // add asterisk for fields that are required in RemoteEventRequest
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
