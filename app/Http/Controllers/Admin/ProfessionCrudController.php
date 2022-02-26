<?php

namespace App\Http\Controllers\Admin;

use App\Models\Profession;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
use Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
use Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

class ProfessionCrudController extends CrudController
{
    use ListOperation;
    use CreateOperation;
    use UpdateOperation;

    public function setup()
    {
        CRUD::setModel(Profession::class);
        CRUD::setRoute(config('backpack.base.route_prefix').'/profession');
        CRUD::setEntityNameStrings('profession', 'professions');
    }

    protected function setupListOperation()
    {
        CRUD::column('id');
        CRUD::column('name');
        CRUD::column('created_at');
        CRUD::column('updated_at');
    }

    protected function setupCreateOperation()
    {
        $this->crud->setValidation([
            'name' => 'required|min:1|max:255'
        ]);

        CRUD::field('name');
    }

    protected function setupUpdateOperation()
    {
        $this->setupCreateOperation();
    }
}
