<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\ExternalCourseRequest;
use App\Models\Course;
use App\Models\Level;
use App\Models\Partner;
use App\Models\Period;
use App\Models\Rhythm;
use App\Models\Room;
use App\Models\Teacher;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
use Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;
use Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
use Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanel;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

/**
 * Class ExternalCourseCrudController.
 * @property-read CrudPanel $crud
 */
class ExternalCourseCrudController extends CourseCrudController
{
    use ListOperation;
    use CreateOperation;
    use UpdateOperation;
    use DeleteOperation;

    public function setup()
    {
        CRUD::setModel(Course::class);
        CRUD::setRoute(config('backpack.base.route_prefix').'/externalcourse');
        CRUD::setEntityNameStrings(__('External Course'), __('External Courses'));
        CRUD::addClause('external');

        CRUD::enableExportButtons();
    }

    protected function setupListOperation()
    {
        CRUD::setColumns([
            [
                'label' => __('Partnership'),
                'type' => 'select',
                'name' => 'partner_id',
                'entity' => 'partner',
                'attribute' => 'name',
                'model' => Partner::class,
            ],

            [
                'label' => __('Rhythm'),
                'type' => 'select',
                'name' => 'rhythm_id',
                'entity' => 'rhythm',
                'attribute' => 'name',
                'model' => Rhythm::class,
            ],

            [
                'label' => __('Level'),
                'type' => 'select',
                'name' => 'level_id',
                'entity' => 'level',
                'attribute' => 'name',
                'model' => Level::class,
            ],

            [
                'name' => 'name',
                'label' => __('Name'),
            ],

            [
                'name' => 'volume',
                'label' => __('Volume'),
                'suffix' => 'h',
                'type' => 'number',
            ],

            array_merge([
                'name' => 'hourly_price',
                'label' => __('Price'),
                'type' => 'number',
            ], $this->currency),

            [
                'label' => __('Teacher'),
                'type' => 'select',
                'name' => 'teacher_id',
                'entity' => 'teacher',
                'attribute' => 'name',
                'model' => Teacher::class,
                'searchLogic' => false,
            ],

            [
                'label' => __('Room'),
                'type' => 'select',
                'name' => 'room_id',
                'entity' => 'room',
                'attribute' => 'name',
                'model' => Room::class,
            ],

            [
                'name' => 'times',
                'label' => __('Schedule'),
                'type' => 'model_function',
                'function_name' => 'getCourseTimesAttribute',
                'limit' => 150,
            ],

            [
                'name' => 'head_count',
                'label' => __('Students'),
            ],

            [
                'name' => 'new_students',
                'label' => __('Year Students'),
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

        CRUD::addFilter(
            [
                'name' => 'rhythm_id',
                'type' => 'select2',
                'label' => __('Rhythm'),
            ],
            fn () => Rhythm::all()->pluck('name', 'id')->toArray(),
            function ($value) {
                CRUD::addClause('where', 'rhythm_id', $value);
            },
            function () {
            }
        );

        CRUD::addFilter(
            [
                'name' => 'teacher_id',
                'type' => 'select2',
                'label' => __('Teacher'),
            ],
            fn () => Teacher::all()->pluck('name', 'id')->toArray(),
            function ($value) {
                CRUD::addClause('where', 'teacher_id', $value);
            },
            function () {
            }
        );

        CRUD::addFilter(
            [
                'name' => 'level_id',
                'type' => 'select2',
                'label' => __('Level'),
            ],
            fn () => Level::all()->pluck('name', 'id')->toArray(),
            function ($value) {
                CRUD::addClause('where', 'level_id', $value);
            },
            function () {
            }
        );

        CRUD::addFilter(
            [
                'name' => 'period_id',
                'type' => 'select2',
                'label' => __('Period'),
            ],
            fn () => Period::all()->pluck('name', 'id')->toArray(),
            function ($value) {
                CRUD::addClause('where', 'period_id', $value);
            },
            function () {
                $period = Period::get_default_period()->id;
                CRUD::addClause('where', 'period_id', $period);
                $this->crud->getRequest()->request->add(['period_id' => $period]); // to make the filter look active
            }
        );
    }

    protected function setupCreateOperation()
    {
        CRUD::setValidation(ExternalCourseRequest::class);

        CRUD::addField([
            'label' => __('Partnership'),
            'type' => 'select',
            'name' => 'partner_id',
            'entity' => 'partner',
            'attribute' => 'name',
            'model' => Partner::class,
            'tab' => __('Course info'),
        ]);

        $this->addRhythmField();
        $this->addLevelField();
        $this->addNameField();

        CRUD::addField([
            'name' => 'volume',
            'label' => __('Volume'),
            'suffix' => 'h',
            'tab' => __('Course info'),
        ]);

        CRUD::addField(array_merge(
            [
                'name' => 'hourly_price',
                'label' => __('Hourly Price'),
                'tab' => __('Course info'),
                'type' => 'number',
            ], $this->currency)
        );


        CRUD::addFields([
            [
                'label' => __('Campus'),
                'type' => 'hidden',
                'name' => 'campus_id',
                'value' => 2,
                'tab' => __('Course info'),
            ],
            [
                'name' => 'price',
                'type' => 'hidden',
                'value' => 0,
                'tab' => __('Course info'),
            ],
        ]);

        $this->addCourseResourceFields();

        $this->addCourseScheduleFields();

        CRUD::addFields([
            [
                'name' => 'head_count',
                'label' => __('Head Count'),
                'type' => 'number',
                'tab' => __('Course info'),
            ],

            [
                'name' => 'new_students',
                'label' => __('Students to count in year total'),
                'type' => 'number',
                'tab' => __('Course info'),
            ],
        ]);

        $this->addCourseScheduleFieldsForRealCourses();
    }

    protected function setupUpdateOperation()
    {
        $this->setupCreateOperation();
    }
}
