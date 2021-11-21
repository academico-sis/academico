<?php

namespace App\Http\Controllers\Admin;

use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Period;
use App\Models\Result;
use App\Models\ResultType;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
use Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;
use Illuminate\Support\Facades\Gate;

/**
 * Class ResultCrudController
 * Controller to monitor student results. No result can be added from here.
 */
class ResultCrudController extends CrudController
{
    use ListOperation;
    use ShowOperation;

    public function __construct()
    {
        parent::__construct();
        $this->middleware('permission:evaluation.view', ['except' => ['show']]);
    }

    public function setup()
    {
        CRUD::setModel(Enrollment::class);
        CRUD::setRoute(config('backpack.base.route_prefix').'/result');
        CRUD::setEntityNameStrings(__('result'), __('results'));

        if (backpack_user()->hasRole('admin')) {
            CRUD::addButtonFromView('line', 'editResult', 'editResult', 'end');
        }
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
                'name' => 'student', // the method that defines the relationship in your Model
                'attribute' => 'name', // foreign key attribute that is shown to user
                'searchLogic' => function ($query, $column, $searchTerm) {
                    $query->orWhereHas('student', function ($q) use ($searchTerm) {
                        $q->WhereHas('user', function ($q) use ($searchTerm) {
                            $q->where('firstname', 'like', '%'.$searchTerm.'%')
                            ->orWhere('lastname', 'like', '%'.$searchTerm.'%')
                            ->orWhere('email', 'like', '%'.$searchTerm.'%')
                          ->orWhere('idnumber', 'like', '%'.$searchTerm.'%');
                        });
                    });
                },
            ],
            [
                // COURSE NAME
                'label' => __('Course'), // Table column heading
                'type' => 'relationship',
                'name' => 'course', // the column that contains the ID of that connected entity;
                'attribute' => 'name', // foreign key attribute that is shown to user
                'model' => Course::class, // foreign key model
            ],
            [
                'name' => 'course.period',
                'label' => __('Period'),
                'type' => 'relationship',
                'attribute' => 'name'
            ],
            [
                // RESULT
                'label' => __('Result'), // Table column heading
                'type' => 'relationship',
                'name' => 'result', // the method that defines the relationship in your Model
                'attribute' => 'result_type', // foreign key attribute that is shown to user
                'model' => Result::class, // foreign key model
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
          }
        );

        CRUD::addFilter(
            [
            'type' => 'simple',
            'name' => 'hideparents',
            'label'=> __('Hide Parents'),
        ],
            false,
            function () {
              CRUD::addClause('real');
          }
        );

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
            'type' => 'select2',
            'label'=> __('Result'),
        ], function () { // the options that show up in the select2
            return ResultType::all()->pluck('name', 'id')->toArray();
        }, function ($value) { // if the filter is active
            $this->crud->query = $this->crud->query->whereHas('result', function ($query) use ($value) {
                $query->where('result_type_id', $value);
            });
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

        $grades = $enrollment->grades->groupBy('grade_type_category');
        $skills = $enrollment->skill_evaluations;
        $result = $enrollment->result;
        $results = ResultType::all();
        $writeaccess = Gate::forUser(backpack_user())->check('edit-result', $enrollment);

        return view('results.show', compact('enrollment', 'writeaccess', 'grades', 'skills', 'result', 'results'));
    }
}
