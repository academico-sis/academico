<?php

namespace App\Http\Controllers\Admin;

use App\Models\Course;
use App\Models\Enrollment;
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
                'label' => __('Student'),
                'type' => 'select',
                'name' => 'student',
                'attribute' => 'name',
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
                'label' => __('Course'),
                'type' => 'select',
                'name' => 'course',
                'attribute' => 'name',
                'model' => Course::class,
            ],
            [
                'name' => 'course.period',
                'label' => __('Period'),
                'type' => 'select',
                'attribute' => 'name',
            ],
            [
                'label' => __('Result'),
                'type' => 'select',
                'name' => 'result',
                'attribute' => 'result_type',
                'model' => Result::class,
            ],
        ]);
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
        $skills = $enrollment->skillEvaluations;
        $result = $enrollment->result;
        $results = ResultType::all();
        $writeaccess = Gate::forUser(backpack_user())->check('edit-result', $enrollment);

        return view('results.show', ['enrollment' => $enrollment, 'writeaccess' => $writeaccess, 'grades' => $grades, 'skills' => $skills, 'result' => $result, 'results' => $results]);
    }
}
