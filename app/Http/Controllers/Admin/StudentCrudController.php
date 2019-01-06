<?php

namespace App\Http\Controllers\Admin;


use App\Models\User;
use Illuminate\Http\Request;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use App\Http\Requests\UserStoreCrudRequest as StoreRequest;
use App\Http\Requests\UserUpdateCrudRequest as UpdateRequest;

class StudentCrudController extends CrudController
{
    public function setup()
    {
        /*
        |--------------------------------------------------------------------------
        | BASIC CRUD INFORMATION
        |--------------------------------------------------------------------------
        */
        $this->crud->setModel(config('backpack.permissionmanager.models.user'));
        $this->crud->setRoute(config('backpack.base.route_prefix') . '/student');
        $this->crud->setEntityNameStrings('student', 'students');
        //$this->crud->removeAllButtons();

        $this->crud->removeButton('delete');
        $this->crud->removeButton('create');
        $this->crud->removeButton('update');

        $this->crud->allowAccess('show');

        $this->crud->addClause('student');

        $permissions = backpack_user()->getAllPermissions();
        
        if($permissions->contains('name', 'enrollments.create')) {
            $this->crud->addButtonFromView('line', 'enroll', 'enroll', 'beginning');
        }
        
        if(!$permissions->contains('name', 'students.edit')) {
            $this->crud->denyAccess('edit');
        }

        // Columns.
        $this->crud->setColumns([
            [
                'label' => "ID number", // Table column heading
                'type' => "text",
                'name' => 'idnumber'
            ],
            [
                'label' => "First Name", // Table column heading
                'type' => "text",
                'name' => 'firstname'
            ],
            [
                'label' => "Last Name", // Table column heading
                'type' => "text",
                'name' => 'lastname'
            ],
            [
                'name'  => 'email',
                'label' => trans('backpack::permissionmanager.email'),
                'type'  => 'email',
            ],
            
        ]);

        // Fields
        $this->crud->addFields([
            [  // Select2
                'label' => trans('academico.firstname'),
                'type' => 'text',
                'name' => 'firstname'
            ],
            [  // Select2
                'label' => trans('academico.lastname'),
                'type' => 'text',
                'name' => 'lastname'
            ],
            [
                'name'  => 'email',
                'label' => trans('backpack::permissionmanager.email'),
                'type'  => 'email',
            ],
            [
                'name'  => 'password',
                'label' => trans('backpack::permissionmanager.password'),
                'type'  => 'password',
            ],
            [
                'name'  => 'password_confirmation',
                'label' => trans('backpack::permissionmanager.password_confirmation'),
                'type'  => 'password',
            ],
            [
                'name'  => 'birthdate',
                'label' => trans('academico.birthdate'),
                'type'  => 'date',
            ],
            [
                'name'  => 'language',
                'label' => trans('academico.language'),
                'type'  => 'text',
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
        $student = User::findOrFail($student);
        return view('students/show', compact('student'));
    }


    /**
     * Handle password input fields.
     *
     * @param Request $request
     */
    protected function handlePasswordInput(Request $request)
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
}