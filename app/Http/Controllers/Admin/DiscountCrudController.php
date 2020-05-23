<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\DiscountRequest as StoreRequest;
// VALIDATION: change the requests to match your own file names if you need form validation
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;
use Backpack\CRUD\CrudPanel;

/**
 * Class DiscountCrudController.
 * @property-read CrudPanel $crud
 */
class DiscountCrudController extends CrudController
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
        CRUD::setModel(\App\Models\Discount::class);
        CRUD::setRoute(config('backpack.base.route_prefix').'/discount');
        CRUD::setEntityNameStrings('discount', 'discounts');
    }

    protected function setupListOperation()
    {
        CRUD::setColumns([
            [
                'name' => 'id',
                'label' => 'ID',
            ],
            [
                // Discount name
                'label' => __('Name'), // Table column heading
                'type' => 'text',
                'name' => 'name',
            ],
            [
                // Value
                'label' => __('Discount Value'), // Table column heading
                'type' => 'decimal',
                'name' => 'value',
                'suffix' => '%',
            ],
        ]);
    }

    protected function setupCreateOperation()
    {
        CRUD::setValidation(StoreRequest::class);
        CRUD::addFields([
            [
                // Discount name
                'label' => __('Name'), // Table column heading
                'type' => 'text',
                'name' => 'name',
            ],
            [
                // Value
                'label' => __('Discount Value (0-100%)'), // Table column heading
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
