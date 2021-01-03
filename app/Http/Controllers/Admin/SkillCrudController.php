<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\SkillRequest as StoreRequest;
// VALIDATION: change the requests to match your own file names if you need form validation
use App\Models\Level;
use App\Models\Skills\Skill;
use App\Models\Skills\SkillType;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
use Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;
use Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
use Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

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
            [ // skill type
                'label'     => 'Type', // Table column heading
                'type'      => 'select',
                'name'      => 'skill_type', // the method that defines the relationship in your Model
                'entity'    => 'skill_type', // the method that defines the relationship in your Model
                'attribute' => 'name', // foreign key attribute that is shown to user
                'model'     => 'skill_type', // foreign key model
            ],
            [
                'label' => 'Name', // skill description
                'type' => 'text',
                'name' => 'name',
            ],
            [ // skill level
                'label'     => 'Level', // Table column heading
                'type'      => 'select',
                'name'      => 'level', // the method that defines the relationship in your Model
                'entity'    => 'level', // the method that defines the relationship in your Model
                'attribute' => 'name', // foreign key attribute that is shown to user
                'model'     => 'level', // foreign key model
            ],
        ]);

        CRUD::enableBulkActions();

        CRUD::addFilter([ // select2 filter
            'name' => 'level_id',
            'type' => 'select2',
            'label'=> 'Level',
        ], function () {
            return Level::all()->pluck('name', 'id')->toArray();
        }, function ($value) { // if the filter is active
            CRUD::addClause('where', 'level_id', $value);
        });

        CRUD::addFilter([ // select2 filter
            'name' => 'skill_type_id',
            'type' => 'select2',
            'label'=> 'Type',
        ], function () {
            return SkillType::all()->pluck('name', 'id')->toArray();
        }, function ($value) { // if the filter is active
            CRUD::addClause('where', 'skill_type_id', $value);
        });
    }

    protected function setupCreateOperation()
    {
        CRUD::setValidation(StoreRequest::class);
        CRUD::addFields([
            [ // skill type
                'label'     => 'Type', // Table column heading
                'type' => 'select',
                'name' => 'skill_type_id', // the db column for the foreign key
                'entity' => 'skill_type', // the method that defines the relationship in your Model
                'attribute' => 'name', // foreign key attribute that is shown to user
                'model' => SkillType::class,
            ],
            [
                'label' => 'Name', // skill description
                'type' => 'text',
                'name' => 'name',
            ],
            [ // skill level
                'label'     => 'Level', // Table column heading
                'type' => 'select',
                'name' => 'level_id', // the db column for the foreign key
                'entity' => 'level', // the method that defines the relationship in your Model
                'attribute' => 'name', // foreign key attribute that is shown to user
                'model' => Level::class,
            ],
        ]);
    }

    protected function setupUpdateOperation()
    {
        $this->setupCreateOperation();
    }
}
