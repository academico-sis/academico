<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\FeeRequest as StoreRequest;
// VALIDATION: change the requests to match your own file names if you need form validation
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;
use Backpack\CRUD\CrudPanel;

/**
 * Class FeeCrudController.
 * @property-read CrudPanel $crud
 */
class FeeCrudController extends CrudController
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
        CRUD::setModel(\App\Models\Fee::class);
        CRUD::setRoute(config('backpack.base.route_prefix').'/fee');
        CRUD::setEntityNameStrings('fee', 'fees');
    }

    protected function setupListOperation()
    {
        CRUD::addColumns([
            ['name' => 'name', 'label' => 'Name'],
            ['name' => 'price', 'label' => 'Price'],
            ['name' => 'product_code', 'label' => 'Product Code'],
        ]);
    }

    protected function setupCreateOperation()
    {
        CRUD::setValidation(StoreRequest::class);
        CRUD::addFields([
            ['name' => 'name', 'label' => 'Name', 'type' => 'text'],
            ['name' => 'price', 'label' => 'Price', 'type' => 'text'],
            ['name' => 'product_code', 'label' => 'Product Code', 'type' => 'text'],
        ]);
    }

    protected function setupUpdateOperation()
    {
        $this->setupCreateOperation();
    }
}
