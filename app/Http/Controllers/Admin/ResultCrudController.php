<?php

namespace App\Http\Controllers\Admin;

use App\Models\Period;

// VALIDATION: change the requests to match your own file names if you need form validation
use App\Models\Result;
use App\Models\Comment;
use App\Models\Enrollment;
use App\Models\ResultType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use App\Http\Requests\ResultRequest as StoreRequest;
use App\Http\Requests\ResultRequest as UpdateRequest;
use Backpack\CRUD\app\Http\Controllers\CrudController;

/**
 * Class ResultCrudController
 * @package App\Http\Controllers\Admin
 * @property-read CrudPanel $crud
 */
class ResultCrudController extends CrudController
{

    public function __construct()
    {
        parent::__construct();
        $this->middleware('permission:evaluation.view', ['except' => ['show', 'store']]);
        //$this->middleware('permission:evaluation.edit', ['only' => ['store', 'update']]);
    }

    public function setup()
    {
        /*
        |--------------------------------------------------------------------------
        | CrudPanel Basic Information
        |--------------------------------------------------------------------------
        */
        $this->crud->setModel('App\Models\Enrollment');
        $this->crud->setRoute(config('backpack.base.route_prefix') . '/result');
        $this->crud->setEntityNameStrings('result', 'results');

        //$this->crud->denyAccess('update');
        $this->crud->denyAccess('delete');
        $this->crud->denyAccess('create');
        $this->crud->allowAccess('show');
        //$this->crud->removeAllButtons();

        /*
        |--------------------------------------------------------------------------
        | CrudPanel Configuration
        |--------------------------------------------------------------------------
        */
        $this->crud->setColumns([

            [
                'name' => 'id',
                'label' => "ID",
            ],

            [
            // STUDENT NAME
            'label' => __("Student"), // Table column heading
            'type' => "select",
            'entity' => 'student', // the method that defines the relationship in your Model
            'attribute' => "name", // foreign key attribute that is shown to user
            'searchLogic' => function ($query, $column, $searchTerm) {
                $query->orWhereHas('student', function ($q) use ($column, $searchTerm) {
                    $q->WhereHas('user', function ($q) use ($column, $searchTerm) {
                        $q->where('firstname', 'like', '%'.$searchTerm.'%')
                        ->orWhere('lastname', 'like', '%'.$searchTerm.'%');
                    });
                });
            }
            ],



            [
            // COURSE NAME
            'label' => __("Course"), // Table column heading
            'type' => "select",
            'name' => 'course_id', // the column that contains the ID of that connected entity;
            'entity' => 'course', // the method that defines the relationship in your Model
            'attribute' => "name", // foreign key attribute that is shown to user
            'model' => "App\Models\Course", // foreign key model
            ],

            [
            'name' => 'course.period.name',
            'label' => __('Period'),
            'type' => 'text'
            ],

            [
                // RESULT
                'label' => __("Result"), // Table column heading
                'type' => "select",
                'entity' => 'result', // the method that defines the relationship in your Model
                'attribute' => "result_type", // foreign key attribute that is shown to user
                'model' => "App\Models\Result", // foreign key model
                ],
        ]);
        

        // add asterisk for fields that are required in EnrollmentRequest
        $this->crud->setRequiredFields(UpdateRequest::class, 'edit');


          $this->crud->addFilter([
            'type' => 'simple',
            'name' => 'noresult',
            'label'=> __('No Result')
          ],
          false,
          function() {
              $this->crud->addClause('noResult'); 
          });

          $this->crud->addFilter([
            'type' => 'simple',
            'name' => 'hideparents',
            'label'=> __('Hide Parents')
          ],
          false,
          function() {
              $this->crud->addClause('real'); 
          });

        $this->crud->addFilter([
            'name' => 'period_id',
            'type' => 'select2',
            'label'=> __('Period')
          ], function() {
              return Period::all()->pluck('name', 'id')->toArray();
          }, function($value) { // if the filter is active
            $this->crud->addClause('period', $value); 
          });

          $this->crud->addFilter([ // select2_multiple filter
            'name' => 'result',
            'type' => 'select2_multiple',
            'label'=> __('Result')
          ], function() { // the options that show up in the select2
              return ResultType::all()->pluck('name', 'id')->toArray();
          }, function($values) { // if the filter is active
              foreach (json_decode($values) as $key => $value) {
                  $this->crud->query = $this->crud->query->whereHas('result', function ($query) use ($value) {
                      $query->where('result_type_id', $value);
                  });
              }
          });


    }
    /**
     * Store a newly created result in storage.
     *
     */
    public function store(Request $request)
    {

        $enrollment = Enrollment::findOrFail($request->input('enrollment'));

        if (Gate::forUser(backpack_user())->denies('edit-result', $enrollment)) {
            abort(403);
        }

        $result = Result::firstOrNew([
            'enrollment_id' => $enrollment->id
        ]);


/*         if($request->input('comment') !== null) {
            Comment::create([
                'commentable_id' => $result->id,
                'commentable_type' => Result::class,
                'body' => $request->input('comment'),
                'author_id' => \backpack_user()->id,
            ]);
        } */

        $result->result_type_id = $request->input('result');

        $result->save();

        return $result;
    }

    /**
     * Display the specified resource (result for a specific enrollment)
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

    public function update(UpdateRequest $request)
    {
        // todo protect this method
        // your additional operations before save here
        $redirect_location = parent::updateCrud($request);
        // your additional operations after save here
        // use $this->data['entry'] or $this->crud->entry
        return $redirect_location;
    }
}
