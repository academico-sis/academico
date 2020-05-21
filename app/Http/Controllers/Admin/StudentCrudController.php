<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\StudentRequest;
use App\Models\LeadType;
use App\Models\Period;
use App\Models\Student;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

class StudentCrudController extends CrudController
{
    use \Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation { show as traitShow; }

    public function __construct()
    {
        parent::__construct();
        $this->middleware('permission:enrollments.view', ['except' => ['dataAjax', 'show']]);
        $this->middleware('permission:student.edit', ['except' => ['index', 'show', 'search', 'dataAjax']]);
    }

    public function setup()
    {
        CRUD::setModel(\App\Models\Student::class);
        CRUD::setRoute(config('backpack.base.route_prefix').'/student');
        CRUD::setEntityNameStrings(__('student'), __('students'));
        //CRUD::removeAllButtons();

        CRUD::removeButton('delete');
        CRUD::removeButton('create');
        CRUD::removeButton('update');

        CRUD::setListView('students.list');

        $permissions = backpack_user()->getAllPermissions();

        if ($permissions->contains('name', 'enrollments.edit')) {
            CRUD::addButtonFromView('line', 'selectCourse', 'selectCourse', 'beginning');
        }

        if (backpack_user()->hasPermissionTo('enrollments.view')) {
            CRUD::enableExportButtons();
        }

        CRUD::orderBy('created_at', 'desc');
    }

    public function setupListOperation()
    {
        // Columns.
        CRUD::setColumns([
            [
                'label' => 'ID number',
                'type' => 'text',
                'name' => 'idnumber',
            ],
            [
                'label' => __('Name'),
                'type' => 'text',
                'name' => 'name',
                'searchLogic' => function ($query, $column, $searchTerm) {
                    $query->orWhereHas('user', function ($q) use ($column, $searchTerm) {
                        $q->where('firstname', 'like', '%'.$searchTerm.'%')
                          ->orWhere('lastname', 'like', '%'.$searchTerm.'%');
                    });
                },
            ],

            [
                'name'  => 'email',
                'label' => trans('backpack::permissionmanager.email'),
                'type'  => 'text',
            ],

            [
                // n-n relationship (with pivot table)
                'label' => 'Phone', // Table column heading
                'type' => 'select_multiple',
                'name' => 'phone', // the method that defines the relationship in your Model
                'entity' => 'phone', // the method that defines the relationship in your Model
                'attribute' => 'phone_number', // foreign key attribute that is shown to user
                'model' => \App\Models\PhoneNumber::class, // foreign key model
            ],

            [
                // 1-n relationship
                'label' => __('Status'), // Table column heading
                'type' => 'select',
                'name' => 'lead_type_id', // the column that contains the ID of that connected entity;
                'entity' => 'leadType', // the method that defines the relationship in your Model
                'attribute' => 'name', // foreign key attribute that is shown to user
                'model' => \App\Models\LeadType::class, // foreign key model
            ],

        ]);

        CRUD::addFilter([ // select2 filter
            'name' => 'enrolled',
            'type' => 'select2',
            'label'=> __('Is Enrolled in'),
        ], function () {
            return Period::all()->pluck('name', 'id')->toArray();
        }, function ($value) { // if the filter is active
            $this->crud->query = $this->crud->query->whereHas('enrollments', function ($query) use ($value) {
                return $query->whereHas('course', function ($q) use ($value) {
                    $q->where('period_id', $value);
                });
            });
        },
          function () { // if the filter is NOT active (the GET parameter "checkbox" does not exit)
          });

        CRUD::addFilter([ // select2_multiple filter
            'name' => 'notenrolled',
            'type' => 'select2_multiple',
            'label'=> __('Is Not Enrolled in'),
        ], function () { // the options that show up in the select2
            return Period::all()->pluck('name', 'id')->toArray();
        }, function ($values) { // if the filter is active
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
            'type' => 'select2',
            'label'=> __('Status is'),
        ], function () {
            return LeadType::all()->pluck('name', 'id')->toArray();
        }, function ($value) { // if the filter is active
            CRUD::addClause('where', 'lead_type_id', $value);
        });

        CRUD::addFilter([ // select2 filter
            'name' => 'lead_status_isnot',
            'type' => 'select2',
            'label'=> __('Status is not'),
        ], function () {
            return LeadType::all()->pluck('name', 'id')->toArray();
        }, function ($value) { // if the filter is active
            CRUD::addClause('where', 'lead_type_id', '!=', $value);
        });
    }

    public function setupCreateOperation()
    {
        CRUD::setValidation(StudentRequest::class);

        // Fields
        CRUD::addFields([
            [
                'label' => trans('firstname'),
                'type' => 'text',
                'name' => 'firstname',
            ],
            [
                'label' => trans('lastname'),
                'type' => 'text',
                'name' => 'lastname',
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

    public function show($student)
    {
        $student = Student::findOrFail($student);

        if (! backpack_user()->can('show', $student) && ! backpack_user()->can('enrollments.view')) {
            abort(403);
        }

        $comments = $student->comments;

        return view('students/show', [
            'student' => $student,
            'comments' => $comments,
            'lead_types' => LeadType::all(),
            'attendances' => $student->periodAttendance()->get(),
            'writeaccess' => backpack_user()->can('enrollments.edit') ?? 0,
        ]);
    }

    public function setupUpdateOperation()
    {
        $this->setupCreateOperation();
    }
}
