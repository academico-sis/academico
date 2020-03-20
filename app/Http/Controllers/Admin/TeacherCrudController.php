<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\TeacherRequest as StoreRequest;
// VALIDATION: change the requests to match your own file names if you need form validation
use App\Http\Requests\TeacherRequest as UpdateRequest;
use App\Models\Teacher;
use App\Models\User;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;
use Backpack\CRUD\CrudPanel;

/**
 * Class TeacherCrudController.
 * @property-read CrudPanel $crud
 */
class TeacherCrudController extends CrudController
{
    use \Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;

    public function setup()
    {
        /*
        |--------------------------------------------------------------------------
        | CrudPanel Basic Information
        |--------------------------------------------------------------------------
        */
        CRUD::setModel('App\Models\Teacher');
        CRUD::setRoute(config('backpack.base.route_prefix').'/teacher');
        CRUD::setEntityNameStrings('teacher', 'teachers');

        /*
        |--------------------------------------------------------------------------
        | CrudPanel Configuration
        |--------------------------------------------------------------------------
        */

        CRUD::setColumns([
            [
                'label' => __('First Name'),
                'type' => 'text',
                'name' => 'firstname',
            ],
            [
                'label' => __('Last Name'),
                'type' => 'text',
                'name' => 'lastname',
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

        CRUD::addFields([

            [  // Select2
                'label' => 'User',
                'type' => 'select2',
                'name' => 'user_id', // the db column for the foreign key
                'entity' => 'user', // the method that defines the relationship in your Model
                'attribute' => 'name', // foreign key attribute that is shown to user
                'model' => "App\Models\User", // foreign key model
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

        // add asterisk for fields that are required in TeacherRequest
        CRUD::setRequiredFields(StoreRequest::class, 'create');
        CRUD::setRequiredFields(UpdateRequest::class, 'edit');
    }

    protected function setupCreateOperation()
    {
        CRUD::setValidation(StoreRequest::class);
    }

    protected function setupUpdateOperation()
    {
        CRUD::setValidation(UpdateRequest::class);
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
