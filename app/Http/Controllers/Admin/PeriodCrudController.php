<?php

namespace App\Http\Controllers\Admin;

use App\Models\Period;
use App\Models\Year;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
use Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;
use Backpack\CRUD\app\Http\Controllers\Operations\FetchOperation;
use Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
use Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;
use Illuminate\Validation\Rule;

class PeriodCrudController extends CrudController
{
    use ListOperation;
    use CreateOperation;
    use UpdateOperation;
    use DeleteOperation;
    use FetchOperation;

    public function setup()
    {
        CRUD::setModel(Period::class);
        CRUD::setRoute(config('backpack.base.route_prefix').'/period');
        CRUD::setEntityNameStrings(__('period'), __('periods'));
    }

    public function setupListOperation()
    {
        CRUD::setColumns([
            [
                'label' => __('Year'),
                'type' => 'select',
                'name' => 'year_id',
                'entity' => 'year',
                'model' => Year::class,
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

            [
                'label' => __('Archived'),
                'type' => 'boolean',
                'name' => 'archived',
            ],
        ]);
    }

    public function setupCreateOperation()
    {
        CRUD::setValidation([
            'name' => [
                'required',
                'min:1',
                'max:40',
                Rule::unique($this->crud->getModel()->getTable())->ignore($this->crud->getCurrentEntry()),
            ],
            'year_id' => 'required|integer',
            'start' => 'date|required',
            'end' => 'date|required',
        ]);

        if (config('backpack.base.license_code')) {
            CRUD::addField(['type' => 'relationship', 'name' => 'year_id', 'inline_create' => true]);
        } else {
            CRUD::addField([
                'label' => __('Year'),
                'type' => 'select',
                'name' => 'year_id',
                'entity' => 'year',
                'model' => Year::class,
                'attribute' => 'name',
            ]);
        }

        CRUD::addFields([
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

            [
                'label' => __('Archived_s'),
                'type' => 'switch',
                'name' => 'archived',
            ],
        ]);
    }

    public function setupUpdateOperation()
    {
        $this->setupCreateOperation();
    }

    protected function fetchYear()
    {
        return $this->fetch(Year::class);
    }
}
