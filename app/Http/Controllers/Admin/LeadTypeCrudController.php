<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\LeadTypeRequest as StoreRequest;
use App\Models\LeadType;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
use Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;
use Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
use Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

class LeadTypeCrudController extends CrudController
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
        CRUD::setModel(LeadType::class);
        CRUD::setRoute(config('backpack.base.route_prefix').'/leadtype');
        CRUD::setEntityNameStrings(__('lead type'), __('lead types'));
    }

    protected function setupListOperation()
    {
        CRUD::addColumn(['name' => 'name',
            'label' => 'Name', ]);
    }

    protected function setupCreateOperation()
    {
        CRUD::setValidation(StoreRequest::class);
        CRUD::addField(['name' => 'name',
            'label' => 'Name',
            'type' => 'text', ]);
    }

    protected function setupUpdateOperation()
    {
        $this->setupCreateOperation();
    }
}
