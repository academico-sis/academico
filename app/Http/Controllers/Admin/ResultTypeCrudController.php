<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\ResultTypeRequest as StoreRequest;
use App\Http\Requests\ResultTypeRequest as UpdateRequest;
// VALIDATION: change the requests to match your own file names if you need form validation
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

/**
 * Class ResultTypeCrudController.
 * @property-read CrudPanel $crud
 */
class ResultTypeCrudController extends CrudController
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
        CRUD::setModel(\App\Models\ResultType::class);
        CRUD::setRoute(config('backpack.base.route_prefix').'/resulttype');
        CRUD::setEntityNameStrings('resulttype', 'result_types');

        /*
        |--------------------------------------------------------------------------
        | CrudPanel Configuration
        |--------------------------------------------------------------------------
        */

        CRUD::addColumns([
            ['name' => 'name', 'label' => 'Name'],
            ['name' => 'description', 'label' => 'Description']
        ]);

        CRUD::addFields([
            ['name' => 'name', 'label' => 'Name', 'type' => 'textarea'],
            ['name' => 'description', 'label' => 'Description', 'type' => 'textarea']
        ]);

        // add asterisk for fields that are required in ResultTypeRequest
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
