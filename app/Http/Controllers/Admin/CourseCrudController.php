<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\CourseRequest as StoreRequest;
// VALIDATION: change the requests to match your own file names if you need form validation
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Event;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;
use Illuminate\Support\Facades\Gate;

/**
 * Class CourseCrudController.
 * @property-read CrudPanel $crud
 */
class CourseCrudController extends CrudController
{
    use \Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\CloneOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;

    public function __construct()
    {
        parent::__construct();
        $this->middleware('permission:courses.view', ['except' => 'show']);
        $this->middleware('permission:courses.edit', ['only' => ['update', 'create', 'store', 'destroy']]);
    }

    public function setup()
    {
        /*
        |--------------------------------------------------------------------------
        | CrudPanel Basic Information
        |--------------------------------------------------------------------------
        */
        CRUD::setModel(\App\Models\Course::class);
        CRUD::setRoute(config('backpack.base.route_prefix').'/course');
        CRUD::setEntityNameStrings(__('course'), __('courses'));

        CRUD::addClause('internal');

        $permissions = backpack_user()->getAllPermissions();

        if (! $permissions->contains('name', 'courses.edit')) {
            CRUD::denyAccess('update');
        }
        if ($permissions->contains('name', 'courses.edit')) {
            CRUD::allowAccess('clone');
        }
        if (! $permissions->contains('name', 'courses.edit')) {
            CRUD::denyAccess('create');
            CRUD::denyAccess('clone');
        }

        if ($permissions->contains('name', 'courses.view')) {
            CRUD::allowAccess('show');
        }

        if ($permissions->contains('name', 'courses.edit')) {
            CRUD::addButtonFromView('line', 'schedule', 'schedule', 'end');
        }

        CRUD::addButtonFromView('line', 'children_badge', 'children_badge', 'beginning');

        if ($permissions->contains('name', 'courses.delete')) {
            CRUD::addButtonFromView('line', 'deleteCourse', 'deleteCourse', 'end');
        } else {
            CRUD::denyAccess('delete');
        }

        if (backpack_user()->hasRole('admin')) {
            CRUD::enableExportButtons();
        }
    }

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

            // ENROLLMENTS COUNT
            [
                'name' => 'enrollments',
                'label' => __('Enrollments'), // Table column heading
                'type' => 'model_function',
                'function_name' => 'getCourseEnrollmentsCountAttribute', // the method in your Model
                // 'limit' => 100, // Limit the number of characters shown
            ],

            [
                'name' => 'start_date', // The db column name
                'label' => __('Start Date'), // Table column heading
                'type' => 'date',
                // 'format' => 'l j F Y', // use something else than the base.default_date_format config value
            ],

            [
                'name' => 'end_date', // The db column name
                'label' => __('End Date'), // Table column heading
                'type' => 'date',
                // 'format' => 'l j F Y', // use something else than the base.default_date_format config value
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
            return \App\Models\Period::all()->sortByDesc('id')->pluck('name', 'id')->toArray();
        }, function ($value) { // if the filter is active
            CRUD::addClause('where', 'period_id', $value);
        },
          function () { // if the filter is NOT active (the GET parameter "checkbox" does not exit)
              $period = \App\Models\Period::get_default_period()->id;
              CRUD::addClause('where', 'period_id', $period);
              //$this->crud->request->request->add(['period_id' => $period]); // to make the filter look active
          });

        CRUD::addFilter([ // add a "simple" filter called Draft
            'type' => 'simple',
            'name' => 'parent',
            'label'=> __('Hide Children Courses'),
        ],
          false,
          function () {
              CRUD::addClause('parent');
          });
    }

    protected function setupCreateOperation()
    {
        CRUD::addFields([
            [
                // RYTHM
                'label' => __('Rhythm'), // Table column heading
                'type' => 'select',
                'name' => 'rhythm_id', // the column that contains the ID of that connected entity;
                'entity' => 'rhythm', // the method that defines the relationship in your Model
                'attribute' => 'name', // foreign key attribute that is shown to user
                'model' => \App\Models\Rhythm::class, // foreign key model
                'tab' => __('Course info'),
            ],

            [
                // LEVEL
                'label' => __('Level'), // Table column heading
                'type' => 'select',
                'name' => 'level_id', // the column that contains the ID of that connected entity;
                'entity' => 'level', // the method that defines the relationship in your Model
                'attribute' => 'name', // foreign key attribute that is shown to user
                'model' => \App\Models\Level::class, // foreign key model
                'tab' => __('Course info'),
            ],

            [
                'name' => 'name', // The db column name
                'label' => __('Name'), // Table column heading
                'tab' => __('Course info'),
            ],

            [
                'name' => 'price', // The db column name
                'label' => __('Price'), // Table column heading
                'tab' => __('Course info'),
            ],

            [
                'name' => 'volume', // The db column name
                'label' => __('Volume'), // Table column heading
                'suffix' => 'h',
                'tab' => __('Course info'),
            ],

            [
                'name' => 'spots', // The db column name
                'label' => __('Spots'), // Table column heading
                'tab' => __('Course info'),
            ],

            [
                'name' => 'exempt_attendance', // The db column name
                'label' => __('Exempt Attendance'), // Table column heading
                'type' => 'checkbox',
                'tab' => __('Course info'),
            ],

            [
                // TEACHER
                'label' => __('Teacher'), // Table column heading
                'type' => 'select',
                'name' => 'teacher_id', // the column that contains the ID of that connected entity;
                'entity' => 'teacher', // the method that defines the relationship in your Model
                'attribute' => 'name', // foreign key attribute that is shown to user
                'model' => \App\Models\Teacher::class, // foreign key model
                'tab' => __('Resources'),
            ],

            [
                // ROOM
                'label' => __('Room'), // Table column heading
                'type' => 'select',
                'name' => 'room_id', // the column that contains the ID of that connected entity;
                'entity' => 'room', // the method that defines the relationship in your Model
                'attribute' => 'name', // foreign key attribute that is shown to user
                'model' => \App\Models\Room::class, // foreign key model
                'tab' => __('Resources'),
            ],

            [
                // RYTHM
                'label' => __('Campus'), // Table column heading
                'type' => 'hidden',
                'name' => 'campus_id', // the column that contains the ID of that connected entity;
                'value' => 1,
            ],

            [
                // n-n relationship (with pivot table)
                'label' => __('Books'), // Table column heading
                'type' => 'select_multiple',
                'name' => 'books', // the method that defines the relationship in your Model
                'entity' => 'books', // the method that defines the relationship in your Model
                'attribute' => 'name', // foreign key attribute that is shown to user
                'model' => \App\Models\Book::class, // foreign key model
                'pivot' => true, // on create&update, do you need to add/delete pivot table entries?
                'tab' => __('Pedagogy'),

            ],

            [
                // PERIOD
                'label' => __('Period'), // Table column heading
                'type' => 'select',
                'name' => 'period_id', // the column that contains the ID of that connected entity;
                'entity' => 'period', // the method that defines the relationship in your Model
                'attribute' => 'name', // foreign key attribute that is shown to user
                'model' => \App\Models\Period::class, // foreign key model
                'tab' => __('Schedule'),
            ],

            [
                'name' => 'start_date', // The db column name
                'label' => __('Start Date'),
                'type' => 'date',
                // 'format' => 'l j F Y', // use something else than the base.default_date_format config value
                'tab' => __('Schedule'),

            ],

            [
                'name' => 'end_date', // The db column name
                'label' => __('End Date'), // Table column heading
                'type' => 'date',
                // 'format' => 'l j F Y', // use something else than the base.default_date_format config value
                'tab' => __('Schedule'),

            ],

        ]);

        // add asterisk for fields that are required in CourseRequest
        CRUD::setValidation(StoreRequest::class);
    }

    protected function setupUpdateOperation()
    {
        $this->setupCreateOperation(); // since this calls the methods above, no need to do anything here
    }

    public function show($course)
    {
        $course = Course::findOrFail($course);

        // The current is not allowed to view the page
        if (Gate::forUser(backpack_user())->denies('view-course', $course)) {
            abort(403);
        }

        $enrollments = $course->enrollments()->with('student')->get();

        return view('courses/show', compact('course', 'enrollments'));
    }

    /*
    * Allow to create a child course for the selected parent
    * This method is temporary and should be improved in the future
    */
    public function clone($id)
    {
        CRUD::hasAccessOrFail('clone');
        CRUD::setOperation('clone');

        $course = Course::findOrFail($id);

        // clone the course
        $child_course = Course::create([
            'campus_id' => $course->campus_id,
            'rhythm_id' => $course->rhythm_id,
            'level_id' => $course->level_id,
            'volume' => $course->volume,
            'name' => $course->name,
            'price' => $course->price,
            'start_date' => $course->start_date,
            'end_date' => $course->end_date,
            'room_id' => $course->room_id,
            'teacher_id' => $course->teacher_id,
            'parent_course_id' => $course->id,
            'exempt_attendance' => $course->exempt_attendance,
            'period_id' => $course->period_id,
            'opened' => $course->opened,
            'spots' => $course->spots,
        ]);

        // TODO migrate the coursetime events

        // TODO the evaluation methods

        // TODO and generate the events

        // TODO delete relations linked to the parent course (evaluation, etc)

        // TODO return the id of the newly created course. The front-end will redirect to the edit screen for this course

        return $child_course->id;
    }

    public function destroy($id)
    {
        CRUD::hasAccessOrFail('delete');
        $course = Course::find($id);
        if ($course->enrollments()->count() > 0) {
            \Alert::add('error', 'The course has enrollments, impossible to delete');
        } else {
            Event::where('course_id', $id)->delete();
            Enrollment::where('course_id', $id)->delete();

            return CRUD::delete($id);
        }
    }
}
