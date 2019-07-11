<?php

namespace App\Http\Controllers\Admin;

use Backpack\CRUD\app\Http\Controllers\CrudController;

// VALIDATION: change the requests to match your own file names if you need form validation
use App\Http\Requests\ExternalCourseRequest as StoreRequest;
use App\Http\Requests\ExternalCourseRequest as UpdateRequest;
use Backpack\CRUD\CrudPanel;

/**
 * Class ExternalCourseCrudController
 * @package App\Http\Controllers\Admin
 * @property-read CrudPanel $crud
 */
class ExternalCourseCrudController extends CrudController
{

    public function __construct()
    {
        parent::__construct();
        $this->middleware('permission:courses.edit');
    }

    public function setup()
    {
        /*
        |--------------------------------------------------------------------------
        | CrudPanel Basic Information
        |--------------------------------------------------------------------------
        */
        $this->crud->setModel('App\Models\ExternalCourse');
        $this->crud->setRoute(config('backpack.base.route_prefix') . '/externalcourse');
        $this->crud->setEntityNameStrings('externalcourse', 'external_courses');

        $this->crud->enableExportButtons();

        /*
        |--------------------------------------------------------------------------
        | CrudPanel Configuration
        |--------------------------------------------------------------------------
        */



        $this->crud->setColumns([
            [
            // RYTHM
            'label' => __('Rhythm'), // Table column heading
            'type' => "select",
            'name' => 'rhythm_id', // the column that contains the ID of that connected entity;
            'entity' => 'rhythm', // the method that defines the relationship in your Model
            'attribute' => "name", // foreign key attribute that is shown to user
            'model' => "App\Models\Rhythm", // foreign key model
            ],

            [
            // LEVEL
            'label' => __('Level'), // Table column heading
            'type' => "select",
            'name' => 'level_id', // the column that contains the ID of that connected entity;
            'entity' => 'level', // the method that defines the relationship in your Model
            'attribute' => "name", // foreign key attribute that is shown to user
            'model' => "App\Models\Level", // foreign key model
            ],

            [
            'name' => 'name', // The db column name
            'label' => __('Name'), // Table column heading
            ],

            [
            'name' => 'volume', // The db column name
            'label' => __('Volume'), // Table column heading
            'suffix' => "h",
            ],

            [
            // TEACHER
            'label' => __('Teacher'), // Table column heading
            'type' => "select",
            'name' => 'teacher_id', // the column that contains the ID of that connected entity;
            'entity' => 'teacher', // the method that defines the relationship in your Model
            'attribute' => "name", // foreign key attribute that is shown to user
            'model' => "App\Models\Teacher", // foreign key model
            ],

            [
            // ROOM
            'label' => __("Room"), // Table column heading
            'type' => "select",
            'name' => 'room_id', // the column that contains the ID of that connected entity;
            'entity' => 'room', // the method that defines the relationship in your Model
            'attribute' => "name", // foreign key attribute that is shown to user
            'model' => "App\Models\Room", // foreign key model
            ],

            // COURSE SCHEDULED TIMES
            [
            'name' => "times",
            'label' => __("Schedule"), // Table column heading
            'type' => "model_function",
            'function_name' => 'getCourseTimesAttribute', // the method in your Model
            'limit' => 150, // Limit the number of characters shown
            ],

            // HEAD COUNT
            [
            'name' => "head_count",
            'label' => __("Students"),
            ],

            // HEAD COUNT
            [
                'name' => "new_students",
                'label' => __("Year Students"),
            ],

            [
            'name' => "start_date", // The db column name
            'label' => __("Start Date"), // Table column heading
            'type' => "date",
            ],

            [
            'name' => "end_date", // The db column name
            'label' => __("End Date"), // Table column heading
            'type' => "date",
            ],
             
        ]);


        $this->crud->addFilter([ // select2 filter
            'name' => 'rhythm_id',
            'type' => 'select2',
            'label'=> __('Rhythm')
          ], function() {
              return \App\Models\Rhythm::all()->pluck('name', 'id')->toArray();
          }, function($value) { // if the filter is active
                  $this->crud->addClause('where', 'rhythm_id', $value);
          },
          function () { // if the filter is NOT active (the GET parameter "checkbox" does not exit)
            
        });


        $this->crud->addFilter([ // select2 filter
            'name' => 'teacher_id',
            'type' => 'select2',
            'label'=> __('Teacher')
          ], function() {
              return \App\Models\Teacher::all()->pluck('name', 'id')->toArray();
          }, function($value) { // if the filter is active
                  $this->crud->addClause('where', 'teacher_id', $value);
          },
          function () { // if the filter is NOT active (the GET parameter "checkbox" does not exit)
            
        });

        $this->crud->addFilter([ // select2 filter
            'name' => 'level_id',
            'type' => 'select2',
            'label'=> __('Level')
          ], function() {
              return \App\Models\Level::all()->pluck('name', 'id')->toArray();
          }, function($value) { // if the filter is active
                  $this->crud->addClause('where', 'level_id', $value);
          },
          function () { // if the filter is NOT active (the GET parameter "checkbox" does not exit)
        });

        $this->crud->addFilter([ // select2 filter
            'name' => 'period_id',
            'type' => 'select2',
            'label'=> __('Period')
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
                'label' => __("Rhythm"), // Table column heading
                'type' => "select",
                'name' => 'rhythm_id', // the column that contains the ID of that connected entity;
                'entity' => 'rhythm', // the method that defines the relationship in your Model
                'attribute' => "name", // foreign key attribute that is shown to user
                'model' => "App\Models\Rhythm", // foreign key model
             ],

             [
                // LEVEL
                'label' => __("Level"), // Table column heading
                'type' => "select",
                'name' => 'level_id', // the column that contains the ID of that connected entity;
                'entity' => 'level', // the method that defines the relationship in your Model
                'attribute' => "name", // foreign key attribute that is shown to user
                'model' => "App\Models\Level", // foreign key model
             ],

             [
                'name' => 'name', // The db column name
                'label' => __("Name"), // Table column heading
             ],

             [
                'name' => 'volume', // The db column name
                'label' => __("Volume"), // Table column heading
                'suffix' => "h",
             ],

             [
                // TEACHER
                'label' => __("Teacher"), // Table column heading
                'type' => "select",
                'name' => 'teacher_id', // the column that contains the ID of that connected entity;
                'entity' => 'teacher', // the method that defines the relationship in your Model
                'attribute' => "name", // foreign key attribute that is shown to user
                'model' => "App\Models\Teacher", // foreign key model
             ],

             [
                // ROOM
                'label' => __("Room"), // Table column heading
                'type' => "select",
                'name' => 'room_id', // the column that contains the ID of that connected entity;
                'entity' => 'room', // the method that defines the relationship in your Model
                'attribute' => "name", // foreign key attribute that is shown to user
                'model' => "App\Models\Room", // foreign key model
             ],

             [
                // RYTHM
                'label' => __("Campus"), // Table column heading
                'type' => "hidden",
                'name' => 'campus_id', // the column that contains the ID of that connected entity;
                'value' => 2,
             ],

             [
                'name' => 'price', // The db column name
                'type' => "hidden",
                'value' => 0,
             ],


             [
                // PERIOD
                'label' => __("Period"), // Table column heading
                'type' => "select",
                'name' => 'period_id', // the column that contains the ID of that connected entity;
                'entity' => 'period', // the method that defines the relationship in your Model
                'attribute' => "name", // foreign key attribute that is shown to user
                'model' => "App\Models\Period", // foreign key model
             ],

            [
                'name' => "start_date", // The db column name
                'label' => __("Start Date"),
                'type' => "date",

             ],

             [
                'name' => "end_date", // The db column name
                'label' => __("End Date"), // Table column heading
                'type' => "date",
             ],

             [
                'name' => "head_count", // The db column name
                'label' => __("Head Count"), // Table column heading
                'type' => "number",
             ],

             [
                'name' => "new_students", // The db column name
                'label' => __("Students to count in year total"), // Table column heading
                'type' => "number",
             ],

        ]);

        // add asterisk for fields that are required in ExternalCourseRequest
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
