<?php

namespace App\Http\Controllers\Admin;

use App\Models\Result;

// VALIDATION: change the requests to match your own file names if you need form validation
use App\Models\Enrollment;
use Illuminate\Http\Request;
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
        $this->middleware(['permission:evaluation.view']);
        $this->middleware('permission:evaluation.edit', ['only' => ['store', 'update']]);
    }

    public function setup()
    {
        /*
        |--------------------------------------------------------------------------
        | CrudPanel Basic Information
        |--------------------------------------------------------------------------
        */
        $this->crud->setModel('App\Models\Result');
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
                'label'     => 'Result', 
                'type'      => 'select',
                'entity'    => 'result_name',
                'attribute' => 'name', 
            ],

            [

                'name' => "student",
                'label' => "Student", // Table column heading
                'type' => "model_function",
                'function_name' => 'getStudentNameAttribute', // the method in your Model
            ],

            [
                'name' => "course",
                'label' => "Course", // Table column heading
                'type' => "model_function",
                'function_name' => 'getCourseNameAttribute', // the method in your Model
            ],

            [

                'name' => "period",
                'label' => "Period", // Table column heading
                'type' => "model_function",
                'function_name' => 'getCoursePeriodAttribute', // the method in your Model
            ],
        ]);

        $this->crud->addFilter([ // select2 filter
            'name' => 'result_type_id',
            'type' => 'select2',
            'label'=> 'Result'
          ], function() {
              return \App\Models\ResultType::all()->pluck('name', 'id')->toArray();
          }, function($value) { // if the filter is active
                  $this->crud->addClause('where', 'result_type_id', $value);
          });


          $this->crud->addFields([
            [  // Select
                'label' => "Result",
                'type' => 'select',
                'name' => 'result_type_id', // the db column for the foreign key
                'entity' => 'result_name', // the method that defines the relationship in your Model
                'attribute' => 'name', // foreign key attribute that is shown to user
                'model' => "App\Models\ResultType",
            ]             
          ]);


        // add asterisk for fields that are required in ResultRequest
        $this->crud->setRequiredFields(StoreRequest::class, 'create');
        $this->crud->setRequiredFields(UpdateRequest::class, 'edit');
    }
    /**
     * Store a newly created result in storage.
     *
     */
    public function store(Request $request)
    {

        //dd($request);

        $result = Result::firstOrNew([
            'enrollment_id' => $request->input('enrollment')
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
    public function show($result)
    {
        $enrollment = Result::findOrFail($result)->enrollment;
        
        if(backpack_user()->can('evaluation.view') || backpack_user()->id == $enrollment->student->id)
        {
            $grades = $enrollment->grades;
            $skills = $enrollment->skills;
            $result = $enrollment->result;
            return view('results.show', compact('enrollment', 'grades', 'skills', 'result'));
        }
        else {
            abort(403);
        }
    }

    public function update(UpdateRequest $request)
    {
        // your additional operations before save here
        $redirect_location = parent::updateCrud($request);
        // your additional operations after save here
        // use $this->data['entry'] or $this->crud->entry
        return $redirect_location;
    }
}
