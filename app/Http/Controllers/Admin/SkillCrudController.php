<?php

namespace App\Http\Controllers\Admin;

use App\Models\Course;

// VALIDATION: change the requests to match your own file names if you need form validation
use App\Models\Skills\Skill;
use App\Http\Requests\SkillRequest as StoreRequest;
use App\Http\Requests\SkillRequest as UpdateRequest;
use Backpack\CRUD\app\Http\Controllers\CrudController;

/**
 * Class SkillCrudController
 * @package App\Http\Controllers\Admin
 * @property-read CrudPanel $crud
 */
class SkillCrudController extends CrudController
{

    public function __construct()
    {
        parent::__construct();
        $this->middleware(['permission:evaluation.edit']);
    }

    
    public function setup()
    {
        /*
        |--------------------------------------------------------------------------
        | CrudPanel Basic Information
        |--------------------------------------------------------------------------
        */
        $this->crud->setModel('App\Models\Skills\Skill');
        $this->crud->setRoute(config('backpack.base.route_prefix') . '/skill');
        $this->crud->setEntityNameStrings('skill', 'skills');

        $this->crud->addButtonFromView('top', 'bulk_attach', 'bulk_attach', 'end');


        /*
        |--------------------------------------------------------------------------
        | CrudPanel Configuration
        |--------------------------------------------------------------------------
        */
        //$this->crud->setFromDb();
        $this->crud->setColumns([

            [ // skill type
                'label'     => 'Type', // Table column heading
                'type'      => 'select',
                'name'      => 'skill_type', // the method that defines the relationship in your Model
                'entity'    => 'skill_type', // the method that defines the relationship in your Model
                'attribute' => 'name', // foreign key attribute that is shown to user
                'model'     => 'skill_type', // foreign key model
             ],

            [
                'label' => "Name", // skill description
                'type' => "text",
                'name' => 'name'
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

        $this->crud->enableBulkActions();

        $this->crud->addFields([

            [ // skill type
                'label'     => 'Type', // Table column heading
                'type' => 'select',
                'name' => 'skill_type_id', // the db column for the foreign key
                'entity' => 'skill_type', // the method that defines the relationship in your Model
                'attribute' => 'name', // foreign key attribute that is shown to user
                'model' => "App\Models\Skills\SkillType",
             ],

            [
                'label' => "Name", // skill description
                'type' => "text",
                'name' => 'name'
            ],

            [ // skill level
               'label'     => 'Level', // Table column heading
               'type' => 'select',
               'name' => 'level_id', // the db column for the foreign key
               'entity' => 'level', // the method that defines the relationship in your Model
               'attribute' => 'name', // foreign key attribute that is shown to user
               'model' => "App\Models\Level",
            ],


        ]);

        // add asterisk for fields that are required in SkillRequest
        $this->crud->setRequiredFields(StoreRequest::class, 'create');
        $this->crud->setRequiredFields(UpdateRequest::class, 'edit');


        $this->crud->addFilter([ // select2 filter
            'name' => 'level_id',
            'type' => 'select2',
            'label'=> 'Level'
          ], function() {
              return \App\Models\Level::all()->pluck('name', 'id')->toArray();
          }, function($value) { // if the filter is active
                  $this->crud->addClause('where', 'level_id', $value);
          });

          $this->crud->addFilter([ // select2 filter
            'name' => 'skill_type_id',
            'type' => 'select2',
            'label'=> 'Type'
          ], function() {
              return \App\Models\Skills\SkillType::all()->pluck('name', 'id')->toArray();
          }, function($value) { // if the filter is active
                  $this->crud->addClause('where', 'skill_type_id', $value);
          });

    }

    public function store(StoreRequest $request)
    {
        // your additional operations before save here
        $redirect_location = parent::storeCrud($request);
        // your additional operations after save here
        // use $this->data['entry'] or $this->crud->entry
        return $redirect_location;
    }

    public function update(UpdateRequest $request)
    {
        // your additional operations before save here
        $redirect_location = parent::updateCrud($request);
        // your additional operations after save here
        // use $this->data['entry'] or $this->crud->entry
        return $redirect_location;
    }

    public function bulkAttachToCourse()
    {
        $this->middleware(['permission:evaluation.edit']);

        $entries = $this->request->input('entries');
        $course = Course::find($this->request->input('course'));

        $course->skills()->detach();

        foreach ($entries as $skill)
        {
            $course->skills()->attach(Skill::find($skill),
            ['weight' => 1]); // todo allow edition of this parameter
        }
        \Alert::success(__('Skills set was saved for the course'))->flash();

    }
}
