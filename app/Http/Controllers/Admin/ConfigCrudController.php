<?php

namespace App\Http\Controllers\Admin;

use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

// VALIDATION: change the requests to match your own file names if you need form validation
use App\Http\Requests\ConfigRequest as StoreRequest;
use App\Http\Requests\ConfigRequest as UpdateRequest;
use Backpack\CRUD\CrudPanel;

/**
 * Class ConfigCrudController
 * Various settings that are more easily stored here than in the env file.
 * @package App\Http\Controllers\Admin
 * @property-read CrudPanel $crud
 */
class ConfigCrudController extends CrudController
{
    
    use \Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;
    
    
    public function setup()
    {
        /*
        |--------------------------------------------------------------------------
        | CrudPanel Basic Information
        |--------------------------------------------------------------------------
        */
        CRUD::setModel('App\Models\Config');
        CRUD::setRoute(config('backpack.base.route_prefix') . '/config');
        CRUD::setEntityNameStrings('config', 'configs');
        
        /*
        |--------------------------------------------------------------------------
        | CrudPanel Configuration
        |--------------------------------------------------------------------------
        */

        // TODO: remove setFromDb() and manually define Fields and Columns
        CRUD::setFromDb();

        // add asterisk for fields that are required in ConfigRequest
        CRUD::setRequiredFields(StoreRequest::class, 'create');
        CRUD::setRequiredFields(UpdateRequest::class, 'edit');
    }

    protected function setupUpdateOperation()
    {
        CRUD::setValidation(UpdateRequest::class);
    }

}
