<?php

namespace App\Http\Controllers\Admin;

use Backpack\CRUD\app\Http\Controllers\CrudController;

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
    public function setup()
    {
        /*
        |--------------------------------------------------------------------------
        | CrudPanel Basic Information
        |--------------------------------------------------------------------------
        */
        $this->crud->setModel('App\Models\Discount');
        $this->crud->setRoute(config('backpack.base.route_prefix') . '/discount');
        $this->crud->setEntityNameStrings('discount', 'discounts');

        /*
        |--------------------------------------------------------------------------
        | CrudPanel Configuration
        |--------------------------------------------------------------------------
        */

        $this->crud->setColumns([

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


        $this->crud->addFields([
           
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
        $this->crud->setRequiredFields(StoreRequest::class, 'create');
        $this->crud->setRequiredFields(UpdateRequest::class, 'edit');
    }

    public function store(StoreRequest $request)
    {
        // your additional operations before save here
        $redirect_location = parent::storeCrud($request);
        // your additional operations after save here
        // use $this->data['entry'] or $this->crud->entry
        return $redirect_location;
    }

    public function update(UpdateRequest $request)
    {
        // your additional operations before save here
        $redirect_location = parent::updateCrud($request);
        // your additional operations after save here
        // use $this->data['entry'] or $this->crud->entry
        return $redirect_location;
    }
}
