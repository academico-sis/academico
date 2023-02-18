<?php

namespace App\Http\Controllers\Admin;

use App\Models\Level;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
use Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
use Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;
use Illuminate\Validation\Rule;

class LevelCrudController extends CrudController
{
    use ListOperation;
    use CreateOperation;
    use UpdateOperation;

    public function setup()
    {
        CRUD::setModel(Level::class);
        CRUD::setRoute(config('backpack.base.route_prefix').'/level');
        CRUD::setEntityNameStrings(__('level'), __('levels'));
        CRUD::addClause('withTrashed');
        CRUD::addButtonFromView('line', 'toggle', 'toggle', 'end');
    }

    protected function setupListOperation()
    {
        CRUD::addColumn([
            'name' => 'name',
            'label' => __('Name'),
        ]);

        CRUD::addColumn([
            'name' => 'reference',
            'label' => __('Reference'),
            'type' => 'text',
        ]);
    }

    protected function setupCreateOperation()
    {
        CRUD::setValidation([
            'name' => [
                'required',
                'min:1',
                'max:255',
                Rule::unique($this->crud->getModel()->getTable())->ignore($this->crud->getCurrentEntry()),
            ],
        ]);

        CRUD::addField([
            'name' => 'name',
            'label' => 'Name',
            'type' => 'text',
        ]);

        CRUD::addField([
            'name' => 'reference',
            'label' => __('Reference'),
            'type' => 'text',
        ]);
    }

    protected function setupUpdateOperation()
    {
        $this->setupCreateOperation();
    }
}
