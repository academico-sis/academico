<?php

namespace App\Http\Controllers\Admin;

use App\Models\EvaluationType;
use App\Models\Level;
use App\Models\Skills\Skill;
use App\Models\Skills\SkillType;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
use Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;
use Backpack\CRUD\app\Http\Controllers\Operations\FetchOperation;
use Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
use Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Validation\Rule;
use League\Csv\Reader;

class SkillCrudController extends CrudController
{
    use ListOperation;
    use CreateOperation;
    use UpdateOperation;
    use DeleteOperation;
    use FetchOperation;

    public function setup()
    {
        CRUD::setModel(Skill::class);
        CRUD::setRoute(config('backpack.base.route_prefix').'/skill');
        CRUD::setEntityNameStrings(__('skill'), __('skills'));

        CRUD::enableExportButtons();

        if (backpack_user()->hasRole('admin')) {
            CRUD::addButtonFromView(stack: 'bottom', name: 'bulkImportSkills', view: 'bulkImportSkills', position: 'beginning');
        }
    }

    protected function setupListOperation()
    {
        CRUD::setColumns([
            [
                'label' => __('Type'),
                'type' => 'select',
                'name' => 'skillType',
                'entity' => 'skillType',
                'attribute' => 'name',
                'model' => 'skillType',
            ],
            [
                'label' => __('Name'),
                'type' => 'text',
                'name' => 'name',
            ],
            [
                'label' => __('Level'),
                'type' => 'select',
                'name' => 'level',
                'entity' => 'level',
                'attribute' => 'name',
                'model' => 'level',
            ],
        ]);

        CRUD::addFilter([
            'name' => 'level_id',
            'type' => 'select2',
            'label' => 'Level',
        ], fn () => Level::all()->pluck('name', 'id')->toArray(), function ($value) {
            CRUD::addClause('where', 'level_id', $value);
        });

        CRUD::addFilter([
            'name' => 'skill_type_id',
            'type' => 'select2',
            'label' => 'Type',
        ], fn () => SkillType::all()->pluck('name', 'id')->toArray(), function ($value) {
            CRUD::addClause('where', 'skill_type_id', $value);
        });
    }

    protected function setupCreateOperation()
    {
        CRUD::setValidation([
            'name' => [
                'required',
                'min:1',
                'max:1000',
                Rule::unique($this->crud->getModel()->getTable())->ignore($this->crud->getCurrentEntry()),
            ],
            'level_id' => 'required',
            'skill_type_id' => 'required',
        ]);

        CRUD::addFields([
            [
                'label' => __('Type'),
                'type' => 'select',
                'name' => 'skill_type_id',
                'entity' => 'skillType',
                'attribute' => 'name',
                'model' => SkillType::class,
            ],
            [
                'label' => __('Name'),
                'type' => 'text',
                'name' => 'name',
            ],
            [
                'label' => __('Level'),
                'type' => 'select',
                'name' => 'level_id',
                'entity' => 'level',
                'attribute' => 'name',
                'model' => Level::class,
            ]
        ]);
    }

    protected function setupUpdateOperation()
    {
        $this->setupCreateOperation();
    }

    protected function fetchSkill_type()
    {
        return $this->fetch(SkillType::class);
    }

    protected function setupImportRoutes($segment, $routeName, $controller)
    {
        Route::get($segment.'/import', [
            'as'        => $routeName.'.getImport',
            'uses'      => $controller.'@getImportForm',
            'operation' => 'import',
        ]);
        Route::post($segment.'/import', [
            'as'        => $routeName.'.postImport',
            'uses'      => $controller.'@postImportForm',
            'operation' => 'import',
        ]);
    }

    public function getImportForm()
    {
        $this->crud->hasAccessOrFail('update');
        $this->crud->setOperation('Import');

        // get the info for that entry
        $this->data['crud'] = $this->crud;
        $this->data['title'] = 'Import '.$this->crud->entity_name;

        return view('vendor.backpack.crud.import-skills', $this->data);
    }

    public function postImportForm(Request $request)
    {
        $this->crud->hasAccessOrFail('update');

        if (!$request->file('file')) {
            \Alert::error('Le fichier est invalide.')->flash();
            return \Redirect::to($this->crud->route);
        }

        try {
            $csv = Reader::createFromString(content: $request->file('file')->getContent());
        } catch (\Exception $e) {
            \Alert::error('Le fichier est invalide.')->flash();
            return \Redirect::to($this->crud->route);
        }

        $csv->setDelimiter(',');
        $csv->setHeaderOffset(0);

        // check data before doing anything
        foreach ($csv as $record) {
            $data = array_values($record);
            if (! $data[2] || !$level = Level::firstWhere(['name' => $data[2]])) {
                \Alert::error('Le fichier contient des niveaux invalides.')->flash();
                return \Redirect::to($this->crud->route);
            }

            if (! $data[1]) {
                \Alert::error('Le fichier contient des compétences invalides.')->flash();
                return \Redirect::to($this->crud->route);
            }

            if (! $data[0] || !$skillType = SkillType::firstWhere(['shortname' => $data[0]]) ?? SkillType::firstWhere(['name' => $data[0]])) {
                \Alert::error('Le fichier contient des catégories de compétences invalides.')->flash();
                return \Redirect::to($this->crud->route);
            }
        }

        if ($groupName = $request->group) {
            if (EvaluationType::firstWhere(['name' => $request->group])) {
                \Alert::error("Ce nom est déjà utilisé pour un autre type d'évaluation.")->flash();
                return \Redirect::to($this->crud->route);
            }

            $group = EvaluationType::create([
                'name' => $groupName,
            ]);
        }

        foreach ($csv as $record) {
            $data = array_values($record);

            $skill = Skill::create([
                'skill_type_id' => $skillType->id,
                'name' => $data[1],
                'level_id' => $level->id,
            ]);

            if (isset($group)) {
                $group->skills()->save($skill);
            }
        }

        return \Redirect::to($this->crud->route);
    }

}
