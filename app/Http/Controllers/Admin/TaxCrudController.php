<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\TaxRequest;
use App\Models\Tax;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
use Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;
use Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
use Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation;
use Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanel;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

class TaxCrudController extends CrudController
{
    use ListOperation;
    use CreateOperation;
    use UpdateOperation;
    use DeleteOperation;
    use ShowOperation;

    public function setup()
    {
        CRUD::setModel(Tax::class);
        CRUD::setRoute(config('backpack.base.route_prefix').'/tax');
        CRUD::setEntityNameStrings('tax', 'taxes');
    }

    protected function setupListOperation()
    {
        CRUD::setColumns([
            [
                'name' => 'id',
                'label' => 'ID',
            ],
            [
                'label' => __('Name'),
                'type' => 'text',
                'name' => 'name',
            ],
            [
                'label' => __('Value'),
                'type' => 'decimal',
                'name' => 'value',
                'suffix' => '%',
            ],
        ]);
    }

    protected function setupCreateOperation()
    {
        CRUD::setValidation(TaxRequest::class);

        CRUD::addFields([
            [
                'label' => __('Name'),
                'type' => 'text',
                'name' => 'name',
            ],
            [
                'label' => __('Value (0-100%)'),
                'type' => 'number',
                'name' => 'value',
            ],
        ]);
    }

    protected function setupUpdateOperation()
    {
        $this->setupCreateOperation();
    }
}
