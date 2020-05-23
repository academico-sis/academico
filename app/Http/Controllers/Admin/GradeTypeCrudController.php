<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\GradeTypeRequest as StoreRequest;
use App\Http\Requests\GradeTypeRequest as UpdateRequest;
// VALIDATION: change the requests to match your own file names if you need form validation
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

/**
 * Class GradeTypeCrudController.
 * @property-read CrudPanel $crud
 */
class GradeTypeCrudController extends CrudController
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
        CRUD::setModel(\App\Models\GradeType::class);
        CRUD::setRoute(config('backpack.base.route_prefix').'/gradetype');
        CRUD::setEntityNameStrings('gradetype', 'grade_types');

        /*
        |--------------------------------------------------------------------------
        | CrudPanel Configuration
        |--------------------------------------------------------------------------
        */

        CRUD::addColumns([
            ['name' => 'name', 'label' => 'Name'],
            ['name' => 'total', 'label' => 'Total'],
        ]);

        CRUD::addFields([
            ['name' => 'name', 'label' => 'Name', 'type' => 'text'],
            ['name' => 'total', 'label' => 'Total', 'type' => 'text'],
        ]);

        // add asterisk for fields that are required in GradeTypeRequest
        CRUD::setRequiredFields(StoreRequest::class, 'create');
        CRUD::setRequiredFields(UpdateRequest::class, 'edit');
    }

    protected function setupCreateOperation()
    {
        CRUD::setValidation(StoreRequest::class);
    }

    protected function setupUpdateOperation()
    {
        $this->setupCreateOperation();
    }
}
