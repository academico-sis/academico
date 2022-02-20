<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\DiscountRequest;
use App\Models\Discount;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
use Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;
use Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
use Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

class DiscountCrudController extends CrudController
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
        CRUD::setModel(Discount::class);
        CRUD::setRoute(config('backpack.base.route_prefix').'/discount');
        CRUD::setEntityNameStrings(__('discount'), __('discounts'));
    }

    protected function setupListOperation()
    {
        CRUD::setColumns([
            [
                'name' => 'id',
                'label' => 'ID',
            ],
            [
                'label' => __('Name'),
                'type' => 'text',
                'name' => 'name',
            ],
            [
                'label' => __('Discount Value'),
                'type' => 'decimal',
                'name' => 'value',
                'suffix' => '%',
            ],
        ]);
    }

    protected function setupCreateOperation()
    {
        CRUD::setValidation(DiscountRequest::class);

        CRUD::addFields([
            [
                'label' => __('Name'),
                'type' => 'text',
                'name' => 'name',
            ],
            [
                'label' => __('Discount Value (0-100%)'),
                'type' => 'number',
                'attributes' => ['step' => '1'],
                'name' => 'value',
            ],
        ]);
    }

    protected function setupUpdateOperation()
    {
        $this->setupCreateOperation();
    }
}
