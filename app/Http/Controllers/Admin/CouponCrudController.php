<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\CouponRequest;
use App\Models\Coupon;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
use Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;
use Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
use Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

class CouponCrudController extends CrudController
{
    use ListOperation;
    use CreateOperation;
    use UpdateOperation;
    use DeleteOperation;

    public function setup()
    {
        CRUD::setModel(Coupon::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/coupon');
        CRUD::setEntityNameStrings(__('coupon'), __('coupons'));
    }

    public function setupListOperation()
    {
        CRUD::addColumns([
            ['name' => 'name',
                'label' => 'Name', ],
            ['name' => 'value',
                'label' => 'Value', ],
        ]);
    }

    public function setupCreateOperation()
    {
        CRUD::setValidation(CouponRequest::class);

        CRUD::addFields([
            ['name' => 'name',
                'label' => 'Name',
                'type' => 'text', ],
            ['name' => 'value',
                'label' => 'Value',
                'type' => 'text', ],
        ]);
    }

    public function setupUpdateOperation()
    {
        $this->setupCreateOperation();
    }
}
