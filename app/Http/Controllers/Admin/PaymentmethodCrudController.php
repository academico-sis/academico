<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\PaymentmethodRequest;
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
        CRUD::setModel(Paymentmethod::class);
        CRUD::setRoute(config('backpack.base.route_prefix').'/paymentmethod');
        CRUD::setEntityNameStrings(__('Payment method'), __('Payment methods'));
    }

    protected function setupListOperation()
    {
        CRUD::addColumns([
            [
                'name' => 'name',
                'label' => __('Name'),
            ],
            [
                'name' => 'code',
                'label' => __('Code'),
            ],
        ]);
    }

    protected function setupCreateOperation()
    {
        CRUD::setValidation(PaymentmethodRequest::class);

        CRUD::addFields([
            [
                'name' => 'name',
                'label' => 'Name',
                'type' => 'text',
            ],
            [
                'name' => 'code',
                'label' => __('Code'),
                'type' => 'text',
            ],
        ]);
    }

    protected function setupUpdateOperation()
    {
        $this->setupCreateOperation();
    }
}
