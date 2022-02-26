<?php

namespace App\Http\Controllers\Admin;

use App\Models\Year;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
use Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;
use Backpack\CRUD\app\Http\Controllers\Operations\InlineCreateOperation;
use Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
use Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;
use Illuminate\Validation\Rule;

class YearCrudController extends CrudController
{
    use ListOperation;
    use CreateOperation;
    use InlineCreateOperation;
    use UpdateOperation;
    use DeleteOperation;

    public function setup()
    {
        CRUD::setModel(Year::class);
        CRUD::setRoute(config('backpack.base.route_prefix').'/year');
        CRUD::setEntityNameStrings(__('year'), __('years'));
    }

    protected function setupListOperation()
    {
        CRUD::addColumn(['name' => 'name',
            'label' => 'Name', ]);
    }

    protected function setupCreateOperation()
    {
        CRUD::setValidation([
            'name' => [
                'required',
                'min:1',
                'max:10',
                Rule::unique($this->crud->getModel()->getTable())->ignore($this->crud->getCurrentEntry()),
            ],
        ]);

        CRUD::addField(['name' => 'name',
            'label' => 'Name',
            'type' => 'text', ]);
    }

    protected function setupUpdateOperation()
    {
        $this->setupCreateOperation();
    }
}
