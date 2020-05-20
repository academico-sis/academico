<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\RhythmRequest as StoreRequest;
use App\Http\Requests\RhythmRequest as UpdateRequest;
// VALIDATION: change the requests to match your own file names if you need form validation
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

/**
 * Class RhythmCrudController.
 * @property-read CrudPanel $crud
 */
class RhythmCrudController extends CrudController
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
        CRUD::setModel(\App\Models\Rhythm::class);
        CRUD::setRoute(config('backpack.base.route_prefix').'/rhythm');
        CRUD::setEntityNameStrings('rhythm', 'rhythms');

        /*
        |--------------------------------------------------------------------------
        | CrudPanel Configuration
        |--------------------------------------------------------------------------
        */

        CRUD::addColumns([
            ['name' => 'name', 'label' => 'Name'],
            ['name' => 'default_volume', 'label' => 'Default volume'],
            ['name' => 'product_code', 'label' => 'Product code'],
        ]);

        CRUD::addFields([
            ['name' => 'name', 'label' => 'Name', 'type' => 'text'],
            ['name' => 'default_volume', 'label' => 'Default volume', 'type' => 'text'],
            ['name' => 'product_code', 'label' => 'Product code', 'type' => 'text'],
        ]);

        // add asterisk for fields that are required in RhythmRequest
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
