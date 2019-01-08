<?php

namespace App\Http\Controllers\Admin;

use Backpack\CRUD\app\Http\Controllers\CrudController;

// VALIDATION: change the requests to match your own file names if you need form validation
use App\Http\Requests\PeriodRequest as StoreRequest;
use App\Http\Requests\PeriodRequest as UpdateRequest;

/**
 * Class PeriodCrudController
 * @package App\Http\Controllers\Admin
 * @property-read CrudPanel $crud
 */
class PeriodCrudController extends CrudController
{
    public function setup()
    {
        /*
        |--------------------------------------------------------------------------
        | CrudPanel Basic Information
        |--------------------------------------------------------------------------
        */
        $this->crud->setModel('App\Models\Period');
        $this->crud->setRoute(config('backpack.base.route_prefix') . '/period');
        $this->crud->setEntityNameStrings('period', 'periods');

        /*
        |--------------------------------------------------------------------------
        | CrudPanel Configuration
        |--------------------------------------------------------------------------
        */

        $this->crud->setColumns([
            [
                'label'     => 'Year', 
                'type'      => 'select',
                'entity'    => 'year',
                'attribute' => 'name', 
            ],

            [
                'label' => "Name", 
                'type' => "text",
                'name' => 'name'
            ],
        ]);

        $this->crud->addFields([
            [
                'label'     => 'Year', 
                'type'      => 'select',
                'name'      => 'year_id',
                'entity'    => 'year',
                'attribute' => 'name', 
                'model'     => 'App\Models\Year', 
            ],

            [
                'label' => "Name", 
                'type' => "text",
                'name' => 'name'
            ],
        ]);

        // add asterisk for fields that are required in PeriodRequest
        $this->crud->setRequiredFields(StoreRequest::class, 'create');
        $this->crud->setRequiredFields(UpdateRequest::class, 'edit');
    }

    public function store(StoreRequest $request)
    {
        // your additional operations before save here
        $redirect_location = parent::storeCrud($request);
        // your additional operations after save here
        // use $this->data['entry'] or $this->crud->entry
        return $redirect_location;
    }

    public function update(UpdateRequest $request)
    {
        // your additional operations before save here
        $redirect_location = parent::updateCrud($request);
        // your additional operations after save here
        // use $this->data['entry'] or $this->crud->entry
        return $redirect_location;
    }
}
