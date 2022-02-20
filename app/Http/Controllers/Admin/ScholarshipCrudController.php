<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\ScholarshipRequest;
use App\Models\Scholarship;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
use Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;
use Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
use Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation;
use Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanel;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

class ScholarshipCrudController extends CrudController
{
    use ListOperation;
    use CreateOperation;
    use UpdateOperation;
    use DeleteOperation;
    use ShowOperation;

    public function setup()
    {
        CRUD::setModel(Scholarship::class);
        CRUD::setRoute(config('backpack.base.route_prefix').'/scholarship');
        CRUD::setEntityNameStrings(__('scholarship'), __('scholarships'));
    }

    protected function setupListOperation()
    {
        CRUD::column('name')->type('text');
    }

    protected function setupCreateOperation()
    {
        CRUD::setValidation(ScholarshipRequest::class);

        CRUD::field('name')->type('text');
    }

    protected function setupUpdateOperation()
    {
        $this->setupCreateOperation();
    }
}
