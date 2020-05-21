<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\EvaluationTypeRequest as StoreRequest;
// VALIDATION: change the requests to match your own file names if you need form validation
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

/**
 * Class EvaluationTypeCrudController.
 * @property-read CrudPanel $crud
 */
class EvaluationTypeCrudController extends CrudController
{
    use \Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;

    public function setup()
    {
        CRUD::setModel(\App\Models\EvaluationType::class);
        CRUD::setRoute(config('backpack.base.route_prefix').'/evaluationtype');
        CRUD::setEntityNameStrings('evaluationtype', 'evaluation_types');
    }

    protected function setupListOperation()
    {
        CRUD::addColumn(['name' => 'name', 'label' => 'Name']);
    }

    protected function setupCreateOperation()
    {
        CRUD::addField(['name' => 'name', 'label' => 'Name', 'type' => 'text']);
        CRUD::setValidation(StoreRequest::class);
    }

    protected function setupUpdateOperation()
    {
        $this->setupCreateOperation();
    }
}
