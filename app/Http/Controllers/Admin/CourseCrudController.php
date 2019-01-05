<?php

namespace App\Http\Controllers\Admin;

use Backpack\CRUD\app\Http\Controllers\CrudController;

// VALIDATION: change the requests to match your own file names if you need form validation
use App\Http\Requests\CourseRequest as StoreRequest;
use App\Http\Requests\CourseRequest as UpdateRequest;

/**
 * Class CourseCrudController
 * @package App\Http\Controllers\Admin
 * @property-read CrudPanel $crud
 */
class CourseCrudController extends CrudController
{
    public function setup()
    {
        /*
        |--------------------------------------------------------------------------
        | CrudPanel Basic Information
        |--------------------------------------------------------------------------
        */
        $this->crud->setModel('App\Models\Course');
        $this->crud->setRoute(config('backpack.base.route_prefix') . '/course');
        $this->crud->setEntityNameStrings('course', 'courses');

        $permissions = backpack_user()->getAllPermissions();
        if(!$permissions->contains('name', 'courses.delete')) {$this->crud->denyAccess('delete'); }
        if(!$permissions->contains('name', 'courses.edit')) {$this->crud->denyAccess('edit'); }
        if(!$permissions->contains('name', 'courses.create')) {$this->crud->denyAccess('new'); }



        /*
        |--------------------------------------------------------------------------
        | CrudPanel Configuration
        |--------------------------------------------------------------------------
        */

        $this->crud->setColumns([
            [
                // RYTHM
                'label' => "Rythm", // Table column heading
                'type' => "select",
                'name' => 'rythm_id', // the column that contains the ID of that connected entity;
                'entity' => 'rythm', // the method that defines the relationship in your Model
                'attribute' => "name", // foreign key attribute that is shown to user
                'model' => "App\Models\Rythm", // foreign key model
             ],

             [
                // LEVEL
                'label' => "Level", // Table column heading
                'type' => "select",
                'name' => 'level_id', // the column that contains the ID of that connected entity;
                'entity' => 'level', // the method that defines the relationship in your Model
                'attribute' => "name", // foreign key attribute that is shown to user
                'model' => "App\Models\Level", // foreign key model
             ],

             [
                'name' => 'name', // The db column name
                'label' => "Name", // Table column heading
             ],

             [
                'name' => 'volume', // The db column name
                'label' => "Volume", // Table column heading
                'suffix' => "h",
             ],

             [
                // TEACHER
                'label' => "Teacher", // Table column heading
                'type' => "select",
                'name' => 'teacher_id', // the column that contains the ID of that connected entity;
                'entity' => 'teacher', // the method that defines the relationship in your Model
                'attribute' => "name", // foreign key attribute that is shown to user
                'model' => "App\Models\User", // foreign key model
             ],

             [
                // ROOM
                'label' => "Room", // Table column heading
                'type' => "select",
                'name' => 'room_id', // the column that contains the ID of that connected entity;
                'entity' => 'room', // the method that defines the relationship in your Model
                'attribute' => "name", // foreign key attribute that is shown to user
                'model' => "App\Models\Room", // foreign key model
             ],

             // COURSE SCHEDULED TIMES
             [
                'name' => "times",
                'label' => "Schedule", // Table column heading
                'type' => "model_function",
                'function_name' => 'getCourseTimesAttribute', // the method in your Model
                // 'limit' => 100, // Limit the number of characters shown
             ],

             // EVALUATION METHODS
             [
                // n-n relationship (with pivot table)
                'label' => "Evaluation method", // Table column heading
                'type' => "select_multiple",
                'name' => 'evaluation_type', // the method that defines the relationship in your Model
                'entity' => 'evaluation_type', // the method that defines the relationship in your Model
                'attribute' => "name", // foreign key attribute that is shown to user
                'model' => "App\Models\EvaluationType", // foreign key model
             ],


            // ENROLLMENTS COUNT
            [
            'name' => "enrollments",
            'label' => "Enrollments", // Table column heading
            'type' => "model_function",
            'function_name' => 'getEnrollmentsCountAttribute', // the method in your Model
            // 'limit' => 100, // Limit the number of characters shown
            ],

            [
                'name' => "start_date", // The db column name
                'label' => "Start Date", // Table column heading
                'type' => "date",
                 // 'format' => 'l j F Y', // use something else than the base.default_date_format config value
             ],

             [
                'name' => "end_date", // The db column name
                'label' => "End Date", // Table column heading
                'type' => "date",
                 // 'format' => 'l j F Y', // use something else than the base.default_date_format config value
             ],
             

        ]);


        $this->crud->addFilter([ // select2 filter
            'name' => 'campus_id',
            'type' => 'select2',
            'label'=> 'Campus'
          ], function() {
              return \App\Models\Campus::all()->pluck('name', 'id')->toArray();
          }, function($value) { // if the filter is active
                  $this->crud->addClause('where', 'campus_id', $value);
          },
          function () { // if the filter is NOT active (the GET parameter "checkbox" does not exit)
            $this->crud->addClause('where', 'campus_id', '1');
            $this->crud->request->request->add(['campus_id' => 1]); // to make the filter look active
        });

        $this->crud->addFilter([ // select2 filter
            'name' => 'period_id',
            'type' => 'select2',
            'label'=> 'Period'
          ], function() {
              return \App\Models\Period::all()->pluck('name', 'id')->toArray();
          }, function($value) { // if the filter is active
                  $this->crud->addClause('where', 'period_id', $value);
          },
          function () { // if the filter is NOT active (the GET parameter "checkbox" does not exit)
            $period = \App\Models\Period::get_default_period()->id;
            $this->crud->addClause('where', 'period_id', $period);
            $this->crud->request->request->add(['period_id' => $period]); // to make the filter look active
        });



        $this->crud->addFields([
            [
                // RYTHM
                'label' => "Rythm", // Table column heading
                'type' => "select",
                'name' => 'rythm_id', // the column that contains the ID of that connected entity;
                'entity' => 'rythm', // the method that defines the relationship in your Model
                'attribute' => "name", // foreign key attribute that is shown to user
                'model' => "App\Models\Rythm", // foreign key model
                'tab' => 'Course info'

             ],

             [
                // LEVEL
                'label' => "Level", // Table column heading
                'type' => "select",
                'name' => 'level_id', // the column that contains the ID of that connected entity;
                'entity' => 'level', // the method that defines the relationship in your Model
                'attribute' => "name", // foreign key attribute that is shown to user
                'model' => "App\Models\Level", // foreign key model
                'tab' => 'Course info'
             ],

             [
                'name' => 'name', // The db column name
                'label' => "Name", // Table column heading
                'tab' => 'Course info'
             ],

             [
                'name' => 'volume', // The db column name
                'label' => "Volume", // Table column heading
                'suffix' => "h",
                'tab' => 'Course info'
             ],

             [
                // TEACHER
                'label' => "Teacher", // Table column heading
                'type' => "select",
                'name' => 'teacher_id', // the column that contains the ID of that connected entity;
                'entity' => 'teacher', // the method that defines the relationship in your Model
                'attribute' => "name", // foreign key attribute that is shown to user
                'model' => "App\Models\User", // foreign key model
                'tab' => 'Resources'
             ],

             [
                // ROOM
                'label' => "Room", // Table column heading
                'type' => "select",
                'name' => 'room_id', // the column that contains the ID of that connected entity;
                'entity' => 'room', // the method that defines the relationship in your Model
                'attribute' => "name", // foreign key attribute that is shown to user
                'model' => "App\Models\Room", // foreign key model
                'tab' => 'Resources'

             ],


             // EVALUATION METHODS
             [
                // n-n relationship (with pivot table)
                'label' => "Evaluation method", // Table column heading
                'type' => "select_multiple",
                'name' => 'evaluation_type', // the method that defines the relationship in your Model
                'entity' => 'evaluation_type', // the method that defines the relationship in your Model
                'attribute' => "name", // foreign key attribute that is shown to user
                'model' => "App\Models\EvaluationType", // foreign key model
                'pivot' => true, // on create&update, do you need to add/delete pivot table entries?
                'tab' => 'Pedagogy'

             ],


            [
                'name' => "start_date", // The db column name
                'label' => "Start Date", // Table column heading
                'type' => "date",
                 // 'format' => 'l j F Y', // use something else than the base.default_date_format config value
                 'tab' => 'Schedule'

             ],

             [
                'name' => "end_date", // The db column name
                'label' => "End Date", // Table column heading
                'type' => "date",
                 // 'format' => 'l j F Y', // use something else than the base.default_date_format config value
                 'tab' => 'Schedule'

             ],

             [
                // Custom Field
                'name' => 'coursetime',
                'label' => 'Course Events',
                'related_model' => 'times', // the relationship on the main model
                'type' => 'coursetime'
                /// 'view_namespace' => 'yourpackage' // use a custom namespace of your package to load views within a custom view folder.
              ]
             

        ]);


        // add asterisk for fields that are required in CourseRequest
        $this->crud->setRequiredFields(StoreRequest::class, 'create');
        $this->crud->setRequiredFields(UpdateRequest::class, 'edit');
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
