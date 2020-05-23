<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\CourseEvaluationRequest;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

/**
 * Class CourseEvaluationCrudController.
 * @property-read CrudPanel $crud
 */
class CourseEvaluationCrudController extends CrudController
{
    use \Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;

    public function setup()
    {
        $this->crud->setModel(\App\Models\Course::class);
        $this->crud->setRoute(config('backpack.base.route_prefix').'/courseevaluation');
        $this->crud->setEntityNameStrings(__('course evaluation'), __('course evaluations'));

        CRUD::addClause('internal');
        CRUD::addClause('realcourses'); // filter out parent courses

        CRUD::addButtonFromView('line', 'skills', 'skills', 'end');
        CRUD::addButtonFromView('line', 'skillsevaluation', 'skillsevaluation', 'end');
        CRUD::addButtonFromView('line', 'grades', 'grades', 'end');
    }

    protected function setupListOperation()
    {
        CRUD::setColumns([
            [
                // RYTHM
                'label' => __('Rhythm'), // Table column heading
                'type' => 'select',
                'name' => 'rhythm_id', // the column that contains the ID of that connected entity;
                'entity' => 'rhythm', // the method that defines the relationship in your Model
                'attribute' => 'name', // foreign key attribute that is shown to user
                'model' => \App\Models\Rhythm::class, // foreign key model
            ],

            [
                // LEVEL
                'label' => __('Level'), // Table column heading
                'type' => 'select',
                'name' => 'level_id', // the column that contains the ID of that connected entity;
                'entity' => 'level', // the method that defines the relationship in your Model
                'attribute' => 'name', // foreign key attribute that is shown to user
                'model' => \App\Models\Level::class, // foreign key model
            ],

            [
                'name' => 'name', // The db column name
                'label' => __('Name'), // Table column heading
            ],

            [
                // TEACHER
                'label' => __('Teacher'), // Table column heading
                'type' => 'select',
                'name' => 'teacher_id', // the column that contains the ID of that connected entity;
                'entity' => 'teacher', // the method that defines the relationship in your Model
                'attribute' => 'name', // foreign key attribute that is shown to user
                'model' => \App\Models\Teacher::class, // foreign key model
            ],

            // EVALUATION METHODS
            [
                // n-n relationship (with pivot table)
                'label' => __('Evaluation method'), // Table column heading
                'type' => 'select_multiple',
                'name' => 'evaluation_type', // the method that defines the relationship in your Model
                'entity' => 'evaluation_type', // the method that defines the relationship in your Model
                'attribute' => 'name', // foreign key attribute that is shown to user
                'model' => \App\Models\EvaluationType::class, // foreign key model
            ],

        ]);

        CRUD::addFilter([ // select2 filter
            'name' => 'rhythm_id',
            'type' => 'select2',
            'label'=> __('Rhythm'),
        ], function () {
            return \App\Models\Rhythm::all()->pluck('name', 'id')->toArray();
        }, function ($value) { // if the filter is active
            CRUD::addClause('where', 'rhythm_id', $value);
        },
          function () { // if the filter is NOT active (the GET parameter "checkbox" does not exit)
          });

        CRUD::addFilter([ // select2 filter
            'name' => 'teacher_id',
            'type' => 'select2',
            'label'=> __('Teacher'),
        ], function () {
            return \App\Models\Teacher::all()->pluck('name', 'id')->toArray();
        }, function ($value) { // if the filter is active
            CRUD::addClause('where', 'teacher_id', $value);
        },
          function () { // if the filter is NOT active (the GET parameter "checkbox" does not exit)
          });

        CRUD::addFilter([ // select2 filter
            'name' => 'level_id',
            'type' => 'select2',
            'label'=> __('Level'),
        ], function () {
            return \App\Models\Level::all()->pluck('name', 'id')->toArray();
        }, function ($value) { // if the filter is active
            CRUD::addClause('where', 'level_id', $value);
        },
          function () { // if the filter is NOT active (the GET parameter "checkbox" does not exit)
          });

        CRUD::addFilter([ // select2 filter
            'name' => 'period_id',
            'type' => 'select2',
            'label'=> __('Period'),
        ], function () {
            return \App\Models\Period::all()->sortByDesc('id')->pluck('name', 'id')->toArray();
        }, function ($value) { // if the filter is active
            CRUD::addClause('where', 'period_id', $value);
        },
          function () { // if the filter is NOT active (the GET parameter "checkbox" does not exit)
              $period = \App\Models\Period::get_default_period()->id;
              CRUD::addClause('where', 'period_id', $period);
              $this->crud->request->request->add(['period_id' => $period]); // to make the filter look active
          });
    }

    protected function setupCreateOperation()
    {
        //$this->crud->setValidation(CourseEvaluationRequest::class);
    }

    protected function setupUpdateOperation()
    {
        CRUD::addField(
            // EVALUATION METHODS
            [
                // n-n relationship (with pivot table)
                'label' => __('Evaluation method'), // Table column heading
                'type' => 'select_multiple',
                'name' => 'evaluation_type', // the method that defines the relationship in your Model
                'entity' => 'evaluation_type', // the method that defines the relationship in your Model
                'attribute' => 'name', // foreign key attribute that is shown to user
                'model' => \App\Models\EvaluationType::class, // foreign key model
                'pivot' => true,
            ]
            );
    }
}
