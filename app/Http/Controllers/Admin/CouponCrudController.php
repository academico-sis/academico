<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\CouponRequest as StoreRequest;
// VALIDATION: change the requests to match your own file names if you need form validation
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;
use Backpack\CRUD\CrudPanel;

/**
 * Class CouponCrudController.
 * @property-read CrudPanel $crud
 */
class CouponCrudController extends CrudController
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
        CRUD::setModel(\App\Models\Coupon::class);
        CRUD::setRoute(config('backpack.base.route_prefix').'/coupon');
        CRUD::setEntityNameStrings('coupon', 'coupons');

        /*
        |--------------------------------------------------------------------------
        | CrudPanel Configuration
        |--------------------------------------------------------------------------
        */

        CRUD::addColumns([
            ['name' => 'name', 'label' => 'Name'],
            ['name' => 'value', 'label' => 'Value'],
        ]);

        CRUD::addFields([
            ['name' => 'name', 'label' => 'Name', 'type' => 'text'],
            ['name' => 'value', 'label' => 'Value', 'type' => 'text'],
        ]);
    }

    protected function setupCreateOperation()
    {
        CRUD::setValidation(StoreRequest::class);
    }

    protected function setupUpdateOperation()
    {
        $this->setupCreateOperation();
    }
}
