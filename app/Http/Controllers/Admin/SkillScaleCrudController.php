<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\SkillScaleRequest;
use App\Models\Skills\SkillScale;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
use Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;
use Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
use Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

class SkillScaleCrudController extends CrudController
{
    use ListOperation;
    use CreateOperation;
    use UpdateOperation;
    use DeleteOperation;

    public function setup()
    {
        CRUD::setModel(SkillScale::class);
        CRUD::setRoute(config('backpack.base.route_prefix').'/skillscale');
        CRUD::setEntityNameStrings(__('skill scale'), __('skill scales'));
    }

    protected function setupListOperation()
    {
        CRUD::addColumns([
            ['name' => 'shortname',
                'label' => 'Shortname', ],
            ['name' => 'name',
                'label' => 'Name', ],
            ['name' => 'value',
                'label' => 'Value', ],
        ]);
    }

    protected function setupCreateOperation()
    {
        CRUD::setValidation(SkillScaleRequest::class);
        CRUD::addFields([
            ['name' => 'shortname',
                'label' => 'Shortname',
                'type' => 'text', ],
            ['name' => 'name',
                'label' => 'Name',
                'type' => 'text', ],
            ['name' => 'value',
                'label' => 'Value',
                'type' => 'text', ],
        ]);
    }

    protected function setupUpdateOperation()
    {
        $this->setupCreateOperation();
    }
}
