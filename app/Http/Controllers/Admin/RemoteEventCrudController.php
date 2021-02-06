<?php

namespace App\Http\Controllers\Admin;

// VALIDATION: change the requests to match your own file names if you need form validation
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
                // 1-n relationship
                'label' => 'Period', // Table column heading
                'type' => 'relationship',
                'name' => 'period', // the column that contains the ID of that connected entity;
                'attribute' => 'name', // foreign key attribute that is shown to user
            ],

            [
                // 1-n relationship
                'label' => 'Teacher', // Table column heading
                'type' => 'relationship',
                'name' => 'teacher', // the method that defines the relationship in your Model
                'attribute' => 'name', // foreign key attribute that is shown to user
            ],

            [
                'name' => 'worked_hours', // The db column name
                'label' => 'Worked Hours', // Table column heading
                'type' => 'number',
                'suffix' => ' h',
                'decimals' => 2,
            ],

            [
                'name' => 'name', // The db column name
                'label' => 'Project', // Table column heading
            ],

        ]);
    }

    public function setupCreateOperation()
    {
        CRUD::setValidation(RemoteEventRequest::class);

        CRUD::addFields([
            [
                // 1-n relationship
                'label' => 'Period', // Table column heading
                'type' => 'select',
                'name' => 'period_id', // the column that contains the ID of that connected entity;
                'entity' => 'period', // the method that defines the relationship in your Model
                'attribute' => 'name', // foreign key attribute that is shown to user
                'model' => Period::class, // foreign key model
            ],

            [
                // 1-n relationship
                'label' => 'Teacher', // Table column heading
                'type' => 'select',
                'name' => 'teacher_id', // the column that contains the ID of that connected entity;
                'entity' => 'teacher', // the method that defines the relationship in your Model
                'attribute' => 'name', // foreign key attribute that is shown to user
                'model' => Teacher::class, // foreign key model
            ],

            [
                'name' => 'worked_hours', // The db column name
                'label' => 'Worked Hours', // Table column heading
                'type' => 'number',
                'suffix' => ' h',
                'attributes' => ['step' => '0.01'],
            ],

            [
                'name' => 'name', // The db column name
                'label' => 'Project', // Table column heading
            ],

        ]);
    }

    public function setupUpdateOperation()
    {
        $this->setupCreateOperation();
    }
}
