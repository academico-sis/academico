<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\RoomRequest as StoreRequest;
// VALIDATION: change the requests to match your own file names if you need form validation
use App\Models\Room;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
use Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;
use Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
use Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

class RoomCrudController extends CrudController
{
    use ListOperation;
    use CreateOperation;
    use UpdateOperation;
    use DeleteOperation;

    public function setup()
    {
        CRUD::setModel(Room::class);
        CRUD::setRoute(config('backpack.base.route_prefix').'/room');
        CRUD::setEntityNameStrings(__('room'), __('rooms'));
    }

    protected function setupListOperation()
    {
        CRUD::setColumns([
            [
                // 1-n relationship
                'label' => 'Campus',
                'type' => 'relationship',
                'name' => 'campus',
                'attribute' => 'name',
            ],

            [
                'name' => 'name', // The db column name
                'label' => 'Name', // Table column heading
                'type' => 'text',
            ],

            [
                'name' => 'capacity', // The db column name
                'label' => 'Capacity', // Table column heading
                'type' => 'number',
            ],
        ]);
    }

    protected function setupCreateOperation()
    {
        CRUD::setValidation(StoreRequest::class);
        CRUD::addFields([
            [
                // 1-n relationship
                'label' => 'Campus',
                'type' => 'select',
                'entity' => 'campus',
                'name' => 'campus_id', // the db column for the foreign key
                'attribute' => 'name',
            ],

            [
                'name' => 'name', // The db column name
                'label' => 'Name', // Table column heading
                'type' => 'text',
            ],

            [
                'name' => 'capacity', // The db column name
                'label' => 'Capacity', // Table column heading
                'type' => 'number',
            ],
        ]);
    }

    protected function setupUpdateOperation()
    {
        $this->setupCreateOperation();
    }
}
