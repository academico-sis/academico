<?php

namespace App\Http\Controllers\Admin;

use App\Models\Course;
use App\Models\Event;
use App\Models\Room;
use App\Models\Teacher;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
use Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;
use Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
use Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

/**
 * Class EventCrudController
 * An event corresponds to a class. This controller is used to monitor events automatically created
 * but also to add or update events manually.
 */
class EventCrudController extends CrudController
{
    use ListOperation;
    use CreateOperation;
    use UpdateOperation;
    use DeleteOperation;

    public function setup()
    {
        CRUD::setModel(Event::class);
        CRUD::setRoute(config('backpack.base.route_prefix').'/event');
        CRUD::setEntityNameStrings(__('event'), __('events'));
        CRUD::setCreateView('events.create');
    }

    public function setupListOperation()
    {
        CRUD::setColumns([
            [
                'name' => 'name',
                'label' => 'Name',
                'type' => 'text',
            ],

            [
                'label' => __('Course'),
                'type' => 'select',
                'name' => 'course_id',
                'entity' => 'course',
                'attribute' => 'name',
                'model' => Course::class,
            ],

            [
                'name' => 'volume',
                'label' => __('Volume'),
                'type' => 'model_function',
                'function_name' => 'getVolumeAttribute',
                'suffix' => 'h',
            ],

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
                'name' => 'start',
                'label' => __('Start Date'),
                'type' => 'datetime',
            ],

            [
                'name' => 'end',
                'label' => __('End Date'),
                'type' => 'datetime',
            ],

        ]);

        CRUD::addFilter(
            [
                'type' => 'date_range',
                'name' => 'from_to',
                'label' => __('Date range'),
            ],
            false,
            function ($value) { // if the filter is active, apply these constraints
                $dates = json_decode($value, null, 512, JSON_THROW_ON_ERROR);

                if ($dates->from) {
                    CRUD::addClause('where', 'start', '>=', $dates->from);
                }
                if ($dates->to) {
                    CRUD::addClause('where', 'start', '<=', $dates->to.' 23:59:59');
                }
            }
        );

        CRUD::addFilter(
            [
                'type' => 'simple',
                'name' => 'orphan',
                'label' => __('Events with no course'),
            ],
            false,
            function () { // if the filter is active
                $this->crud->query->where('course_id', null);
            },
            function () { // if the filter is NOT active
            }
        );

        CRUD::addFilter(
            [
                'type' => 'simple',
                'name' => 'unassigned',
                'label' => __('Events with no teacher'),
            ],
            false,
            function () {
                CRUD::addClause('unassigned');
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
    }

    public function setupCreateOperation()
    {
        CRUD::setValidation([
            'name' => [
                'required',
                'min:1',
                'max:255',
            ],
            'start' => 'required|date',
            'end' => 'required|date',
            'course_id' => 'nullable|integer',
            'teacher_id' => 'nullable|integer',
            'room_id' => 'nullable|integer',
            'exempt_attendance' => 'nullable|bool',
        ]);

        CRUD::addFields([
            [
                'name' => 'name',
                'label' => 'Name',
                'type' => 'text',
            ],

            [
                'label' => 'Teacher',
                'type' => 'select',
                'name' => 'teacher_id',
                'entity' => 'teacher',
                'attribute' => 'name',
                'model' => Teacher::class,
            ],

            [
                'label' => 'Room',
                'type' => 'select',
                'name' => 'room_id',
                'entity' => 'room',
                'attribute' => 'name',
                'model' => Room::class,
            ],

            [
                'name' => 'start',
                'label' => 'Start Date',
                'type' => 'datetime_picker',
            ],

            [
                'name' => 'end',
                'label' => 'End Date',
                'type' => 'datetime_picker',
            ],
        ]);
    }

    public function setupUpdateOperation()
    {
        $this->setupCreateOperation();
    }
}
