<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\SkillRequest as StoreRequest;
// VALIDATION: change the requests to match your own file names if you need form validation
use App\Models\Course;
use App\Models\Skills\Skill;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

/**
 * Class SkillCrudController.
 * @property-read CrudPanel $crud
 */
class SkillCrudController extends CrudController
{
    use \Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;

    public function setup()
    {
        CRUD::setModel(\App\Models\Skills\Skill::class);
        CRUD::setRoute(config('backpack.base.route_prefix').'/skill');
        CRUD::setEntityNameStrings('skill', 'skills');
        CRUD::addButtonFromView('top', 'bulk_attach', 'bulk_attach', 'end');
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
            return \App\Models\Level::all()->pluck('name', 'id')->toArray();
        }, function ($value) { // if the filter is active
            CRUD::addClause('where', 'level_id', $value);
        });

        CRUD::addFilter([ // select2 filter
            'name' => 'skill_type_id',
            'type' => 'select2',
            'label'=> 'Type',
        ], function () {
            return \App\Models\Skills\SkillType::all()->pluck('name', 'id')->toArray();
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
                'model' => \App\Models\Skills\SkillType::class,
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
                'model' => \App\Models\Level::class,
            ],
        ]);
    }

    protected function setupUpdateOperation()
    {
        $this->setupCreateOperation();
    }

    public function bulkAttachToCourse()
    {
        $this->middleware(['permission:evaluation.edit']);

        $entries = request()->input('entries');
        $course = Course::find(request()->input('course'));

        $course->skills()->detach();

        foreach ($entries as $skill) {
            $course->skills()->attach(Skill::find($skill),
            ['weight' => 1]); // todo allow edition of this parameter
        }
        \Alert::success(__('Skills set was saved for the course'))->flash();

        return $course->id;
    }
}
