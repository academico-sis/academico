<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\PeriodRequest as StoreRequest;
use App\Http\Requests\PeriodRequest as UpdateRequest;
// VALIDATION: change the requests to match your own file names if you need form validation
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

/**
 * Class PeriodCrudController.
 * @property-read CrudPanel $crud
 */
class PeriodCrudController extends CrudController
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
        CRUD::setModel('App\Models\Period');
        CRUD::setRoute(config('backpack.base.route_prefix').'/period');
        CRUD::setEntityNameStrings('period', 'periods');
    }

    /*
    |--------------------------------------------------------------------------
    | CrudPanel Configuration
    |--------------------------------------------------------------------------
    */

    public function setupListOperation()
    {
        CRUD::setColumns([
                [
                    'label'     => __('Year'),
                    'type'      => 'select',
                    'entity'    => 'year',
                    'attribute' => 'name',
                ],

                [
                    'label' => __('Name'),
                    'type' => 'text',
                    'name' => 'name',
                ],

                [
                    'label' => __('Start'),
                    'type' => 'date',
                    'name' => 'start',
                ],

                [
                    'label' => __('End'),
                    'type' => 'date',
                    'name' => 'end',
                ],
            ]);
    }

    public function setupCreateOperation()
    {
        CRUD::addFields([
            [
                'label'     => __('Year'),
                'type'      => 'select',
                'name'      => 'year_id',
                'entity'    => 'year',
                'attribute' => 'name',
                'model'     => 'App\Models\Year',
            ],

            [
                'label' => __('Name'),
                'type' => 'text',
                'name' => 'name',
            ],

            [
                'label' => __('Start'),
                'type' => 'date',
                'name' => 'start',
            ],

            [
                'label' => __('End'),
                'type' => 'date',
                'name' => 'end',
            ],
        ]);

        // add asterisk for fields that are required in PeriodRequest
        CRUD::setRequiredFields(StoreRequest::class, 'create');
        CRUD::setRequiredFields(UpdateRequest::class, 'edit');
    }

    public function setupUpdateOperation()
    {
        $this->setupCreateOperation();
    }
}
