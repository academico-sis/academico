<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\ExternalCourseRequest as StoreRequest;
use App\Http\Requests\ExternalCourseRequest as UpdateRequest;
// VALIDATION: change the requests to match your own file names if you need form validation
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;
use Backpack\CRUD\CrudPanel;

/**
 * Class ExternalCourseCrudController.
 * @property-read CrudPanel $crud
 */
class ExternalCourseCrudController extends CrudController
{
    use \Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;

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
        CRUD::setModel(\App\Models\ExternalCourse::class);
        CRUD::setRoute(config('backpack.base.route_prefix').'/externalcourse');
        CRUD::setEntityNameStrings('External Course', 'External Courses');
        $permissions = backpack_user()->getAllPermissions();
        if ($permissions->contains('name', 'courses.edit')) {
            CRUD::addButtonFromView('line', 'schedule', 'schedule', 'end');
        }

        CRUD::enableExportButtons();
    }

    /*
    |--------------------------------------------------------------------------
    | CrudPanel Configuration
    |--------------------------------------------------------------------------
    */

    protected function setupListOperation()
    {
        CRUD::setColumns([
            [
                // RYTHM
                'label' => __('Rhythm'), // Table column heading
                'type' => 'select',
                'name' => 'rhythm_id', // the column that contains the ID of that connected entity;
                'entity' => 'rhythm', // the method that defines the relationship in your Model
                'attribute' => 'name', // foreign key attribute that is shown to user
                'model' => \App\Models\Rhythm::class, // foreign key model
            ],

            [
                // LEVEL
                'label' => __('Level'), // Table column heading
                'type' => 'select',
                'name' => 'level_id', // the column that contains the ID of that connected entity;
                'entity' => 'level', // the method that defines the relationship in your Model
                'attribute' => 'name', // foreign key attribute that is shown to user
                'model' => \App\Models\Level::class, // foreign key model
            ],

            [
                'name' => 'name', // The db column name
                'label' => __('Name'), // Table column heading
            ],

            [
                'name' => 'volume', // The db column name
                'label' => __('Volume'), // Table column heading
                'suffix' => 'h',
            ],

            [
                // TEACHER
                'label' => __('Teacher'), // Table column heading
                'type' => 'select',
                'name' => 'teacher_id', // the column that contains the ID of that connected entity;
                'entity' => 'teacher', // the method that defines the relationship in your Model
                'attribute' => 'name', // foreign key attribute that is shown to user
                'model' => \App\Models\Teacher::class, // foreign key model
            ],

            [
                // ROOM
                'label' => __('Room'), // Table column heading
                'type' => 'select',
                'name' => 'room_id', // the column that contains the ID of that connected entity;
                'entity' => 'room', // the method that defines the relationship in your Model
                'attribute' => 'name', // foreign key attribute that is shown to user
                'model' => \App\Models\Room::class, // foreign key model
            ],

            // COURSE SCHEDULED TIMES
            [
                'name' => 'times',
                'label' => __('Schedule'), // Table column heading
                'type' => 'model_function',
                'function_name' => 'getCourseTimesAttribute', // the method in your Model
                'limit' => 150, // Limit the number of characters shown
            ],

            // HEAD COUNT
            [
                'name' => 'head_count',
                'label' => __('Students'),
            ],

            // HEAD COUNT
            [
                'name' => 'new_students',
                'label' => __('Year Students'),
            ],

            [
                'name' => 'start_date', // The db column name
                'label' => __('Start Date'), // Table column heading
                'type' => 'date',
            ],

            [
                'name' => 'end_date', // The db column name
                'label' => __('End Date'), // Table column heading
                'type' => 'date',
            ],

        ]);

        CRUD::addFilter([ // select2 filter
            'name' => 'rhythm_id',
            'type' => 'select2',
            'label'=> __('Rhythm'),
        ], function () {
            return \App\Models\Rhythm::all()->pluck('name', 'id')->toArray();
        }, function ($value) { // if the filter is active
            CRUD::addClause('where', 'rhythm_id', $value);
        },
          function () { // if the filter is NOT active (the GET parameter "checkbox" does not exit)
          });

        CRUD::addFilter([ // select2 filter
            'name' => 'teacher_id',
            'type' => 'select2',
            'label'=> __('Teacher'),
        ], function () {
            return \App\Models\Teacher::all()->pluck('name', 'id')->toArray();
        }, function ($value) { // if the filter is active
            CRUD::addClause('where', 'teacher_id', $value);
        },
          function () { // if the filter is NOT active (the GET parameter "checkbox" does not exit)
          });

        CRUD::addFilter([ // select2 filter
            'name' => 'level_id',
            'type' => 'select2',
            'label'=> __('Level'),
        ], function () {
            return \App\Models\Level::all()->pluck('name', 'id')->toArray();
        }, function ($value) { // if the filter is active
            CRUD::addClause('where', 'level_id', $value);
        },
          function () { // if the filter is NOT active (the GET parameter "checkbox" does not exit)
          });

        CRUD::addFilter([ // select2 filter
            'name' => 'period_id',
            'type' => 'select2',
            'label'=> __('Period'),
        ], function () {
            return \App\Models\Period::all()->pluck('name', 'id')->toArray();
        }, function ($value) { // if the filter is active
            CRUD::addClause('where', 'period_id', $value);
        },
          function () { // if the filter is NOT active (the GET parameter "checkbox" does not exit)
              $period = \App\Models\Period::get_default_period()->id;
              CRUD::addClause('where', 'period_id', $period);
              $this->crud->request->request->add(['period_id' => $period]); // to make the filter look active
          });
    }

    protected function setupCreateOperation()
    {
        CRUD::setValidation(StoreRequest::class);

        CRUD::addFields([
            [
                // RYTHM
                'label' => __('Rhythm'), // Table column heading
                'type' => 'select',
                'name' => 'rhythm_id', // the column that contains the ID of that connected entity;
                'entity' => 'rhythm', // the method that defines the relationship in your Model
                'attribute' => 'name', // foreign key attribute that is shown to user
                'model' => \App\Models\Rhythm::class, // foreign key model
            ],

            [
                // LEVEL
                'label' => __('Level'), // Table column heading
                'type' => 'select',
                'name' => 'level_id', // the column that contains the ID of that connected entity;
                'entity' => 'level', // the method that defines the relationship in your Model
                'attribute' => 'name', // foreign key attribute that is shown to user
                'model' => \App\Models\Level::class, // foreign key model
            ],

            [
                'name' => 'name', // The db column name
                'label' => __('Name'), // Table column heading
            ],

            [
                'name' => 'volume', // The db column name
                'label' => __('Volume'), // Table column heading
                'suffix' => 'h',
            ],

            [
                // TEACHER
                'label' => __('Teacher'), // Table column heading
                'type' => 'select',
                'name' => 'teacher_id', // the column that contains the ID of that connected entity;
                'entity' => 'teacher', // the method that defines the relationship in your Model
                'attribute' => 'name', // foreign key attribute that is shown to user
                'model' => \App\Models\Teacher::class, // foreign key model
            ],

            [
                // ROOM
                'label' => __('Room'), // Table column heading
                'type' => 'select',
                'name' => 'room_id', // the column that contains the ID of that connected entity;
                'entity' => 'room', // the method that defines the relationship in your Model
                'attribute' => 'name', // foreign key attribute that is shown to user
                'model' => \App\Models\Room::class, // foreign key model
            ],

            [
                // RYTHM
                'label' => __('Campus'), // Table column heading
                'type' => 'hidden',
                'name' => 'campus_id', // the column that contains the ID of that connected entity;
                'value' => 2,
            ],

            [
                'name' => 'price', // The db column name
                'type' => 'hidden',
                'value' => 0,
            ],

            [
                // PERIOD
                'label' => __('Period'), // Table column heading
                'type' => 'select',
                'name' => 'period_id', // the column that contains the ID of that connected entity;
                'entity' => 'period', // the method that defines the relationship in your Model
                'attribute' => 'name', // foreign key attribute that is shown to user
                'model' => \App\Models\Period::class, // foreign key model
            ],

            [
                'name' => 'start_date', // The db column name
                'label' => __('Start Date'),
                'type' => 'date',

            ],

            [
                'name' => 'end_date', // The db column name
                'label' => __('End Date'), // Table column heading
                'type' => 'date',
            ],

            [
                'name' => 'head_count', // The db column name
                'label' => __('Head Count'), // Table column heading
                'type' => 'number',
            ],

            [
                'name' => 'new_students', // The db column name
                'label' => __('Students to count in year total'), // Table column heading
                'type' => 'number',
            ],
        ]);
    }

    protected function setupUpdateOperation()
    {
        $this->setupCreateOperation();
        CRUD::setValidation(UpdateRequest::class);
    }
}
