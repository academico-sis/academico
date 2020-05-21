<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\PaymentmethodRequest as StoreRequest;
// VALIDATION: change the requests to match your own file names if you need form validation
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;
use Backpack\CRUD\CrudPanel;

/**
 * Class PaymentmethodCrudController.
 * @property-read CrudPanel $crud
 */
class PaymentmethodCrudController extends CrudController
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
        CRUD::setModel(\App\Models\Paymentmethod::class);
        CRUD::setRoute(config('backpack.base.route_prefix').'/paymentmethod');
        CRUD::setEntityNameStrings('paymentmethod', 'paymentmethods');
    }

    protected function setupListOperation()
    {
        CRUD::addColumns([
            ['name' => 'name', 'label' => 'Name'],
            ['name' => 'code', 'label' => 'Code'],
        ]);
    }

    protected function setupCreateOperation()
    {
        CRUD::setValidation(StoreRequest::class);
        CRUD::addFields([
            ['name' => 'name', 'label' => 'Name', 'type' => 'text'],
            ['name' => 'code', 'label' => 'Code', 'type' => 'text'],
        ]);
    }

    protected function setupUpdateOperation()
    {
        $this->setupCreateOperation();
    }
}
