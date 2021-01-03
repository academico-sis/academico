<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\GradeTypeRequest as StoreRequest;
use App\Http\Requests\GradeTypeRequest as UpdateRequest;
// VALIDATION: change the requests to match your own file names if you need form validation
use App\Models\GradeType;
use App\Models\GradeTypeCategory;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
use Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;
use Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
use Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

class GradeTypeCrudController extends CrudController
{
    use ListOperation;
    use CreateOperation;
    use UpdateOperation;
    use DeleteOperation;

    public function setup()
    {
        /*
        |--------------------------------------------------------------------------
        | CrudPanel Basic Information
        |--------------------------------------------------------------------------
        */
        CRUD::setModel(GradeType::class);
        CRUD::setRoute(config('backpack.base.route_prefix').'/gradetype');
        CRUD::setEntityNameStrings(__('grade type'), __('grade types'));

        /*
        |--------------------------------------------------------------------------
        | CrudPanel Configuration
        |--------------------------------------------------------------------------
        */

        CRUD::addColumns([
            ['name' => 'name', 'label' => 'Name'],
            ['name' => 'total', 'label' => 'Total'],
            [
                // 1-n relationship
                'label'     => 'Category', // Table column heading
                'type'      => 'select',
                'name'      => 'grade_type_category_id', // the column that contains the ID of that connected entity;
                'entity'    => 'category', // the method that defines the relationship in your Model
                'attribute' => 'name', // foreign key attribute that is shown to user
                'model'     => GradeTypeCategory::class,
            ],
        ]);

        CRUD::addFields([
            ['name' => 'name', 'label' => 'Name', 'type' => 'text'],
            ['name' => 'total', 'label' => 'Total', 'type' => 'text'],
            [  // Select
                'label'     => 'Category',
                'type'      => 'select',
                'name'      => 'grade_type_category_id', // the db column for the foreign key
                'entity'    => 'category', // the method that defines the relationship in your Model
                'attribute' => 'name', // foreign key attribute that is shown to user
                'model'     => GradeTypeCategory::class,
            ],
        ]);

        // add asterisk for fields that are required in GradeTypeRequest
        CRUD::setRequiredFields(StoreRequest::class, 'create');
        CRUD::setRequiredFields(UpdateRequest::class, 'edit');
    }

    protected function setupCreateOperation()
    {
        CRUD::setValidation(StoreRequest::class);
    }

    protected function setupUpdateOperation()
    {
        $this->setupCreateOperation();
    }
}
