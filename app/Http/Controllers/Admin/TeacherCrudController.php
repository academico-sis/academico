<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\TeacherRequest as StoreRequest;
// VALIDATION: change the requests to match your own file names if you need form validation
use App\Http\Requests\TeacherRequest as UpdateRequest;
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
        CRUD::setModel(\App\Models\Teacher::class);
        CRUD::setRoute(config('backpack.base.route_prefix').'/teacher');
        CRUD::setEntityNameStrings('teacher', 'teachers');
    }

    public function setupListOperation()
    {
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
    }

    public function setupCreateOperation()
    {
        CRUD::setValidation(StoreRequest::class);
        CRUD::addFields([
            [  // Select2
                'label' => 'User',
                'type' => 'select2',
                'name' => 'user_id', // the db column for the foreign key
                'entity' => 'user', // the method that defines the relationship in your Model
                'attribute' => 'name', // foreign key attribute that is shown to user
                'model' => \App\Models\User::class, // foreign key model
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
    }

    protected function setupUpdateOperation()
    {
        CRUD::setValidation(UpdateRequest::class);
        CRUD::addFields([
            [  // Select2
                'label' => 'User',
                'type' => 'select2',
                'name' => 'user_id', // the db column for the foreign key
                'entity' => 'user', // the method that defines the relationship in your Model
                'attribute' => 'name', // foreign key attribute that is shown to user
                'model' => \App\Models\User::class,
                'attributes' => [
                    'readonly'=>'readonly',
                    'disabled'=>'disabled',
                ],
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
    }
}
