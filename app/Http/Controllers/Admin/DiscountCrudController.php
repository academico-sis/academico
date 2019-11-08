<?php

namespace App\Http\Controllers\Admin;

use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

// VALIDATION: change the requests to match your own file names if you need form validation
use App\Http\Requests\DiscountRequest as StoreRequest;
use App\Http\Requests\DiscountRequest as UpdateRequest;
use Backpack\CRUD\CrudPanel;

/**
 * Class DiscountCrudController
 * @package App\Http\Controllers\Admin
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
        CRUD::setModel('App\Models\Discount');
        CRUD::setRoute(config('backpack.base.route_prefix') . '/discount');
        CRUD::setEntityNameStrings('discount', 'discounts');

        /*
        |--------------------------------------------------------------------------
        | CrudPanel Configuration
        |--------------------------------------------------------------------------
        */

        CRUD::setColumns([

            [
                'name' => 'id',
                'label' => "ID",
            ],

            [
                // Discount name
                'label' => __("Name"), // Table column heading
                'type' => "text",
                'name' => 'name'
            ],

            [
                // Value
                'label' => __("Discount Value"), // Table column heading
                'type' => "decimal",
                'name' => 'value',
                'suffix' => '%'
            ],

        ]);


        CRUD::addFields([
           
            [
                // Discount name
                'label' => __("Name"), // Table column heading
                'type' => "text",
                'name' => 'name'
            ],

            [
                // Value
                'label' => __("Discount Value (0-100%)"), // Table column heading
                'type' => 'number',
                'attributes' => ["step" => "1"],
                'name' => 'value'
            ],

        ]);

        // add asterisk for fields that are required in DiscountRequest
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
