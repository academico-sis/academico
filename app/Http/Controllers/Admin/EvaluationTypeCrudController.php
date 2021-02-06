<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\EvaluationTypeRequest as StoreRequest;
// VALIDATION: change the requests to match your own file names if you need form validation
use App\Models\EvaluationType;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
use Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;
use Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
use Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

class EvaluationTypeCrudController extends CrudController
{
    use ListOperation;
    use CreateOperation;
    use UpdateOperation;
    use DeleteOperation;

    public function setup()
    {
        CRUD::setModel(EvaluationType::class);
        CRUD::setRoute(config('backpack.base.route_prefix').'/evaluationtype');
        CRUD::setEntityNameStrings(__('evaluation type'), __('evaluation types'));
    }

    protected function setupListOperation()
    {
        CRUD::addColumn(['name' => 'name', 'label' => 'Name']);
    }

    protected function setupCreateOperation()
    {
        CRUD::addField(['name' => 'name', 'label' => 'Name', 'type' => 'text']);

        CRUD::addField([    // Select2Multiple = n-n relationship (with pivot table)
            'label'     => __('Grade Types'),
            'type'      => 'select2_multiple',
            'name'      => 'gradeTypes', // the method that defines the relationship in your Model

            // optional
            'entity'    => 'gradeTypes', // the method that defines the relationship in your Model
            'model'     => "App\Models\GradeType", // foreign key model
            'attribute' => 'complete_name', // foreign key attribute that is shown to user
            'pivot'     => true, // on create&update, do you need to add/delete pivot table entries?
            'select_all' => true, // show Select All and Clear buttons?
        ]);

        CRUD::addField([    // Select2Multiple = n-n relationship (with pivot table)
            'label'     => __('Skills'),
            'type'      => 'select2_multiple',
            'name'      => 'skills', // the method that defines the relationship in your Model

            // optional
            'entity'    => 'skills', // the method that defines the relationship in your Model
            'model'     => "App\Models\Skills\Skill", // foreign key model
            'attribute' => 'complete_name', // foreign key attribute that is shown to user
            'pivot'     => true, // on create&update, do you need to add/delete pivot table entries?
            'select_all' => true, // show Select All and Clear buttons?
        ]);

        CRUD::setValidation(StoreRequest::class);
    }

    protected function setupUpdateOperation()
    {
        $this->setupCreateOperation();
    }
}
