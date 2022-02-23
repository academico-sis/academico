<?php

namespace App\Http\Controllers\Admin;

use App\Models\Room;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
use Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;
use Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
use Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;
use Illuminate\Validation\Rule;

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
                'label' => 'Campus',
                'type' => 'select',
                'name' => 'campus',
                'attribute' => 'name',
            ],

            [
                'name' => 'name',
                'label' => 'Name',
                'type' => 'text',
            ],

            [
                'name' => 'capacity',
                'label' => 'Capacity',
                'type' => 'number',
            ],
        ]);
    }

    protected function setupCreateOperation()
    {
        CRUD::setValidation([
            'name' => [
                'required',
                'min:1',
                'max:40',
                Rule::unique($this->crud->getModel()->getTable())->ignore($this->crud->getCurrentEntry()),
            ],
        ]);

        CRUD::addFields([
            [
                'label' => 'Campus',
                'type' => 'select',
                'entity' => 'campus',
                'name' => 'campus_id',
                'attribute' => 'name',
            ],

            [
                'name' => 'name',
                'label' => 'Name',
                'type' => 'text',
            ],

            [
                'name' => 'capacity',
                'label' => 'Capacity',
                'type' => 'number',
            ],
        ]);
    }

    protected function setupUpdateOperation()
    {
        $this->setupCreateOperation();
    }
}
