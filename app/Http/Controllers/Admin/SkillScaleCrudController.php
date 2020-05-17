<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\SkillScaleRequest as StoreRequest;
// VALIDATION: change the requests to match your own file names if you need form validation
use App\Http\Requests\SkillScaleRequest as UpdateRequest;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

/**
 * Class SkillScaleCrudController.
 * @property-read CrudPanel $crud
 */
class SkillScaleCrudController extends CrudController
{
    use \Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;

    public function setup()
    {
        /*
        |--------------------------------------------------------------------------
        | CrudPanel Basic Information
        |--------------------------------------------------------------------------
        */
        CRUD::setModel(\App\Models\Skills\SkillScale::class);
        CRUD::setRoute(config('backpack.base.route_prefix').'/skillscale');
        CRUD::setEntityNameStrings('skillscale', 'skill_scales');

        /*
        |--------------------------------------------------------------------------
        | CrudPanel Configuration
        |--------------------------------------------------------------------------
        */

        CRUD::addColumns([
            ['name' => 'shortname', 'label' => 'Shortname'],
            ['name' => 'name', 'label' => 'Name'],
            ['name' => 'value', 'label' => 'Value']
        ]);

        CRUD::addFields([
            ['name' => 'shortname', 'label' => 'Shortname', 'type' => 'text'],
            ['name' => 'name', 'label' => 'Name', 'type' => 'text'],
            ['name' => 'value', 'label' => 'Value', 'type' => 'text']
        ]);

        // add asterisk for fields that are required in SkillScaleRequest
        CRUD::setRequiredFields(StoreRequest::class, 'create');
        CRUD::setRequiredFields(UpdateRequest::class, 'edit');
    }

    protected function setupCreateOperation()
    {
        CRUD::setValidation(StoreRequest::class);
    }

    protected function setupUpdateOperation()
    {
        CRUD::setValidation(UpdateRequest::class);
    }
}
