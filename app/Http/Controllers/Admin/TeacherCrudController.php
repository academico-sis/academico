<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;

// VALIDATION: change the requests to match your own file names if you need form validation
use App\Models\Teacher;
use Backpack\CRUD\CrudPanel;
use App\Http\Requests\TeacherRequest as StoreRequest;
use App\Http\Requests\TeacherRequest as UpdateRequest;
use Backpack\CRUD\app\Http\Controllers\CrudController;

/**
 * Class TeacherCrudController
 * @package App\Http\Controllers\Admin
 * @property-read CrudPanel $crud
 */
class TeacherCrudController extends CrudController
{
    public function setup()
    {
        /*
        |--------------------------------------------------------------------------
        | CrudPanel Basic Information
        |--------------------------------------------------------------------------
        */
        $this->crud->setModel('App\Models\Teacher');
        $this->crud->setRoute(config('backpack.base.route_prefix') . '/teacher');
        $this->crud->setEntityNameStrings('teacher', 'teachers');

        /*
        |--------------------------------------------------------------------------
        | CrudPanel Configuration
        |--------------------------------------------------------------------------
        */

        $this->crud->setColumns([
            [
                'label' => __('First Name'),
                'type' => "text",
                'name' => 'firstname'
            ],
            [
                'label' => __('Last Name'),
                'type' => "text",
                'name' => 'lastname'
            ],
            [
                'name'  => 'email',
                'label' => trans('backpack::permissionmanager.email'),
                'type'  => 'email',
            ],

            [
                'name'  => 'max_week_hours',
                'label' => __('Weekly workable hours'),
                'type'  => 'number',
            ],

            [
                'name'  => 'hired_at',
                'label' => __('Hire Date'),
                'type'  => 'date',
            ],

        ]);


        $this->crud->addFields([
            [
                'name'  => 'max_week_hours',
                'label' => __('Weekly workable hours'),
                'type'  => 'number',
            ],

            [
                'name'  => 'hired_at',
                'label' => __('Hire Date'),
                'type'  => 'date',
            ],


        ]);

        // add asterisk for fields that are required in TeacherRequest
        $this->crud->setRequiredFields(StoreRequest::class, 'create');
        $this->crud->setRequiredFields(UpdateRequest::class, 'edit');
    }

    public function store(StoreRequest $request)
    {
        $this->handlePasswordInput($request);

        $user = new User();
        $user->firstname = $request->input('firstname');
        $user->lastname = $request->input('lastname');
        $user->email = $request->input('email');
        $user->password = $request->input('password');
        $user->save();

        $teacher = new Teacher();
        $teacher->user_id = $user->id;
        $teacher->save();

        // link the teacher and the user models together
        $teacher->user()->associate($user);

        return $this->performSaveAction($teacher->id);

    }

    public function update(UpdateRequest $request)
    {
        // your additional operations before save here
        $redirect_location = parent::updateCrud($request);
        // your additional operations after save here
        // use $this->data['entry'] or $this->crud->entry
        return $redirect_location;
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
}
