<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\EvaluationTypeRequest as StoreRequest;
use App\Models\EvaluationType;
use App\Models\GradeType;
use App\Models\Skills\Skill;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
use Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;
use Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
use Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

class EvaluationTypeCrudController extends CrudController
{
    use ListOperation;
    use CreateOperation {store as traitStore; }
    use UpdateOperation {update as traitUpdate; }
    use DeleteOperation;

    public function setup()
    {
        CRUD::setModel(EvaluationType::class);
        CRUD::setRoute(config('backpack.base.route_prefix').'/evaluationtype');
        CRUD::setEntityNameStrings(__('evaluation type'), __('evaluation types'));
    }

    protected function setupListOperation()
    {
        CRUD::addColumn(['name' => 'name',
            'label' => 'Name', ]);
    }

    protected function setupCreateOperation()
    {
        CRUD::addField(['name' => 'name',
            'label' => 'Name',
            'type' => 'text', ]);

        CRUD::addField([    // Select2Multiple = n-n relationship (with pivot table)
            'label' => __('Grade Types'),
            'type' => 'select2_multiple',
            'name' => 'gradeTypes',

            // optional
            'entity' => 'gradeTypes',
            'model' => GradeType::class,
            'attribute' => 'complete_name',
            'pivot' => true,
            // on create&update, do you need to add/delete pivot table entries?
            'select_all' => true,
            // show Select All and Clear buttons?
        ]);

        CRUD::addField([    // Select2Multiple = n-n relationship (with pivot table)
            'label' => __('Skills'),
            'type' => 'select2_multiple',
            'name' => 'skills',

            // optional
            'entity' => 'skills',
            'model' => Skill::class,
            'attribute' => 'complete_name',
            'pivot' => true,
            // on create&update, do you need to add/delete pivot table entries?
            'select_all' => true,
            // show Select All and Clear buttons?
        ]);

        CRUD::setValidation(StoreRequest::class);
    }

    protected function setupUpdateOperation()
    {
        $this->setupCreateOperation();
    }

    public function store()
    {
        $value = $this->crud->getRequest()->input('name');
        $response = $this->traitStore();
        $entry = $this->crud->getCurrentEntry();

        $this->overrideTranslations($entry, $value);

        return $response;
    }

    private function overrideTranslations($entry, $value)
    {
        foreach (config('app.languages') as $i => $locale) {
            $entry->setTranslation('name', $locale, $value);
        }
        $entry->save();
    }

    public function update()
    {
        $value = $this->crud->getRequest()->input('name');
        $response = $this->traitUpdate();
        $entry = $this->crud->getCurrentEntry();

        $this->overrideTranslations($entry, $value);

        return $response;
    }
}
