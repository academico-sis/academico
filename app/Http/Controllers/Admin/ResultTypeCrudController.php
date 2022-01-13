<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\ResultTypeRequest as StoreRequest;
use App\Models\ResultType;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
use Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;
use Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
use Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

class ResultTypeCrudController extends CrudController
{
    use ListOperation;
    use CreateOperation;
    use UpdateOperation;
    use DeleteOperation;

    public function setup()
    {
        CRUD::setModel(ResultType::class);
        CRUD::setRoute(config('backpack.base.route_prefix').'/resulttype');
        CRUD::setEntityNameStrings(__('result type'), __('result types'));
    }

    protected function setupListOperation()
    {
        CRUD::addColumns([
            ['name' => 'name',
                'label' => 'Name', ],
            ['name' => 'description',
                'label' => 'Description', ],
        ]);
    }

    protected function setupCreateOperation()
    {
        CRUD::setValidation(StoreRequest::class);

        CRUD::addFields([
            ['name' => 'name',
                'label' => 'Name',
                'type' => 'textarea', ],
            ['name' => 'description',
                'label' => 'Description',
                'type' => 'textarea', ],
        ]);
    }

    protected function setupUpdateOperation()
    {
        $this->setupCreateOperation();
    }
}
