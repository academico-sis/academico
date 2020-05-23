<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\ConfigRequest as UpdateRequest;
// VALIDATION: change the requests to match your own file names if you need form validation
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;
use Backpack\CRUD\CrudPanel;

/**
 * Class ConfigCrudController
 * Various settings that are more easily stored here than in the env file.
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
        CRUD::setModel(\App\Models\Config::class);
        CRUD::setRoute(config('backpack.base.route_prefix').'/config');
        CRUD::setEntityNameStrings('config', 'configs');
    }

    protected function setupListOperation()
    {
        $this->crud->addColumn([
            'name' => 'name', // The db column name
            'label' => 'Config Field Name', // Table column heading
            'type' => 'Text',
        ]);

        $this->crud->addColumn([
            'name' => 'value', // The db column name
            'label' => 'Config Field Value', // Table column heading
            'type' => 'Text',
        ]);
    }

    protected function setupUpdateOperation()
    {
        CRUD::setValidation(UpdateRequest::class);

        $this->crud->addField([
            'name' => 'name', // The db column name
            'label' => 'Config Field Name', // Table column heading
            'type' => 'text',
            'attributes' => [
                'disabled'=>'disabled',
            ],
        ]);

        $this->crud->addField([
            'name' => 'value', // The db column name
            'label' => 'Config Field Value', // Table column heading
            'type' => 'text',
        ]);
    }
}
