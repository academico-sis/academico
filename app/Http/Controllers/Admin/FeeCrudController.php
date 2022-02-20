<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\FeeRequest;
use App\Models\Fee;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
use Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;
use Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
use Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

class FeeCrudController extends CrudController
{
    use ListOperation;
    use CreateOperation;
    use UpdateOperation;
    use DeleteOperation;

    public function setup()
    {
        CRUD::setModel(Fee::class);
        CRUD::setRoute(config('backpack.base.route_prefix').'/fee');
        CRUD::setEntityNameStrings(__('fee'), __('fees'));
    }

    protected function setupListOperation()
    {
        CRUD::addColumns([
            [
                'name' => 'name',
                'label' => __('Name'),
            ],
            [
            '   name' => 'price',
                'label' => __('Price'),
            ],
            [
                'name' => 'product_code',
                'label' => __('Product Code'),
            ],
            [
                'name' => 'default',
                'label' => __('Add automatically to every order'),
                'type' => 'check',
            ],
        ]);
    }

    protected function setupCreateOperation()
    {
        CRUD::setValidation(FeeRequest::class);

        CRUD::addFields([
            [
                'name' => 'name',
                'label' => __('Name'),
                'type' => 'text',
            ],
            [
                'name' => 'price',
                'label' => __('Price'),
                'type' => 'text',
            ],
            [
                'name' => 'product_code',
                'label' => __('Product Code'),
                'type' => 'text',
            ],
            [
                'name' => 'default',
                'label' => __('Add automatically to every order'),
                'type' => 'checkbox',
            ],
        ]);
    }

    protected function setupUpdateOperation()
    {
        $this->setupCreateOperation();
    }
}
