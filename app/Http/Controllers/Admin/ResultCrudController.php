<?php

namespace App\Http\Controllers\Admin;

use App\Models\Enrollment;
use App\Models\Period;
use App\Models\Result;
use App\Models\ResultType;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;
use Illuminate\Support\Facades\Gate;

/**
 * Class ResultCrudController
 * Controller to monitor student results. No result can be added from here.
 * @property-read CrudPanel $crud
 */
class ResultCrudController extends CrudController
{
    use \Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation;

    public function __construct()
    {
        parent::__construct();
        $this->middleware('permission:evaluation.view', ['except' => ['show']]);
    }

    public function setup()
    {
        CRUD::setModel(\App\Models\Enrollment::class);
        CRUD::setRoute(config('backpack.base.route_prefix').'/result');
        CRUD::setEntityNameStrings('result', 'results');
    }

    public function setupListOperation()
    {
        CRUD::setColumns([
            [
                'name' => 'id',
                'label' => 'ID',
            ],
            [
                // STUDENT NAME
                'label' => __('Student'), // Table column heading
                'type' => 'select',
                'entity' => 'student', // the method that defines the relationship in your Model
                'attribute' => 'name', // foreign key attribute that is shown to user
                'searchLogic' => function ($query, $column, $searchTerm) {
                    $query->orWhereHas('student', function ($q) use ($column, $searchTerm) {
                        $q->WhereHas('user', function ($q) use ($column, $searchTerm) {
                            $q->where('firstname', 'like', '%'.$searchTerm.'%')
                            ->orWhere('lastname', 'like', '%'.$searchTerm.'%');
                        });
                    });
                },
            ],
            [
                // COURSE NAME
                'label' => __('Course'), // Table column heading
                'type' => 'select',
                'name' => 'course_id', // the column that contains the ID of that connected entity;
                'entity' => 'course', // the method that defines the relationship in your Model
                'attribute' => 'name', // foreign key attribute that is shown to user
                'model' => \App\Models\Course::class, // foreign key model
            ],
            [
                'name' => 'course.period.name',
                'label' => __('Period'),
                'type' => 'text',
            ],
            [
                // RESULT
                'label' => __('Result'), // Table column heading
                'type' => 'select',
                'entity' => 'result', // the method that defines the relationship in your Model
                'attribute' => 'result_type', // foreign key attribute that is shown to user
                'model' => \App\Models\Result::class, // foreign key model
            ],
        ]);

        CRUD::addFilter([
            'type' => 'simple',
            'name' => 'noresult',
            'label'=> __('No Result'),
        ],
          false,
          function () {
              CRUD::addClause('noResult');
          });

        CRUD::addFilter([
            'type' => 'simple',
            'name' => 'hideparents',
            'label'=> __('Hide Parents'),
        ],
          false,
          function () {
              CRUD::addClause('real');
          });

        CRUD::addFilter([
            'name' => 'period_id',
            'type' => 'select2',
            'label'=> __('Period'),
        ], function () {
            return Period::all()->pluck('name', 'id')->toArray();
        }, function ($value) { // if the filter is active
            CRUD::addClause('period', $value);
        });

        CRUD::addFilter([ // select2_multiple filter
            'name' => 'result',
            'type' => 'select2_multiple',
            'label'=> __('Result'),
        ], function () { // the options that show up in the select2
            return ResultType::all()->pluck('name', 'id')->toArray();
        }, function ($values) { // if the filter is active
            foreach (json_decode($values) as $key => $value) {
                $this->crud->query = $this->crud->query->whereHas('result', function ($query) use ($value) {
                    $query->where('result_type_id', $value);
                });
            }
        });
    }

    /**
     * Display the specified resource (result for a specific enrollment).
     */
    public function show($enrollment)
    {
        // the user is allowed to view the result if they are the student,
        // if they are the teacher of the course for this result
        // of if they have explicit permission to view any result

        $enrollment = Enrollment::findOrFail($enrollment);

        if (Gate::forUser(backpack_user())->denies('view-enrollment', $enrollment)) {
            abort(403);
        }

        $grades = $enrollment->grades;
        $skills = $enrollment->skills;
        $result = $enrollment->result;

        return view('results.show', compact('enrollment', 'grades', 'skills', 'result'));
    }
}
