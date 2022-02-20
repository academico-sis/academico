<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\SkillTypeRequest;
use App\Models\Skills\SkillType;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
use Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;
use Backpack\CRUD\app\Http\Controllers\Operations\InlineCreateOperation;
use Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
use Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

class SkillTypeCrudController extends CrudController
{
    use ListOperation;
    use CreateOperation;
    use InlineCreateOperation;
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
        CRUD::setValidation(SkillTypeRequest::class);
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
