<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\TeacherRequest as StoreRequest;
// VALIDATION: change the requests to match your own file names if you need form validation
use App\Models\Teacher;
use App\Models\User;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
use Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
use Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

class TeacherCrudController extends CrudController
{
    use ListOperation;
    use CreateOperation;
    use UpdateOperation;

    public function setup()
    {
        CRUD::setModel(Teacher::class);
        CRUD::setRoute(config('backpack.base.route_prefix').'/teacher');
        CRUD::setEntityNameStrings(__('teacher'), __('teachers'));
        CRUD::addClause('withTrashed');
        CRUD::addButtonFromView('line', 'toggle', 'toggle', 'end');
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
                'name' => 'id', // the db column for the foreign key
                'entity' => 'user', // the method that defines the relationship in your Model
                'attribute' => 'name', // foreign key attribute that is shown to user
                'model' => User::class, // foreign key model
            ],
            [
                'name'  => 'max_week_hours',
                'label' => __('Weekly workable hours'),
                'type'  => 'number',
                'attributes' => ['step' => '0.01'],
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
        CRUD::addFields([
            [  // Select2
                'label' => 'User',
                'type' => 'select2',
                'name' => 'id', // the db column for the foreign key
                'entity' => 'user', // the method that defines the relationship in your Model
                'attribute' => 'name', // foreign key attribute that is shown to user
                'model' => User::class,
                'attributes' => [
                    'readonly'=>'readonly',
                    'disabled'=>'disabled',
                ],
            ],
            [
                'name'  => 'max_week_hours',
                'label' => __('Weekly workable hours'),
                'type'  => 'number',
                'attributes' => ['step' => '0.01'],
            ],
            [
                'name'  => 'hired_at',
                'label' => __('Hire Date'),
                'type'  => 'date',
            ],
        ]);
    }
}
