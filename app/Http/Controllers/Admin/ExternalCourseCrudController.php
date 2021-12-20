<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\ExternalCourseRequest as StoreRequest;
use App\Http\Requests\ExternalCourseRequest as UpdateRequest;
use App\Models\Course;
use App\Models\Level;
use App\Models\Partner;
use App\Models\Period;
use App\Models\Rhythm;
use App\Models\Room;
use App\Models\SchedulePreset;
use App\Models\Teacher;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
use Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;
use Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
use Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;
use Backpack\CRUD\CrudPanel;

/**
 * Class ExternalCourseCrudController.
 * @property-read CrudPanel $crud
 */
class ExternalCourseCrudController extends CrudController
{
    use ListOperation;
    use CreateOperation { store as traitStore; }
    use UpdateOperation { update as traitUpdate; }
    use DeleteOperation;

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
        CRUD::setModel(Course::class);
        CRUD::setRoute(config('backpack.base.route_prefix').'/externalcourse');
        CRUD::setEntityNameStrings(__('External Course'), __('External Courses'));
        CRUD::addClause('external');
        $permissions = backpack_user()->getAllPermissions();

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
                'label' => __('Partnership'), // Table column heading
                'type' => 'select',
                'name' => 'partner_id', // the column that contains the ID of that connected entity;
                'entity' => 'partner', // the method that defines the relationship in your Model
                'attribute' => 'name', // foreign key attribute that is shown to user
                'model' => Partner::class, // foreign key model
            ],

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
                'type' => 'number',
            ],

            [
                'name' => 'hourly_price', // The db column name
                'label' => __('Hourly Price'), // Table column heading
                'prefix' => '$',
                'type' => 'number',
            ],

            [
                // TEACHER
                'label' => __('Teacher'), // Table column heading
                'type' => 'select',
                'name' => 'teacher_id', // the column that contains the ID of that connected entity;
                'entity' => 'teacher', // the method that defines the relationship in your Model
                'attribute' => 'name', // foreign key attribute that is shown to user
                'model' => Teacher::class, // foreign key model
                'searchLogic' => false,
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

        CRUD::addFilter(
            [ // select2 filter
                'name' => 'rhythm_id',
                'type' => 'select2',
                'label'=> __('Rhythm'),
            ],
            function () {
                return Rhythm::all()->pluck('name', 'id')->toArray();
            },
            function ($value) { // if the filter is active
                CRUD::addClause('where', 'rhythm_id', $value);
            },
            function () { // if the filter is NOT active (the GET parameter "checkbox" does not exit)
            }
        );

        CRUD::addFilter(
            [ // select2 filter
                'name' => 'teacher_id',
                'type' => 'select2',
                'label'=> __('Teacher'),
            ],
            function () {
                return Teacher::all()->pluck('name', 'id')->toArray();
            },
            function ($value) { // if the filter is active
                CRUD::addClause('where', 'teacher_id', $value);
            },
            function () { // if the filter is NOT active (the GET parameter "checkbox" does not exit)
            }
        );

        CRUD::addFilter(
            [ // select2 filter
                'name' => 'level_id',
                'type' => 'select2',
                'label'=> __('Level'),
            ],
            function () {
                return Level::all()->pluck('name', 'id')->toArray();
            },
            function ($value) { // if the filter is active
                CRUD::addClause('where', 'level_id', $value);
            },
            function () { // if the filter is NOT active (the GET parameter "checkbox" does not exit)
            }
        );

        CRUD::addFilter(
            [ // select2 filter
                'name' => 'period_id',
                'type' => 'select2',
                'label'=> __('Period'),
            ],
            function () {
                return Period::all()->pluck('name', 'id')->toArray();
            },
            function ($value) { // if the filter is active
                CRUD::addClause('where', 'period_id', $value);
            },
            function () { // if the filter is NOT active (the GET parameter "checkbox" does not exit)
                $period = Period::get_default_period()->id;
                CRUD::addClause('where', 'period_id', $period);
                $this->crud->getRequest()->request->add(['period_id' => $period]); // to make the filter look active
            }
        );
    }

    protected function setupCreateOperation()
    {
        CRUD::setValidation(StoreRequest::class);

        CRUD::addFields([
            [
                // RYTHM
                'label' => __('Partnership'), // Table column heading
                'type' => 'select',
                'name' => 'partner_id', // the column that contains the ID of that connected entity;
                'entity' => 'partner', // the method that defines the relationship in your Model
                'attribute' => 'name', // foreign key attribute that is shown to user
                'model' => Partner::class, // foreign key model
                'tab' => __('Course info'),
            ],

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
                'name' => 'volume', // The db column name
                'label' => __('Volume'), // Table column heading
                'suffix' => 'h',
                'tab' => __('Course info'),
            ],

            [
                'name' => 'hourly_price', // The db column name
                'label' => __('Hourly Price'), // Table column heading
                'prefix' => '$',
                'tab' => __('Course info'),
            ],

            [
                // TEACHER
                'label' => __('Teacher'), // Table column heading
                'type' => 'select',
                'name' => 'teacher_id', // the column that contains the ID of that connected entity;
                'entity' => 'teacher', // the method that defines the relationship in your Model
                'attribute' => 'name', // foreign key attribute that is shown to user
                'model' => Teacher::class, // foreign key model
                'tab' => __('Course info'),
            ],

            [
                // ROOM
                'label' => __('Room'), // Table column heading
                'type' => 'select',
                'name' => 'room_id', // the column that contains the ID of that connected entity;
                'entity' => 'room', // the method that defines the relationship in your Model
                'attribute' => 'name', // foreign key attribute that is shown to user
                'model' => Room::class, // foreign key model
                'tab' => __('Course info'),
            ],

            [
                // RYTHM
                'label' => __('Campus'), // Table column heading
                'type' => 'hidden',
                'name' => 'campus_id', // the column that contains the ID of that connected entity;
                'value' => 2,
                'tab' => __('Course info'),
            ],

            [
                'name' => 'price', // The db column name
                'type' => 'hidden',
                'value' => 0,
                'tab' => __('Course info'),
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
                'tab' => __('Schedule'),
                'default' => Period::get_enrollments_period()->start,
            ],

            [
                'name' => 'end_date', // The db column name
                'label' => __('End Date'), // Table column heading
                'type' => 'date',
                'tab' => __('Schedule'),
                'default' => Period::get_enrollments_period()->end,
            ],

            [
                'name' => 'head_count', // The db column name
                'label' => __('Head Count'), // Table column heading
                'type' => 'number',
                'tab' => __('Course info'),
            ],

            [
                'name' => 'new_students', // The db column name
                'label' => __('Students to count in year total'), // Table column heading
                'type' => 'number',
                'tab' => __('Course info'),
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
                'init_rows' => 0,
                'tab' => __('Schedule'),
            ],

            [   // view
                'name' => 'custom-ajax-button',
                'type' => 'view',
                'view' => 'courses/schedule-preset-alert',
                'tab' => __('Schedule'),
            ],
        ]);

        CRUD::addField([
            'name'        => 'schedulepreset',
            'label'       => __('Schedule Preset'),
            'type'        => 'select_from_array',
            'options'     => array_column(SchedulePreset::all()->toArray(), 'name', 'presets'),
            'allows_null' => true,
            'tab' => __('Schedule'),
        ]);
    }

    protected function setupUpdateOperation()
    {
        CRUD::addFields([
            [
                // RYTHM
                'label' => __('Partnership'), // Table column heading
                'type' => 'select',
                'name' => 'partner_id', // the column that contains the ID of that connected entity;
                'entity' => 'partner', // the method that defines the relationship in your Model
                'attribute' => 'name', // foreign key attribute that is shown to user
                'model' => Partner::class, // foreign key model
                'tab' => __('Course info'),
            ],

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
                'name' => 'volume', // The db column name
                'label' => __('Volume'), // Table column heading
                'suffix' => 'h',
                'tab' => __('Course info'),
            ],

            [
                'name' => 'hourly_price', // The db column name
                'label' => __('Hourly Price'), // Table column heading
                'prefix' => '$',
                'tab' => __('Course info'),
            ],

            [
                // TEACHER
                'label' => __('Teacher'), // Table column heading
                'type' => 'select',
                'name' => 'teacher_id', // the column that contains the ID of that connected entity;
                'entity' => 'teacher', // the method that defines the relationship in your Model
                'attribute' => 'name', // foreign key attribute that is shown to user
                'model' => Teacher::class, // foreign key model
                'tab' => __('Course info'),
            ],

            [
                // ROOM
                'label' => __('Room'), // Table column heading
                'type' => 'select',
                'name' => 'room_id', // the column that contains the ID of that connected entity;
                'entity' => 'room', // the method that defines the relationship in your Model
                'attribute' => 'name', // foreign key attribute that is shown to user
                'model' => Room::class, // foreign key model
                'tab' => __('Course info'),
            ],

            [
                // RYTHM
                'label' => __('Campus'), // Table column heading
                'type' => 'hidden',
                'name' => 'campus_id', // the column that contains the ID of that connected entity;
                'value' => 2,
                'tab' => __('Course info'),
            ],

            [
                'name' => 'price', // The db column name
                'type' => 'hidden',
                'value' => 0,
                'tab' => __('Course info'),
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
                'tab' => __('Schedule'),
                'default' => Period::get_enrollments_period()->start,
            ],

            [
                'name' => 'end_date', // The db column name
                'label' => __('End Date'), // Table column heading
                'type' => 'date',
                'tab' => __('Schedule'),
                'default' => Period::get_enrollments_period()->end,
            ],

            [
                'name' => 'head_count', // The db column name
                'label' => __('Head Count'), // Table column heading
                'type' => 'number',
                'tab' => __('Course info'),
            ],

            [
                'name' => 'new_students', // The db column name
                'label' => __('Students to count in year total'), // Table column heading
                'type' => 'number',
                'tab' => __('Course info'),
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
                'init_rows' => 0,
                'tab' => __('Schedule'),
            ],
        ]);

        CRUD::setValidation(UpdateRequest::class);
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
        // if a schedule preset was applied, use it
        if ($this->crud->getRequest()->input('schedulepreset') !== null) {
            $courseTimes = collect(json_decode($this->crud->getRequest()->input('schedulepreset')));
        } else {
            // otherwise, use any user-defined course times
            $courseTimes = collect(json_decode($this->crud->getRequest()->input('times')));
        }

        $response = $this->traitStore();
        $course = $this->crud->getCurrentEntry();

        // apply course times to the parent.
        $course->saveCourseTimes($courseTimes);

        return $response;
    }
}
