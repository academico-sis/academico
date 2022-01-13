<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\SchedulePresetRequest;
use App\Models\SchedulePreset;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
use Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;
use Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
use Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation;
use Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanel;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

/**
 * Class SchedulePresetCrudController
 * @property-read CrudPanel $crud
 */
class SchedulePresetCrudController extends CrudController
{
    use ListOperation;
    use CreateOperation;
    use UpdateOperation;
    use DeleteOperation;
    use ShowOperation;

    /**
     * Configure the CrudPanel object. Apply settings to all operations.
     *
     * @return void
     */
    public function setup()
    {
        CRUD::setModel(SchedulePreset::class);
        CRUD::setRoute(config('backpack.base.route_prefix').'/schedulepreset');
        CRUD::setEntityNameStrings('Schedule Preset', 'Schedule Presets');
    }

    /**
     * Define what happens when the List operation is loaded.
     *
     * @see  https://backpackforlaravel.com/docs/crud-operation-list-entries
     * @return void
     */
    protected function setupListOperation()
    {
        CRUD::column('name')->type('text');

        // TODO add quick view here.
    }

    /**
     * Define what happens when the Create operation is loaded.
     *
     * @see https://backpackforlaravel.com/docs/crud-operation-create
     * @return void
     */
    protected function setupCreateOperation()
    {
        CRUD::setValidation(SchedulePresetRequest::class);

        CRUD::field('name')->type('text');

        CRUD::addfield([
            'name' => 'presets',
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
        ]);
    }

    /**
     * Define what happens when the Update operation is loaded.
     *
     * @see https://backpackforlaravel.com/docs/crud-operation-update
     * @return void
     */
    protected function setupUpdateOperation()
    {
        $this->setupCreateOperation();
    }
}
