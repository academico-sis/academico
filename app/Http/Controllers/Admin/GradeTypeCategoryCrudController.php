<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\GradeTypeCategoryRequest;
use App\Models\GradeTypeCategory;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
use Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;
use Backpack\CRUD\app\Http\Controllers\Operations\InlineCreateOperation;
use Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
use Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation;
use Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanel;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

class GradeTypeCategoryCrudController extends CrudController
{
    use ListOperation;
    use CreateOperation;
    use InlineCreateOperation;
    use UpdateOperation;
    use DeleteOperation;
    use ShowOperation;

    public function setup()
    {
        CRUD::setModel(GradeTypeCategory::class);
        CRUD::setRoute(config('backpack.base.route_prefix').'/gradetypecategory');
        CRUD::setEntityNameStrings('gradetypecategory', 'grade_type_categories');
    }

    protected function setupListOperation()
    {
        CRUD::addColumn([
            'name' => 'name',
            'label' => __('Label'),
        ]);
    }

    protected function setupCreateOperation()
    {
        CRUD::setValidation(GradeTypeCategoryRequest::class);

        CRUD::addField([
            'name' => 'name',
            'label' => __('Label'),
        ]);
    }

    protected function setupUpdateOperation()
    {
        $this->setupCreateOperation();
    }
}
