<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\LevelRequest as StoreRequest;
// VALIDATION: change the requests to match your own file names if you need form validation
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

/**
 * Class LevelCrudController.
 * @property-read CrudPanel $crud
 */
class LevelCrudController extends CrudController
{
    use \Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;

    public function setup()
    {
        CRUD::setModel(\App\Models\Level::class);
        CRUD::setRoute(config('backpack.base.route_prefix').'/level');
        CRUD::setEntityNameStrings('level', 'levels');
    }

    protected function setupListOperation()
    {
        CRUD::addColumn(['name' => 'name', 'label' => 'Name']);
    }

    protected function setupCreateOperation()
    {
        CRUD::setValidation(StoreRequest::class);
        CRUD::addField(['name' => 'name', 'label' => 'Name', 'type' => 'text']);
    }

    protected function setupUpdateOperation()
    {
        $this->setupCreateOperation();
    }
}
