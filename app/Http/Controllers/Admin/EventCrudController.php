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
use Illuminate\Validation\Rule;

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
    }

    public function setupCreateOperation()
    {
        CRUD::setValidation([
            'name' => [
                'required',
                'min:1',
                'max:255',
                Rule::unique($this->crud->getModel()->getTable())->ignore($this->crud->getCurrentEntry()),
            ],
            'start' => 'required|date',
            'end' => 'required|date',
            'course_id' => 'nullable|integer',
            'teacher_id' => 'nullable|integer',
            'room_id' => 'nullable|integer',
            'exempt_attendance' => 'nullable|bool'
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
