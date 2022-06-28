<?php

namespace App\Http\Controllers\Admin;

use App\Models\GradeType;
use App\Models\GradeTypeCategory;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
use Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;
use Backpack\CRUD\app\Http\Controllers\Operations\FetchOperation;
use Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
use Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;
use Illuminate\Validation\Rule;

class GradeTypeCrudController extends CrudController
{
    use ListOperation;
    use CreateOperation;
    use UpdateOperation;
    use DeleteOperation;
    use FetchOperation;

    public function setup()
    {
        CRUD::setModel(GradeType::class);
        CRUD::setRoute(config('backpack.base.route_prefix').'/gradetype');
        CRUD::setEntityNameStrings(__('grade type'), __('grade types'));
    }

    protected function setupListOperation()
    {
        CRUD::addColumns([
            [
                'name' => 'name',
                'label' => __('Name'),
            ],
            [
                'name' => 'total',
                'label' => __('Total'),
            ],
            [
                'label' => __('Category'),
                'type' => 'select',
                'name' => 'grade_type_category_id',
                'entity' => 'category',
                'attribute' => 'name',
                'model' => GradeTypeCategory::class,
            ],
        ]);
    }

    protected function setupCreateOperation()
    {
        CRUD::setValidation([
            'name' => [
                'required',
                'min:1',
                'max:255',
                Rule::unique($this->crud->getModel()->getTable())->ignore($this->crud->getCurrentEntry()),
            ],
            'total' => 'required|integer|min:0',
        ]);

        CRUD::addFields([
            [
                'name' => 'name',
                'label' => __('Name'),
                'type' => 'text',
            ],

            [
                'name' => 'total',
                'label' => __('Total'),
                'type' => 'text',
            ],

            [
                'label' => __('Category'),
                'type' => 'select',
                'name'      => 'grade_type_category_id',
                'entity'    => 'category',
                'model'     => GradeTypeCategory::class,
                'attribute' => 'name',
                'ajax' => true,
            ],
        ]);
    }

    protected function setupUpdateOperation()
    {
        $this->setupCreateOperation();
    }

    protected function fetchCategory()
    {
        return $this->fetch(GradeTypeCategory::class);
    }
}
