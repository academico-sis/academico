<?php

namespace App\Http\Controllers\Admin;

use App\Models\Course;
// VALIDATION: change the requests to match your own file names if you need form validation
use App\Models\Rhythm;
use App\Models\Student;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

/**
 * Class CourseCrudController
 * Used to pick a course to enrol a user.
 * @property-read CrudPanel $crud
 */
class AvailableCourseCrudController extends CrudController
{
    use \Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;

    public function __construct()
    {
        parent::__construct();
        $this->middleware(['permission:enrollments.create']);
        if (\Schema::hasTable('students')) {
            $this->student = Student::find(request()->query('student')); // TODO find a better way
        }
    }

    public function setup()
    {
        /*
        |--------------------------------------------------------------------------
        | CrudPanel Basic Information
        |--------------------------------------------------------------------------
        */
        $this->crud->student = $this->student;
        if ($this->student == null) {
            abort(404); // todo transform into custom exception
        }
        CRUD::setModel('App\Models\Course');
        CRUD::setRoute(config('backpack.base.route_prefix').'/availablecourse');
        CRUD::setEntityNameStrings(__('available course'), __('available courses'));
        CRUD::addButtonFromView('line', 'enroll', 'enroll', 'end');
        CRUD::addButtonFromView('line', 'children_badge', 'children_badge', 'beginning');

        CRUD::setListView('courses.available');

        /*
        |--------------------------------------------------------------------------
        | CrudPanel Configuration
        |--------------------------------------------------------------------------
        */

        CRUD::setColumns([
            [
            // RYTHM
            'label' => __('Rhythm'),
            'type' => 'select',
            'name' => 'rhythm_id', // the column that contains the ID of that connected entity;
            'entity' => 'rhythm', // the method that defines the relationship in your Model
            'attribute' => 'name', // foreign key attribute that is shown to user
            'model' => "App\Models\Rhythm", // foreign key model
            ],

            [
            // LEVEL
            'label' => __('Level'),
            'type' => 'select',
            'name' => 'level_id', // the column that contains the ID of that connected entity;
            'entity' => 'level', // the method that defines the relationship in your Model
            'attribute' => 'name', // foreign key attribute that is shown to user
            'model' => "App\Models\Level", // foreign key model
            ],

            [
            'name' => 'name', // The db column name
            'label' => __('Name'),
            ],

            [
            'name' => 'volume', // The db column name
            'label' => __('Volume'),
            'suffix' => 'h',
            ],

            [
            // TEACHER
            'label' => __('Teacher'),
            'type' => 'select',
            'name' => 'teacher_id', // the column that contains the ID of that connected entity;
            'entity' => 'teacher', // the method that defines the relationship in your Model
            'attribute' => 'name', // foreign key attribute that is shown to user
            'model' => "App\Models\Teacher", // foreign key model
            ],

            [
            // ROOM
            'label' => __('Room'),
            'type' => 'select',
            'name' => 'room_id', // the column that contains the ID of that connected entity;
            'entity' => 'room', // the method that defines the relationship in your Model
            'attribute' => 'name', // foreign key attribute that is shown to user
            'model' => "App\Models\Room", // foreign key model
            ],

            // COURSE SCHEDULED TIMES
            [
            'name' => 'times',
            'label' => __('Schedule'),
            'type' => 'model_function',
            'function_name' => 'getCourseTimesAttribute',
            'limit' => 150, // Limit the number of characters shown
            ],

            // ENROLLMENTS COUNT
            [
            'name' => 'enrollments',
            'label' => __('Enrollments'),
            'type' => 'model_function',
            'function_name' => 'getCourseEnrollmentsCountAttribute',
            ],

            [
            'name' => 'start_date',
            'label' => __('Start Date'),
            'type' => 'date',
            ],

            [
            'name' => 'end_date',
            'label' => __('End Date'),
            'type' => 'date',
            ],

        ]);

        CRUD::addFilter([ // select2 filter
            'name' => 'campus_id',
            'type' => 'select2',
            'label'=> __('Campus'),
          ], function () {
              return \App\Models\Campus::all()->pluck('name', 'id')->toArray();
          }, function ($value) { // if the filter is active
              CRUD::addClause('where', 'campus_id', $value);
          },
          function () { // if the filter is NOT active (the GET parameter "checkbox" does not exit)
              CRUD::addClause('where', 'campus_id', '1');
              $this->crud->request->request->add(['campus_id' => 1]); // to make the filter look active
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
              $period = \App\Models\Period::get_enrollments_period()->id;
              CRUD::addClause('where', 'period_id', $period);
              $this->crud->request->request->add(['period_id' => $period]); // to make the filter look active
          });

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

        CRUD::addFilter([
            'type' => 'simple',
            'name' => 'parent',
            'label'=> __('Show Children Courses'),
          ],
          false,
          function () {
              CRUD::addClause('children');
          },
            function () {
                CRUD::addClause('parent');
            });
    }
}
