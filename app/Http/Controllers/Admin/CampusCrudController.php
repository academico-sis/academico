<?php

namespace App\Http\Controllers\Admin;

use Backpack\CRUD\app\Http\Controllers\CrudController;

// VALIDATION: change the requests to match your own file names if you need form validation
use App\Http\Requests\CampusRequest as StoreRequest;
use App\Http\Requests\CampusRequest as UpdateRequest;

/**
 * Class CampusCrudController
 * @package App\Http\Controllers\Admin
 * @property-read CrudPanel $crud
 */
class CampusCrudController extends CrudController
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
        $this->crud->setModel('App\Models\Campus');
        $this->crud->setRoute(config('backpack.base.route_prefix') . '/campus');
        $this->crud->setEntityNameStrings('campus', 'campuses');

        /*
        |--------------------------------------------------------------------------
        | CrudPanel Configuration
        |--------------------------------------------------------------------------
        */

        $this->crud->addColumn(['name' => 'name', 'label' => "Name"]);

        $this->crud->addField(['name' => 'name', 'label' => "Name", 'type' => 'text']);

        // add asterisk for fields that are required in CampusRequest
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
