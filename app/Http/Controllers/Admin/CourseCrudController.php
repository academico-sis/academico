<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\Operations\ShowStudentPhotoRosterOperation;
use App\Http\Requests\CourseRequest;
use App\Models\Book;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\EvaluationType;
use App\Models\Event;
use App\Models\Level;
use App\Models\Period;
use App\Models\Rhythm;
use App\Models\Room;
use App\Models\SchedulePreset;
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

        CRUD::addClause('internal');
        $permissions = backpack_user()->getAllPermissions();

        if (! $permissions->contains('name', 'courses.edit')) {
            CRUD::denyAccess(['update', 'create']);
        }

        if ($permissions->contains('name', 'courses.view')) {
            CRUD::addButtonFromView('line', 'showEnrollments', 'showEnrollmentsForCourse');
        }

        CRUD::addButtonFromView('line', 'children_badge', 'children_badge', 'beginning');

        if (! $permissions->contains('name', 'courses.delete')) {
            CRUD::denyAccess(['delete']);
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
                'searchLogic' => false,
            ],

            [
                // LEVEL
                'label' => __('Level'), // Table column heading
                'type' => 'select',
                'name' => 'level_id', // the column that contains the ID of that connected entity;
                'entity' => 'level', // the method that defines the relationship in your Model
                'attribute' => 'name', // foreign key attribute that is shown to user
                'model' => Level::class, // foreign key model
                'searchLogic' => false,
            ],

            [
                'name' => 'name', // The db column name
                'label' => __('Name'), // Table column heading
            ],

            [
                'name' => 'volume', // The db column name
                'label' => __('Presential volume'), // Table column heading
                'suffix' => 'h',
                'type' => 'number',
                'searchLogic' => false,
            ],

            [
                'name' => 'remote_volume', // The db column name
                'label' => __('Remote volume'), // Table column heading
                'suffix' => 'h',
                'type' => 'number',
                'searchLogic' => false,
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
                'searchLogic' => false,
            ],

            // COURSE SCHEDULED TIMES
            [
                'name' => 'times',
                'label' => __('Schedule'), // Table column heading
                'type' => 'model_function',
                'function_name' => 'getCourseTimesAttribute', // the method in your Model
                'limit' => 150, // Limit the number of characters shown
                'searchLogic' => false,
            ],

            // ENROLLMENTS COUNT
            [
                'name' => 'enrollments',
                'label' => __('Enrollments'), // Table column heading
                'type' => 'model_function',
                'function_name' => 'getCourseEnrollmentsCountAttribute', // the method in your Model
                // 'limit' => 100, // Limit the number of characters shown
                'searchLogic' => false,
            ],

            [
                'name' => 'start_date', // The db column name
                'label' => __('Start Date'), // Table column heading
                'type' => 'date',
                // 'format' => 'l j F Y', // use something else than the base.default_date_format config value
                'searchLogic' => false,
            ],

            [
                'name' => 'end_date', // The db column name
                'label' => __('End Date'), // Table column heading
                'type' => 'date',
                // 'format' => 'l j F Y', // use something else than the base.default_date_format config value
                'searchLogic' => false,
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

        CRUD::addFilter(
            [ // add a "simple" filter called Draft
                'type' => 'simple',
                'name' => 'parent',
                'label'=> __('Hide Children Courses'),
            ],
            false,
            function () {
                CRUD::addClause('parent');
            }
        );

        $this->crud->addFilter([
            'type'  => 'date_range',
            'name'  => 'start_date',
            'label' => __('Start'),
        ],
            false,
            function ($value) { // if the filter is active, apply these constraints
                $dates = json_decode($value);
                $this->crud->addClause('where', 'start_date', '>=', $dates->from);
                $this->crud->addClause('where', 'start_date', '<=', $dates->to.' 23:59:59');
            });

        $this->crud->addFilter([
            'type'  => 'date_range',
            'name'  => 'end_date',
            'label' => __('End'),
        ],
            false,
            function ($value) { // if the filter is active, apply these constraints
                $dates = json_decode($value);
                $this->crud->addClause('where', 'end_date', '>=', $dates->from);
                $this->crud->addClause('where', 'end_date', '<=', $dates->to.' 23:59:59');
            });
    }

    protected function setupCreateOperation()
    {
        if (config('app.currency_position') === 'before') {
            $currency = ['prefix' => config('app.currency_symbol')];
        } else {
            $currency = ['suffix' => config('app.currency_symbol')];
        }

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

            array_merge([
                'name' => 'price', // The db column name
                'label' => __('Price'), // Table column heading
                'tab' => __('Course info'),
                'type' => 'number',
            ], $currency),
        ]);

        if (config('invoicing.price_categories_enabled')) {
            CRUD::addFields([
                array_merge([
                    'name' => 'price_b',
                    'label' => __('Price B'),
                    'tab' => __('Course info'), 'type' => 'number',
                ], $currency),

                array_merge([
                    'name' => 'price_c',
                    'label' => __('PriceC'),
                    'tab' => __('Course info'), 'type' => 'number',
                ], $currency),
            ]);
        }

        CRUD::addFields([
            [
                'name' => 'volume', // The db column name
                'label' => __('Presential volume'), // Table column heading
                'suffix' => 'h',
                'tab' => __('Course info'),
            ],

            [
                'name' => 'remote_volume', // The db column name
                'label' => __('Remote volume'), // Table column heading
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

            [   // repeatable
                'name'  => 'sublevels',
                'label' => __('Course sublevels'),
                'type'  => 'repeatable',
                'fields' => [
                    [
                        'name' => 'name', // The db column name
                        'label' => __('Name'), // Table column heading
                    ],
                    [
                        'name'    => 'level_id',
                        'label'    => __('Level'),
                        'type' => 'select',
                        'entity' => 'level', // the method that defines the relationship in your Model
                        'attribute' => 'name', // foreign key attribute that is shown to user
                        'model' => Level::class, // foreign key model
                        'allows_null' => true,
                        'wrapper' => ['class' => 'form-group col-md-4'],
                    ],

                    array_merge([
                        'name' => 'price', // The db column name
                        'label' => __('Price'), // Table column heading
                        'type' => 'number',
                    ], $currency),

                    [
                        'name' => 'volume', // The db column name
                        'label' => __('Presential volume'), // Table column heading
                        'suffix' => 'h',
                    ],

                    [
                        'name' => 'remote_volume', // The db column name
                        'label' => __('Remote volume'), // Table column heading
                        'suffix' => 'h',
                    ],

                    [
                        'name'    => 'start_date',
                        'type'    => 'date',
                        'label'   => __('Start Date'),
                        'wrapper' => ['class' => 'form-group col-md-4'],
                    ],
                    [
                        'name'    => 'end_date',
                        'type'    => 'date',
                        'label'   => __('End Date'),
                        'wrapper' => ['class' => 'form-group col-md-4'],
                    ],
                ],
                'tab' => __('Course sublevels'),
                'init_rows' => 0, // number of empty rows to be initialized, by default 1

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
                'init_rows' => 0,
                'tab' => __('Schedule'),
            ],

            [
                'name' => 'remoteevents',
                'label' => __('Remote events'),
                'type' => 'repeatable',
                'fields' => [
                    [
                        'name' => 'name',
                        'type' => 'text',
                        'label' => __('Name'),
                        'wrapper' => ['class' => 'form-group col-md-6'],
                    ],
                    [
                        'name' => 'worked_hours',
                        'type' => 'number',
                        'attributes' => ['step' => '0.25'],
                        'suffix'     => 'h',
                        'label' => __('Weekly Volume'),
                        'wrapper' => ['class' => 'form-group col-md-6'],
                    ],
                ],
                'tab' => __('Schedule'),
                'init_rows' => 0, // number of empty rows to be initialized, by default 1
            ],

            [   // view
                'name' => 'custom-ajax-button',
                'type' => 'view',
                'view' => 'courses/schedule-preset-alert',
                'tab' => __('Schedule'),
            ],

            [   // select_from_array
                'name'        => 'schedulepreset',
                'label'       => __('Schedule Preset'),
                'type'        => 'select_from_array',
                'options'     => array_column(SchedulePreset::all()->toArray(), 'name', 'presets'),
                'allows_null' => true,
                'tab' => __('Schedule'),
            ],

        ]);

        // add asterisk for fields that are required in CourseRequest
        CRUD::setValidation(CourseRequest::class);
    }

    protected function setupUpdateOperation()
    {
        if (config('app.currency_position') === 'before') {
            $currency = ['prefix' => config('app.currency_symbol')];
        } else {
            $currency = ['suffix' => config('app.currency_symbol')];
        }

        if ($this->crud->getCurrentEntry()->children->count() > 0) {
            CRUD::addField([   // view
                'name' => 'custom-ajax-button',
                'type' => 'view',
                'view' => 'courses/parent-course-alert',
            ]);
        }

        if ($this->crud->getCurrentEntry()->parent_course_id !== null) {
            CRUD::addField([   // view
                'name' => 'custom-ajax-button',
                'type' => 'view',
                'view' => 'courses/child-course-alert',
            ]);
        }

        CRUD::addField([
            // RYTHM
            'label' => __('Rhythm'), // Table column heading
            'type' => 'select',
            'name' => 'rhythm_id', // the column that contains the ID of that connected entity;
            'entity' => 'rhythm', // the method that defines the relationship in your Model
            'attribute' => 'name', // foreign key attribute that is shown to user
            'model' => Rhythm::class, // foreign key model
            'tab' => __('Course info'),
        ]);

        // unless the course has children, show the level field
        if ($this->crud->getCurrentEntry()->children->count() == 0) {
            CRUD::addField([
                // LEVEL
                'label' => __('Level'), // Table column heading
                'type' => 'select',
                'name' => 'level_id', // the column that contains the ID of that connected entity;
                'entity' => 'level', // the method that defines the relationship in your Model
                'attribute' => 'name', // foreign key attribute that is shown to user
                'model' => Level::class, // foreign key model
                'tab' => __('Course info'),
            ]);
        }

        CRUD::addFields([
            [
                'name' => 'name', // The db column name
                'label' => __('Name'), // Table column heading
                'tab' => __('Course info'),
            ],

            array_merge([
                'name' => 'price', // The db column name
                'label' => __('Price'), // Table column heading
                'tab' => __('Course info'),
                'type' => 'number',
            ], $currency),
        ]);

        if (config('invoicing.price_categories_enabled')) {
            CRUD::addFields([
                array_merge([
                    'name' => 'price_b',
                    'label' => __('Price B'),
                    'tab' => __('Course info'), 'type' => 'number',
                ], $currency),

                array_merge([
                    'name' => 'price_c',
                    'label' => __('PriceC'),
                    'tab' => __('Course info'), 'type' => 'number',
                ], $currency),
            ]);
        }

        CRUD::addFields([
            [
                'name' => 'volume', // The db column name
                'label' => __('Presential volume'), // Table column heading
                'suffix' => 'h',
                'tab' => __('Course info'),
            ],

            [
                'name' => 'remote_volume', // The db column name
                'label' => __('Remote volume'), // Table column heading
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

        // unless the course has children, show the resources tab
        if ($this->crud->getCurrentEntry()->children->count() == 0) {
            CRUD::addFields([
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
            ]);
        }

        CRUD::addFields([
            [
                // CAMPUS
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
                'label' => __('Evaluation ready'),
                'name' => 'marked',
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

        // unless the course has children, show the coursetimes tab
        if ($this->crud->getCurrentEntry()->children->count() == 0) {
            CRUD::addField([
                'name' => 'times',
                'label' => __('Course Schedule'),
                'type' => 'repeatable',
                'fields' => [
                    [
                        'name' => 'day',
                        'label' => __('Day'),
                        'type' => 'select_from_array',
                        'options' => [
                            0 => __('Sunday'),
                            1 => __('Monday'),
                            2 => __('Tuesday'),
                            3 => __('Wednesday'),
                            4 => __('Thursday'),
                            5 => __('Friday'),
                            6 => __('Saturday'),
                        ],
                        'allows_null' => false,
                        'default' => 1,
                        'wrapper' => ['class' => 'form-group col-md-4'],
                    ],
                    [
                        'name' => 'start',
                        'type' => 'time',
                        'label' => __('Start'),
                        'wrapper' => ['class' => 'form-group col-md-4'],
                    ],
                    [
                        'name' => 'end',
                        'type' => 'time',
                        'label' => __('End'),
                        'wrapper' => ['class' => 'form-group col-md-4'],
                    ],
                ],
                'tab' => __('Schedule'),
                'init_rows' => 0, // number of empty rows to be initialized, by default 1
            ]);

            if ($this->crud->getCurrentEntry()->children->count() == 0) {
                CRUD::addField([
                    'name' => 'remoteevents',
                    'label' => __('Remote events'),
                    'type' => 'repeatable',
                    'fields' => [
                        [
                            'name' => 'name',
                            'type' => 'text',
                            'label' => __('Name'),
                            'wrapper' => ['class' => 'form-group col-md-6'],
                        ],
                        [
                            'name' => 'worked_hours',
                            'type' => 'number',
                            'attributes' => ['step' => '0.25'],
                            'suffix'     => 'h',
                            'label' => __('Weekly Volume'),
                            'wrapper' => ['class' => 'form-group col-md-6'],
                        ],
                    ],
                    'tab' => __('Schedule'),
                    'init_rows' => 0, // number of empty rows to be initialized, by default 1
                ]);
            }
        }

        // add asterisk for fields that are required in CourseRequest
        CRUD::setValidation(CourseRequest::class);
    }

    protected function createSublevels($course, $sublevels, $courseTimes, $teacherId, $roomId) : void
    {
        foreach ($sublevels as $sublevel) {
            // create the subcourse and link it to the parent
            $childCourse = Course::create([
                'campus_id' => $course->campus_id,
                'rhythm_id' => $course->rhythm_id,
                'level_id' => $sublevel->level_id,
                'volume' => $sublevel->volume,
                'remote_volume' => $sublevel->remote_volume,
                'name' => $sublevel->name,
                'price' => $sublevel->price,
                'start_date' => $sublevel->start_date,
                'end_date' => $sublevel->end_date,
                'room_id' => $roomId,
                'teacher_id' => $teacherId,
                'parent_course_id' => $course->id,
                'exempt_attendance' => $course->exempt_attendance,
                'period_id' => $course->period_id,
                'opened' => $course->opened,
                'spots' => $course->spots,
            ]);

            $childCourse->saveCourseTimes($courseTimes);
            $childCourse->books()->attach($course->books);
        }
    }

    public function update()
    {
        $course = $this->crud->getCurrentEntry();
        $newCourseTimes = collect(json_decode($this->crud->getRequest()->input('times')));
        $course->saveCourseTimes($newCourseTimes);

        $remoteEvents = collect(json_decode($this->crud->getRequest()->input('remoteevents')));
        $course->saveRemoteEvents($remoteEvents);

        // update model
        $response = $this->traitUpdate();

        return $response;
    }

    public function store()
    {
        $teacherId = $this->crud->getRequest()->input('teacher_id');
        $roomId = $this->crud->getRequest()->input('room_id');

        $response = $this->traitStore();
        $course = $this->crud->getCurrentEntry();

        // if a schedule preset was applied, use it
        if ($this->crud->getRequest()->input('schedulepreset') !== null) {
            $courseTimes = collect(json_decode($this->crud->getRequest()->input('schedulepreset')));
        } else {
            // otherwise, use any user-defined course times
            $courseTimes = collect(json_decode($this->crud->getRequest()->input('times')));
        }

        $remoteEvents = collect(json_decode($this->crud->getRequest()->input('remoteevents')));
        $course->saveRemoteEvents($remoteEvents);

        $sublevels = collect(json_decode($this->crud->getRequest()->input('sublevels')));

        // if subcourses were added
        if ($sublevels->count() > 0) {
            // do not persist level on parent
            $this->crud->getRequest()->request->remove('level_id');

            // do not persist resources on parent but on children
            $this->crud->getRequest()->request->remove('teacher_id');
            $this->crud->getRequest()->request->remove('room_id');

            // do not persist course times on parent but on children
            $this->crud->getRequest()->request->remove('times');
        }

        if ($sublevels->count() > 0) {
            // create sublevels and apply coursetimes to them
            $this->createSublevels($course, $sublevels, $courseTimes, $teacherId, $roomId);
        } else {
            // otherwise, apply course times to the parent.
            $course->saveCourseTimes($courseTimes);
        }

        return $response;
    }
}
