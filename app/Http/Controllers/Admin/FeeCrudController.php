<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\FeeRequest as StoreRequest;
// VALIDATION: change the requests to match your own file names if you need form validation
use App\Models\Fee;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
use Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;
use Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
use Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

/**
 * Class FeeCrudController.
 */
class FeeCrudController extends CrudController
{
    use ListOperation;
    use CreateOperation;
    use UpdateOperation;
    use DeleteOperation;

    public function setup()
    {
        /*
        |--------------------------------------------------------------------------
        | CrudPanel Basic Information
        |--------------------------------------------------------------------------
        */
        CRUD::setModel(Fee::class);
        CRUD::setRoute(config('backpack.base.route_prefix').'/fee');
        CRUD::setEntityNameStrings(__('fee'), __('fees'));
    }

    protected function setupListOperation()
    {
        CRUD::addColumns([
            ['name' => 'name', 'label' => 'Name'],
            ['name' => 'price', 'label' => 'Price'],
            ['name' => 'product_code', 'label' => 'Product Code'],
            ['name' => 'default', 'label' => 'Add automatically to every order', 'type'  => 'check'],
        ]);
    }

    protected function setupCreateOperation()
    {
        CRUD::setValidation(StoreRequest::class);
        CRUD::addFields([
            ['name' => 'name', 'label' => 'Name', 'type' => 'text'],
            ['name' => 'price', 'label' => 'Price', 'type' => 'text'],
            ['name' => 'product_code', 'label' => 'Product Code', 'type' => 'text'],
            ['name' => 'default', 'label' => 'Add automatically to every order', 'type' => 'checkbox'],
        ]);
    }

    protected function setupUpdateOperation()
    {
        $this->setupCreateOperation();
    }
}
