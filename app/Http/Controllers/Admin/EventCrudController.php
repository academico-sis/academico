<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\EventRequest as StoreRequest;
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
use Carbon\Carbon;
use Illuminate\Http\Request;

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
        /*
        |--------------------------------------------------------------------------
        | CrudPanel Basic Information
        |--------------------------------------------------------------------------
        */
        CRUD::setModel(Event::class);
        CRUD::setRoute(config('backpack.base.route_prefix').'/event');
        CRUD::setEntityNameStrings(__('event'), __('events'));
        CRUD::setCreateView('events.create');
    }

    /*
    |--------------------------------------------------------------------------
    | CrudPanel Configuration
    |--------------------------------------------------------------------------
    */
    public function setupListOperation()
    {
        CRUD::setColumns([

            [
                'name' => 'name', // The db column name
                'label' => 'Name',
                'type' => 'text',
            ],

            [
                // ROOM
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
                'function_name' => 'getVolumeAttribute', // the method in your Model
                'suffix' => 'h',
            ],

            [
                // TEACHER
                'label' => __('Teacher'),
                'type' => 'select',
                'name' => 'teacher_id',
                'entity' => 'teacher',
                'attribute' => 'name',
                'model' => Teacher::class,
                'searchLogic' => false,
            ],

            [
                // ROOM
                'label' => __('Room'),
                'type' => 'select',
                'name' => 'room_id',
                'entity' => 'room',
                'attribute' => 'name',
                'model' => Room::class,
            ],

            [
                'name' => 'start', // The db column name
                'label' => __('Start Date'),
                'type' => 'datetime',
                // 'format' => 'l j F Y', // use something else than the base.defauormat config value
            ],

            [
                'name' => 'end', // The db column name
                'label' => __('End Date'),
                'type' => 'datetime',
                // 'format' => 'l j F Y', // use something else than the base.default_date_format config value
            ],

        ]);

        CRUD::addFilter(
            [ // daterange filter
                'type' => 'date_range',
                'name' => 'from_to',
                'label'=> __('Date range'),
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
                'label'=> __('Events with no course'),
            ],
            false,
            function ($value) { // if the filter is active, apply these constraints
              $this->crud->query->where('course_id', null);
          },
            function () { // if the filter is NOT active (the GET parameter "checkbox" does not exit)
          }
        );

        CRUD::addFilter(
            [
                'type' => 'simple',
                'name' => 'unassigned',
                'label'=> __('Events with no teacher'),
            ],
            false,
            function ($value) { // if the filter is active, apply these constraints
              CRUD::addClause('unassigned');
          }
        );

        CRUD::addFilter(
            [ // select2 filter
                'name' => 'teacher_id',
                'type' => 'select2',
                'label'=> __('Teacher'),
            ],
            fn () => Teacher::all()->pluck('name', 'id')->toArray(),
            function ($value) { // if the filter is active
            CRUD::addClause('where', 'teacher_id', $value);
        },
            function () { // if the filter is NOT active (the GET parameter "checkbox" does not exit)
          }
        );
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
        ]);

        foreach ($request->createList as $eventData) {
            Event::create([
                'name' => $request->name,
                'start' => Carbon::parse($eventData['start']),
                'end' => Carbon::parse($eventData['end']),
                'teacher_id' => $request->teacher,
                'room_id' => $request->room,
            ]);
        }
    }

    public function setupUpdateOperation()
    {
        CRUD::addFields([

            [
                'name' => 'name', // The db column name
                'label' => 'Name',
                'type' => 'text',
            ],

            [
                // TEACHER
                'label' => 'Teacher',
                'type' => 'select',
                'name' => 'teacher_id',
                'entity' => 'teacher',
                'attribute' => 'name',
                'model' => Teacher::class,

            ],

            [
                // ROOM
                'label' => 'Room',
                'type' => 'select',
                'name' => 'room_id',
                'entity' => 'room',
                'attribute' => 'name',
                'model' => Room::class,
            ],

            [
                'name' => 'start', // The db column name
                'label' => 'Start Date',
                'type' => 'datetime_picker',
            ],

            [
                'name' => 'end', // The db column name
                'label' => 'End Date',
                'type' => 'datetime_picker',
            ],

        ]);

        CRUD::setValidation(StoreRequest::class);
    }
}
