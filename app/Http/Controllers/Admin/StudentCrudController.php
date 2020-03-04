<?php

namespace App\Http\Controllers\Admin;

use App\Models\Period;
use App\Models\Student;
use App\Models\LeadType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use App\Http\Requests\UserStoreCrudRequest as StoreRequest;
use App\Http\Requests\UserUpdateCrudRequest as UpdateRequest;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

class StudentCrudController extends CrudController
{

    use \Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation { show as traitShow; }


    public function __construct()
    {
        parent::__construct();
        $this->middleware('permission:enrollments.view', ['except' => ['dataAjax', 'show']]);
        $this->middleware('permission:student.edit', ['except' => ['index', 'show', 'search', 'dataAjax']]);
    }
    
    
    public function setup()
    {
        /*
        |--------------------------------------------------------------------------
        | BASIC CRUD INFORMATION
        |--------------------------------------------------------------------------
        */
        CRUD::setModel('App\Models\Student');
        CRUD::setRoute(config('backpack.base.route_prefix') . '/student');
        CRUD::setEntityNameStrings(__('student'), __('students'));
        //CRUD::removeAllButtons();

        CRUD::removeButton('delete');
        CRUD::removeButton('create');
        CRUD::removeButton('update');

        $permissions = backpack_user()->getAllPermissions();
        
        if($permissions->contains('name', 'enrollments.create')) {
            CRUD::addButtonFromView('line', 'selectCourse', 'selectCourse', 'beginning');
        }

        if(backpack_user()->hasRole('admin'))
        {
            CRUD::enableExportButtons();
        }
        

        CRUD::orderBy('created_at', 'desc');

        // Columns.
        CRUD::setColumns([
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

            [
                // n-n relationship (with pivot table)
                'label' => "Phone", // Table column heading
                'type' => "select_multiple",
                'name' => 'phone', // the method that defines the relationship in your Model
                'entity' => 'phone', // the method that defines the relationship in your Model
                'attribute' => "phone_number", // foreign key attribute that is shown to user
                'model' => "App\Models\PhoneNumber", // foreign key model
             ],

            [
                // 1-n relationship
                'label' => __("Status"), // Table column heading
                'type' => "select",
                'name' => 'lead_type_id', // the column that contains the ID of that connected entity;
                'entity' => 'leadType', // the method that defines the relationship in your Model
                'attribute' => "name", // foreign key attribute that is shown to user
                'model' => "App\Models\LeadType", // foreign key model
             ],

            
        ]);


        CRUD::addFilter([ // select2_multiple filter
            'name' => 'enrolled',
            'type' => 'select2_multiple',
            'label'=> __('Is Enrolled in')
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



        CRUD::addFilter([ // select2_multiple filter
            'name' => 'notenrolled',
            'type' => 'select2_multiple',
            'label'=> __('Is Not Enrolled in')
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

          CRUD::addFilter([ // select2 filter
            'name' => 'lead_status_is',
            'type' => 'select2_multiple',
            'label'=> __('Status is')
          ], function() {
              return LeadType::all()->pluck('name', 'id')->toArray();
          }, function($values) { // if the filter is active
            foreach (json_decode($values) as $key => $value) {
                 CRUD::addClause('orWhere', 'lead_type_id', $value);
             }
            });


            CRUD::addFilter([ // select2 filter
                'name' => 'lead_status_isnot',
                'type' => 'select2_multiple',
                'label'=> __('Status is not')
              ], function() {
                  return LeadType::all()->pluck('name', 'id')->toArray();
              }, function($values) { // if the filter is active
                foreach (json_decode($values) as $key => $value) {
                     CRUD::addClause('where', 'lead_type_id', '!=', $value);
                 }
                });


        // Fields
        CRUD::addFields([
            [
                'label' => trans('firstname'),
                'type' => 'text',
                'name' => 'firstname'
            ],
            [
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
            ]

        ]);

    }


    public function show($student)
    {
        $student = Student::findOrFail($student);

        if (!backpack_user()->can('show', $student) && !backpack_user()->can('enrollments.view'))
        {
            abort(403);
        }

        $comments = $student->comments;
        return view('students/show', [
            'student' => $student,
            'comments' => $comments,
            'lead_types' => LeadType::all(),
            'attendances' => $student->periodAttendance()->get(),
            'writeaccess' => backpack_user()->can('enrollments.edit') ?? 0
        ]);
    }


    protected function setupUpdateOperation()
    {
        CRUD::setValidation(UpdateRequest::class);
    }


    /**
     * Handle password input fields.
     * TODO REMOVE THIS, NOT NEEDED?
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

            $data = DB::table("students")
                    ->select("students.id","users.firstname", "users.lastname")
                    ->join('users', 'students.user_id', '=', 'users.id')
                    ->where('users.firstname','LIKE',"%$search%")
                    ->orWhere('users.lastname','LIKE',"%$search%")
            		->get();
        }

        return response()->json($data);

    }


}