<?php

namespace App\Http\Controllers\Admin;

use App\Models\Coupon;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
use Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;
use Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
use Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;
use Illuminate\Validation\Rule;

class CouponCrudController extends CrudController
{
    use ListOperation;
    use CreateOperation;
    use UpdateOperation;
    use DeleteOperation;

    public function setup()
    {
        CRUD::setModel(Coupon::class);
        CRUD::setRoute(config('backpack.base.route_prefix').'/coupon');
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
        CRUD::setValidation([
            'name' => [
                'required',
                'min:1',
                'max:255',
                Rule::unique($this->crud->getModel()->getTable())->ignore($this->crud->getCurrentEntry()),
            ],
            'value' => 'required|numeric|min:0',
        ]);

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
