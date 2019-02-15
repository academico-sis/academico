<?php

namespace App\Http\Controllers\Admin;

use App\Models\Period;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use App\Http\Requests\UserStoreCrudRequest as StoreRequest;
use App\Http\Requests\UserUpdateCrudRequest as UpdateRequest;

class StudentCrudController extends CrudController
{

    public function __construct()
    {
        parent::__construct();
        $this->middleware('permission:enrollments.view', ['except' => ['dataAjax']]);
    }
    
    public function setup()
    {
        /*
        |--------------------------------------------------------------------------
        | BASIC CRUD INFORMATION
        |--------------------------------------------------------------------------
        */
        $this->crud->setModel('App\Models\Student');
        $this->crud->setRoute(config('backpack.base.route_prefix') . '/student');
        $this->crud->setEntityNameStrings(__('student'), __('students'));
        //$this->crud->removeAllButtons();

        $this->crud->removeButton('delete');
        $this->crud->removeButton('create');
        $this->crud->removeButton('update');

        $this->crud->allowAccess('show');
        $this->crud->denyAccess('create');

        //$this->crud->addClause('student');

        $permissions = backpack_user()->getAllPermissions();
        
        if($permissions->contains('name', 'enrollments.create')) {
            $this->crud->addButtonFromView('line', 'selectCourse', 'selectCourse', 'beginning');
        }
        
        if(!$permissions->contains('name', 'students.edit')) {
            $this->crud->denyAccess('edit');
        }

        $this->crud->orderBy('created_at', 'desc');

        // Columns.
        $this->crud->setColumns([
            [
                'label' => "ID number",
                'type' => "text",
                'name' => 'idnumber'
            ],
            [
                'label' => __('Name'),
                'type' => "text",
                'name' => 'name',
                'searchLogic' => function ($query, $column, $searchTerm) {
                    $query->orWhereHas('user', function ($q) use ($column, $searchTerm) {
                        $q->where('firstname', 'like', '%'.$searchTerm.'%')
                          ->orWhere('lastname', 'like', '%'.$searchTerm.'%');
                    });
                }
            ],
            [
                'name'  => 'email',
                'label' => trans('backpack::permissionmanager.email'),
                'type'  => 'text',
            ],
            
        ]);


        $this->crud->addFilter([ // select2_multiple filter
            'name' => 'enrolled',
            'type' => 'select2_multiple',
            'label'=> 'Is Enrolled in'
          ], function() { // the options that show up in the select2
              return Period::all()->pluck('name', 'id')->toArray();
          }, function($values) { // if the filter is active
              foreach (json_decode($values) as $key => $value) {
                  $this->crud->query = $this->crud->query->whereHas('enrollments', function ($query) use ($value) {
                    return $query->whereHas('course', function ($q) use ($value) {
                        $q->where('period_id', $value);
                    });
                  });
              }
          });


        $this->crud->addFilter([ // select2_multiple filter
            'name' => 'notenrolled',
            'type' => 'select2_multiple',
            'label'=> 'Is Not Enrolled in'
          ], function() { // the options that show up in the select2
              return Period::all()->pluck('name', 'id')->toArray();
          }, function($values) { // if the filter is active
              foreach (json_decode($values) as $key => $value) {
                  $this->crud->query = $this->crud->query->whereDoesntHave('enrollments', function ($query) use ($value) {
                    return $query->whereHas('course', function ($q) use ($value) {
                        $q->where('period_id', $value);
                    });
                  });
              }
          });


        // Fields
        $this->crud->addFields([
            [  // Select2
                'label' => trans('firstname'),
                'type' => 'text',
                'name' => 'firstname'
            ],
            [  // Select2
                'label' => trans('lastname'),
                'type' => 'text',
                'name' => 'lastname'
            ],
            [
                'name'  => 'email',
                'label' => trans('backpack::permissionmanager.email'),
                'type'  => 'email',
            ],
            [
                'name'  => 'birthdate',
                'label' => trans('birthdate'),
                'type'  => 'date',
            ],

        ]);

    }

    /**
     * Store a newly created resource in the database.
     *
     * @param StoreRequest $request - type injection used for validation using Requests
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(StoreRequest $request)
    {
        $this->handlePasswordInput($request);

        return parent::storeCrud($request);
    }

    /**
     * Update the specified resource in the database.
     *
     * @param UpdateRequest $request - type injection used for validation using Requests
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(UpdateRequest $request)
    {
        $this->handlePasswordInput($request);

        return parent::updateCrud($request);
    }

    public function show($student)
    {
        $student = Student::findOrFail($student);
        $comments = $student->comments;

        return view('students/show', compact('student', 'comments'));
    }


    /**
     * Handle password input fields.
     *
     * @param Request $request
     */
    protected function handlePasswordInput(StoreRequest $request)
    {
        // Remove fields not present on the user.
        $request->request->remove('password_confirmation');

        // Encrypt password if specified.
        if ($request->input('password')) {
            $request->request->set('password', bcrypt($request->input('password')));
        } else {
            $request->request->remove('password');
        }
    }

    public function dataAjax(Request $request)

    {

    	$data = [];


        if($request->has('q')){

            $search = $request->q;

            $data = DB::table("users")
            		->select("id","firstname", "lastname")
                    ->where('firstname','LIKE',"%$search%")
                    ->orWhere('lastname','LIKE',"%$search%")

            		->get();
        }


        return response()->json($data);

    }


}