<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\ResultTypeRequest as StoreRequest;
// VALIDATION: change the requests to match your own file names if you need form validation
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

/**
 * Class ResultTypeCrudController.
 * @property-read CrudPanel $crud
 */
class ResultTypeCrudController extends CrudController
{
    use \Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;

    public function setup()
    {
        CRUD::setModel(\App\Models\ResultType::class);
        CRUD::setRoute(config('backpack.base.route_prefix').'/resulttype');
        CRUD::setEntityNameStrings('resulttype', 'result_types');
    }

    protected function setupListOperation()
    {
        CRUD::addColumns([
            ['name' => 'name', 'label' => 'Name'],
            ['name' => 'description', 'label' => 'Description'],
        ]);
    }

    protected function setupCreateOperation()
    {
        CRUD::setValidation(StoreRequest::class);

        CRUD::addFields([
            ['name' => 'name', 'label' => 'Name', 'type' => 'textarea'],
            ['name' => 'description', 'label' => 'Description', 'type' => 'textarea'],
        ]);
    }

    protected function setupUpdateOperation()
    {
        $this->setupCreateOperation();
    }
}
