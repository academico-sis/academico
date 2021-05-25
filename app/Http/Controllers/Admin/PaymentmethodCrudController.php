<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\PaymentmethodRequest as StoreRequest;
// VALIDATION: change the requests to match your own file names if you need form validation
use App\Models\Paymentmethod;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
use Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;
use Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
use Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

class PaymentmethodCrudController extends CrudController
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
        CRUD::setModel(Paymentmethod::class);
        CRUD::setRoute(config('backpack.base.route_prefix').'/paymentmethod');
        CRUD::setEntityNameStrings(__('Payment method'), __('Payment methods'));
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
