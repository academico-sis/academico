<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\SkillRequest as StoreRequest;
use App\Models\Level;
use App\Models\Skills\Skill;
use App\Models\Skills\SkillType;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
use Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;
use Backpack\CRUD\app\Http\Controllers\Operations\FetchOperation;
use Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
use Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

class SkillCrudController extends CrudController
{
    use ListOperation;
    use CreateOperation;
    use UpdateOperation;
    use DeleteOperation;
    use FetchOperation;

    public function setup()
    {
        CRUD::setModel(Skill::class);
        CRUD::setRoute(config('backpack.base.route_prefix').'/skill');
        CRUD::setEntityNameStrings(__('skill'), __('skills'));
    }

    protected function setupListOperation()
    {
        CRUD::setColumns([
            [ // skill type
                'label' => 'Type',
                'type' => 'select',
                'name' => 'skill_type',
                'entity' => 'skill_type',
                'attribute' => 'name',
                'model' => 'skill_type',
            ],
            [
                'label' => 'Name',
                // skill description
                'type' => 'text',
                'name' => 'name',
            ],
            [ // skill level
                'label' => 'Level',
                'type' => 'select',
                'name' => 'level',
                'entity' => 'level',
                'attribute' => 'name',
                'model' => 'level',
            ],
        ]);

        CRUD::enableExportButtons();

        CRUD::addFilter([ // select2 filter
            'name' => 'level_id',
            'type' => 'select2',
            'label' => 'Level',
        ], fn () => Level::all()->pluck('name', 'id')->toArray(), function ($value) {
            CRUD::addClause('where', 'level_id', $value);
        });

        CRUD::addFilter([ // select2 filter
            'name' => 'skill_type_id',
            'type' => 'select2',
            'label' => 'Type',
        ], fn () => SkillType::all()->pluck('name', 'id')->toArray(), function ($value) {
            CRUD::addClause('where', 'skill_type_id', $value);
        });
    }

    protected function setupCreateOperation()
    {
        CRUD::setValidation(StoreRequest::class);
        CRUD::addFields([
            [ // skill type
                'label' => 'Type',
                'type' => 'select',
                'name' => 'skill_type_id',
                'entity' => 'skill_type',
                'attribute' => 'name',
                'model' => SkillType::class,
            ],
            [
                'label' => 'Name',
                // skill description
                'type' => 'text',
                'name' => 'name',
            ],
            [ // skill level
                'label' => 'Level',
                'type' => 'select',
                'name' => 'level_id',
                'entity' => 'level',
                'attribute' => 'name',
                'model' => Level::class,
            ],
            [
                'label' => __('Skill Type'),
                'type' => 'select',
                'name' => 'skill_type',
                'ajax' => true,
            ],
        ]);
    }

    protected function setupUpdateOperation()
    {
        $this->setupCreateOperation();
    }

    protected function fetchSkill_type()
    {
        return $this->fetch(SkillType::class);
    }
}
