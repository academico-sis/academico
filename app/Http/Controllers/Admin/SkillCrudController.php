<?php

namespace App\Http\Controllers\Admin;

use Backpack\CRUD\app\Http\Controllers\CrudController;

// VALIDATION: change the requests to match your own file names if you need form validation
use App\Http\Requests\SkillRequest as StoreRequest;
use App\Http\Requests\SkillRequest as UpdateRequest;

/**
 * Class SkillCrudController
 * @package App\Http\Controllers\Admin
 * @property-read CrudPanel $crud
 */
class SkillCrudController extends CrudController
{
    public function setup()
    {
        /*
        |--------------------------------------------------------------------------
        | CrudPanel Basic Information
        |--------------------------------------------------------------------------
        */
        $this->crud->setModel('App\Models\Skill');
        $this->crud->setRoute(config('backpack.base.route_prefix') . '/skill');
        $this->crud->setEntityNameStrings('skill', 'skills');

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

            [
                'label' => "Weight", // skill description
                'type' => "text",
                'name' => 'weight'
            ],

        ]);


        $this->crud->addFields([

            [ // skill type
                'label'     => 'Type', // Table column heading
                'type' => 'select',
                'name' => 'skill_type_id', // the db column for the foreign key
                'entity' => 'skill_type', // the method that defines the relationship in your Model
                'attribute' => 'name', // foreign key attribute that is shown to user
                'model' => "App\Models\SkillType",
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

            [
                'label' => "Weight", // skill description
                'type' => 'number',
                'attributes' => ["step" => "1"], // allow decimals
                'name' => 'weight'
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
              return \App\Models\SkillType::all()->pluck('name', 'id')->toArray();
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
}
