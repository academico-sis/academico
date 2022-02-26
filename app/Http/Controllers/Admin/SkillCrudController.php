<?php

namespace App\Http\Controllers\Admin;

use App\Models\Level;
use App\Models\Skills\Skill;
use App\Models\Skills\SkillType;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
use Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;
use Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
use Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;
use Illuminate\Validation\Rule;

class SkillCrudController extends CrudController
{
    use ListOperation;
    use CreateOperation;
    use UpdateOperation;
    use DeleteOperation;

    public function setup()
    {
        CRUD::setModel(Skill::class);
        CRUD::setRoute(config('backpack.base.route_prefix').'/skill');
        CRUD::setEntityNameStrings(__('skill'), __('skills'));

    }

    protected function setupListOperation()
    {
        CRUD::setColumns([
            [
                'label' => __('Type'),
                'type' => 'select',
                'name' => 'skillType',
                'entity' => 'skillType',
                'attribute' => 'name',
                'model' => 'skillType',
            ],
            [
                'label' => __('Name'),
                'type' => 'text',
                'name' => 'name',
            ],
            [
                'label' => __('Level'),
                'type' => 'select',
                'name' => 'level',
                'entity' => 'level',
                'attribute' => 'name',
                'model' => 'level',
            ],
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
            'level_id' => 'required',
            'skill_type_id' => 'required',
        ]);

        CRUD::addFields([
            [
                'label' => __('Type'),
                'type' => 'select',
                'name' => 'skill_type_id',
                'entity' => 'skillType',
                'attribute' => 'name',
                'model' => SkillType::class,
            ],
            [
                'label' => __('Name'),
                'type' => 'text',
                'name' => 'name',
            ],
            [
                'label' => __('Level'),
                'type' => 'select',
                'name' => 'level_id',
                'entity' => 'level',
                'attribute' => 'name',
                'model' => Level::class,
            ],
            [
                'label' => __('Skill Type'),
                'type' => 'select',
                'name' => 'skillType',
                'ajax' => true,
            ],
        ]);
    }

    protected function setupUpdateOperation()
    {
        $this->setupCreateOperation();
    }
}
