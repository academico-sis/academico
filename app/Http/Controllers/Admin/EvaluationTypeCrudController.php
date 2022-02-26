<?php

namespace App\Http\Controllers\Admin;

use App\Models\EvaluationType;
use App\Models\GradeType;
use App\Models\Skills\Skill;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
use Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;
use Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
use Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;
use Illuminate\Validation\Rule;

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
        CRUD::addColumn([
            'name' => 'name',
            'label' => __('Name'),
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
        ]);

        CRUD::addField([
            'name' => 'name',
            'label' => __('Name'),
            'type' => 'text',
        ]);

        CRUD::addField([
            'label' => __('Grade Types'),
            'type' => 'select2_multiple',
            'name' => 'gradeTypes',
            'entity' => 'gradeTypes',
            'model' => GradeType::class,
            'attribute' => 'complete_name',
            'pivot' => true,
            'select_all' => true,
        ]);

        CRUD::addField([
            'label' => __('Skills'),
            'type' => 'select2_multiple',
            'name' => 'skills',
            'entity' => 'skills',
            'model' => Skill::class,
            'attribute' => 'complete_name',
            'pivot' => true,
            'select_all' => true,
        ]);
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
        foreach (config('academico.languages') as $i => $locale) {
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
