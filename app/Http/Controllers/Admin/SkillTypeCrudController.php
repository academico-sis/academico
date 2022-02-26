<?php

namespace App\Http\Controllers\Admin;

use App\Models\Skills\SkillType;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
use Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;
use Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
use Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;
use Illuminate\Validation\Rule;

class SkillTypeCrudController extends CrudController
{
    use ListOperation;
    use CreateOperation;
    use UpdateOperation;
    use DeleteOperation;

    public function setup()
    {
        CRUD::setModel(SkillType::class);
        CRUD::setRoute(config('backpack.base.route_prefix').'/skilltype');
        CRUD::setEntityNameStrings(__('skill type'), __('skill types'));
    }

    protected function setupListOperation()
    {
        CRUD::addColumns([
            ['name' => 'shortname',
                'label' => 'Shortname', ],
            ['name' => 'name',
                'label' => 'Name', ],
        ]);
    }

    protected function setupCreateOperation()
    {
        CRUD::setValidation([
            'name' => [
                'required',
                'min:1',
                'max:40',
                Rule::unique($this->crud->getModel()->getTable())->ignore($this->crud->getCurrentEntry()),
            ],
            'shortname' => [
                'required',
                'min:1',
                'max:8',
                Rule::unique($this->crud->getModel()->getTable())->ignore($this->crud->getCurrentEntry()),
            ],
        ]);

        CRUD::addFields([
            ['name' => 'shortname',
                'label' => 'Shortname',
                'type' => 'text', ],
            ['name' => 'name',
                'label' => 'Name',
                'type' => 'text', ],
        ]);
    }

    protected function setupUpdateOperation()
    {
        $this->setupCreateOperation();
    }
}
