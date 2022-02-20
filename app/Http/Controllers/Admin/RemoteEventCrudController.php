<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\RemoteEventRequest;
use App\Models\Period;
use App\Models\RemoteEvent;
use App\Models\Teacher;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
use Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;
use Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
use Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

class RemoteEventCrudController extends CrudController
{
    use ListOperation;
    use CreateOperation;
    use UpdateOperation;
    use DeleteOperation;

    public function setup()
    {
        CRUD::setModel(RemoteEvent::class);
        CRUD::setRoute(config('backpack.base.route_prefix').'/remoteevent');
        CRUD::setEntityNameStrings(__('remote event'), __('remote events'));
    }

    public function setupListOperation()
    {
        CRUD::setColumns([
            [
                'label' => 'Period',
                'type' => 'select',
                'name' => 'period',
                'attribute' => 'name',
            ],

            [
                'label' => 'Teacher',
                'type' => 'select',
                'name' => 'teacher',
                'attribute' => 'name',
            ],

            [
                'name' => 'worked_hours',
                'label' => 'Worked Hours',
                'type' => 'number',
                'suffix' => ' h',
                'decimals' => 2,
            ],

            [
                'name' => 'name',
                'label' => 'Project',
            ],

        ]);
    }

    public function setupCreateOperation()
    {
        CRUD::setValidation(RemoteEventRequest::class);

        CRUD::addFields([
            [
                'label' => 'Period',
                'type' => 'select',
                'name' => 'period_id',
                'entity' => 'period',
                'attribute' => 'name',
                'model' => Period::class,
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
                'name' => 'worked_hours',
                'label' => 'Worked Hours',
                'type' => 'number',
                'suffix' => ' h',
                'attributes' => ['step' => '0.01'],
            ],

            [
                'name' => 'name',
                'label' => 'Project',
            ],

        ]);
    }

    public function setupUpdateOperation()
    {
        $this->setupCreateOperation();
    }
}
