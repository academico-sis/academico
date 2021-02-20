<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\Operations\ShowStudentPhotoRosterOperation;
use App\Http\Requests\CourseRequest as StoreRequest;
use App\Models\Book;
// VALIDATION: change the requests to match your own file names if you need form validation
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\EvaluationType;
use App\Models\Event;
use App\Models\Level;
use App\Models\Period;
use App\Models\Rhythm;
use App\Models\Room;
use App\Models\Teacher;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
use Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;
use Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
use Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation;
use Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;
use Illuminate\Support\Facades\Gate;
use Prologue\Alerts\Facades\Alert;

class CourseCrudController extends CrudController
{
    use ListOperation;
    use ShowOperation;
    use CreateOperation { store as traitStore; }
    use UpdateOperation { update as traitUpdate; }
    use DeleteOperation;
    use ShowStudentPhotoRosterOperation;

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
        CRUD::setModel(Course::class);
        CRUD::setRoute(config('backpack.base.route_prefix').'/course');
        CRUD::setEntityNameStrings(__('course'), __('courses'));

        $permissions = backpack_user()->getAllPermissions();

        if (! $permissions->contains('name', 'courses.edit')) {
            CRUD::denyAccess('update');
            CRUD::denyAccess('create');
        }

        if ($permissions->contains('name', 'courses.view')) {
            CRUD::allowAccess('show');
        }

        if (! $permissions->contains('name', 'courses.delete')) {
            CRUD::denyAccess('delete');
        }

        if (backpack_user()->hasRole('admin')) {
            CRUD::enableExportButtons();
            CRUD::addButtonFromView('line', 'evaluation', 'evaluation', 'end');
        }

        CRUD::addButtonFromView('top', 'courses-view-switcher', 'courses-view-switcher', 'end');
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
                'model' => Rhythm::class, // foreign key model
            ],

            [
                // LEVEL
                'label' => __('Level'), // Table column heading
                'type' => 'select',
                'name' => 'level_id', // the column that contains the ID of that connected entity;
                'entity' => 'level', // the method that defines the relationship in your Model
                'attribute' => 'name', // foreign key attribute that is shown to user
                'model' => Level::class, // foreign key model
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
                'model' => Teacher::class, // foreign key model
            ],

            [
                // ROOM
                'label' => __('Room'), // Table column heading
                'type' => 'select',
                'name' => 'room_id', // the column that contains the ID of that connected entity;
                'entity' => 'room', // the method that defines the relationship in your Model
                'attribute' => 'name', // foreign key attribute that is shown to user
                'model' => Room::class, // foreign key model
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

        CRUD::addFilter([
            'name' => 'rhythm_id',
            'type' => 'select2',
            'label'=> __('Rhythm'),
        ], function () {
            return Rhythm::all()->pluck('name', 'id')->toArray();
        }, function ($value) {
            // if the filter is active
            CRUD::addClause('where', 'rhythm_id', $value);
        },
        function () {
            // if the filter is NOT active (the GET parameter "checkbox" does not exit)
        });

        CRUD::addFilter([
            'name' => 'teacher_id',
            'type' => 'select2',
            'label'=> __('Teacher'),
        ], function () {
            return Teacher::all()->pluck('name', 'id')->toArray();
        }, function ($value) {
            // if the filter is active
            CRUD::addClause('where', 'teacher_id', $value);
        },
        function () {
            // if the filter is NOT active (the GET parameter "checkbox" does not exit)
        });

        CRUD::addFilter([
            'name' => 'level_id',
            'type' => 'select2',
            'label'=> __('Level'),
        ], function () {
            return Level::all()->pluck('name', 'id')->toArray();
        }, function ($value) {
            // if the filter is active
            CRUD::addClause('where', 'level_id', $value);
        },
        function () {
            // if the filter is NOT active (the GET parameter "checkbox" does not exit)
        });

        CRUD::addFilter([
            'name' => 'period_id',
            'type' => 'select2',
            'label'=> __('Period'),
        ],
            function () {
            return \App\Models\Period::all()->sortByDesc('id')->pluck('name', 'id')->toArray();
        },
            function ($value) { // if the filter is active
            CRUD::addClause('where', 'period_id', $value);
        },
            function () { // if the filter is NOT active (the GET parameter "checkbox" does not exit)
              $period = \App\Models\Period::get_default_period()->id;
              CRUD::addClause('where', 'period_id', $period);
              $this->crud->getRequest()->request->add(['period_id' => $period]); // to make the filter look active
          }
        );

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
                'model' => Rhythm::class, // foreign key model
                'tab' => __('Course info'),
            ],

            [
                // LEVEL
                'label' => __('Level'), // Table column heading
                'type' => 'select',
                'name' => 'level_id', // the column that contains the ID of that connected entity;
                'entity' => 'level', // the method that defines the relationship in your Model
                'attribute' => 'name', // foreign key attribute that is shown to user
                'model' => Level::class, // foreign key model
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
        ]);

        if (config('lms.sync_to') == 'apolearn') {
            CRUD::addField([
                'name' => 'sync_to_lms', // The db column name
                'label' => __('Sync to LMS'), // Table column heading
                'type' => 'checkbox',
                'tab' => __('Course info'),
            ]);
        }

        CRUD::addFields([
            [
                'name' => 'color', // The db column name
                'label' => __('Color'), // Table column heading
                'tab' => __('Course info'),
                'type' => 'color_picker',
            ],

            [
                // TEACHER
                'label' => __('Teacher'), // Table column heading
                'type' => 'select',
                'name' => 'teacher_id', // the column that contains the ID of that connected entity;
                'entity' => 'teacher', // the method that defines the relationship in your Model
                'attribute' => 'name', // foreign key attribute that is shown to user
                'model' => Teacher::class, // foreign key model
                'tab' => __('Resources'),
            ],

            [
                // ROOM
                'label' => __('Room'), // Table column heading
                'type' => 'select',
                'name' => 'room_id', // the column that contains the ID of that connected entity;
                'entity' => 'room', // the method that defines the relationship in your Model
                'attribute' => 'name', // foreign key attribute that is shown to user
                'model' => Room::class, // foreign key model
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
                'model' => Book::class, // foreign key model
                'pivot' => true, // on create&update, do you need to add/delete pivot table entries?
                'tab' => __('Pedagogy'),
            ],

            [
                'label' => __('Evaluation method'), // Table column heading
                'type' => 'select2',
                'name' => 'evaluationType', // the method that defines the relationship in your Model
                'entity' => 'evaluationType', // the method that defines the relationship in your Model
                'attribute' => 'name', // foreign key attribute that is shown to user
                'model' => EvaluationType::class, // foreign key model
                'tab' => __('Pedagogy'),
            ],

            [
                // PERIOD
                'label' => __('Period'), // Table column heading
                'type' => 'select',
                'name' => 'period_id', // the column that contains the ID of that connected entity;
                'entity' => 'period', // the method that defines the relationship in your Model
                'attribute' => 'name', // foreign key attribute that is shown to user
                'model' => Period::class, // foreign key model
                'tab' => __('Schedule'),
                'default' => Period::get_enrollments_period()->id,
            ],

            [
                'name' => 'start_date', // The db column name
                'label' => __('Start Date'),
                'type' => 'date',
                // 'format' => 'l j F Y', // use something else than the base.default_date_format config value
                'tab' => __('Schedule'),
                'default' => Period::get_enrollments_period()->start,

            ],

            [
                'name' => 'end_date', // The db column name
                'label' => __('End Date'), // Table column heading
                'type' => 'date',
                // 'format' => 'l j F Y', // use something else than the base.default_date_format config value
                'tab' => __('Schedule'),
                'default' => Period::get_enrollments_period()->end,
            ],

            [   // repeatable
                'name'  => 'times',
                'label' => __('Course Schedule'),
                'type'  => 'repeatable',
                'fields' => [
                    [
                        'name'    => 'day',
                        'label'    => __('Day'),
                        'type'        => 'select_from_array',
                        'options'     => [
                            0 => __('Sunday'),
                            1 => __('Monday'),
                            2 => __('Tuesday'),
                            3 => __('Wednesday'),
                            4 => __('Thursday'),
                            5 => __('Friday'),
                            6 => __('Saturday'),
                        ],
                        'allows_null' => false,
                        'default'     => 1,
                        'wrapper' => ['class' => 'form-group col-md-4'],
                    ],
                    [
                        'name'    => 'start',
                        'type'    => 'time',
                        'label'   => __('Start'),
                        'wrapper' => ['class' => 'form-group col-md-4'],
                    ],
                    [
                        'name'    => 'end',
                        'type'    => 'time',
                        'label'   => __('End'),
                        'wrapper' => ['class' => 'form-group col-md-4'],
                    ],
                ],
                'init_rows' => 0, // number of empty rows to be initialized, by default 1
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

    public function update()
    {
        $course = $this->crud->getCurrentEntry();
        $newCourseTimes = collect(json_decode($this->crud->getRequest()->input('times')));
        $course->saveCourseTimes($newCourseTimes);

        // update model
        $response = $this->traitUpdate();

        return $response;
    }

    public function store()
    {
        $response = $this->traitStore();

        $course = $this->crud->getCurrentEntry();
        $newCourseTimes = collect(json_decode($this->crud->getRequest()->input('times')));
        $course->saveCourseTimes($newCourseTimes);

        return $response;
    }

    public function destroy($id)
    {
        CRUD::hasAccessOrFail('delete');
        $course = Course::find($id);
        if ($course->enrollments->count() > 0) {
            Alert::add('error', 'The course has enrollments, impossible to delete');
        }

        if ($course->enrollments->count() == 0) {
            Event::where('course_id', $id)->forceDelete();
            Enrollment::where('course_id', $id)->forceDelete();

            return CRUD::delete($id);
        }
    }
}
