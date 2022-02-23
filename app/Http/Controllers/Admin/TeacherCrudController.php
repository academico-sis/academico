<?php

namespace App\Http\Controllers\Admin;

use App\Models\Teacher;
use App\Models\User;
use App\Traits\UsernameTrait;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
use Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
use Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class TeacherCrudController extends CrudController
{
    use ListOperation;
    use CreateOperation { store as traitStore; }
    use UpdateOperation;
    use UsernameTrait;

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
        CRUD::addColumns([
            [
                'label' => __('ID number'),
                'type' => 'text',
                'name' => 'idnumber',
            ],
            [
                'label' => __('Last Name'),
                'type' => 'select',
                'key' => 'lastname',
                'name' => 'user',
                'attribute' => 'lastname',
                'model' => User::class,
                'orderable' => true,
                'orderLogic' => fn ($query, $column, $columnDirection) => $query->leftJoin('users', 'users.id', '=', 'students.id')
                    ->orderBy('users.lastname', $columnDirection)->select('students.*'),
                'searchLogic' => function ($query, $column, $searchTerm) {
                    $query->orWhereHas('user', function ($q) use ($searchTerm) {
                        $q->where('lastname', 'like', '%'.$searchTerm.'%');
                    });
                },
            ],

            [
                'label' => __('First Name'),
                'type' => 'select',
                'key' => 'firstname',
                'name' => 'user',
                'attribute' => 'firstname',
                'model' => User::class,
                'orderable' => true,
                'orderLogic' => fn ($query, $column, $columnDirection) => $query->leftJoin('users', 'users.id', '=', 'teachers.id')
                    ->orderBy('users.firstname', $columnDirection)->select('teachers.*'),
                'searchLogic' => function ($query, $column, $searchTerm) {
                    $query->orWhereHas('user', function ($q) use ($searchTerm) {
                        $q->where('firstname', 'like', '%'.$searchTerm.'%');
                    });
                },
            ],

            [
                'label' => __('Email'),
                'type' => 'select',
                'name' => 'user',
                'attribute' => 'email',
                'model' => User::class,
                'orderable' => true,
                'orderLogic' => fn ($query, $column, $columnDirection) => $query->leftJoin('users', 'users.id', '=', 'teachers.id')
                    ->orderBy('users.email', $columnDirection)->select('teachers.*'),
                'searchLogic' => function ($query, $column, $searchTerm) {
                    $query->orWhereHas('user', function ($q) use ($searchTerm) {
                        $q->where('email', 'like', '%'.$searchTerm.'%');
                    });
                },
            ],
        ]);

        CRUD::addColumns([
            [
                'name' => 'max_week_hours',
                'label' => __('Weekly workable hours'),
                'type' => 'number',
            ],
            [
                'name' => 'hired_at',
                'label' => __('Hire Date'),
                'type' => 'date',
            ],
        ]);
    }

    public function setupCreateOperation()
    {
        CRUD::setValidation([
            'firstname' => 'required|max:255',
            'lastname' => 'required|max:255',
            'email' => 'required|email',
            'max_week_hours' => 'nullable|numeric',
            'hired_at' => 'nullable|date',
        ]);

        CRUD::field('firstname')->label(__('Firstname'));
        CRUD::field('lastname')->label(__('Lastname'));
        CRUD::field('email')->label(__('Email'));

        CRUD::addFields([
            [
                'name' => 'max_week_hours',
                'label' => __('Weekly workable hours'),
                'type' => 'number',
                'attributes' => ['step' => '0.01'],
            ],
            [
                'name' => 'hired_at',
                'label' => __('Hire Date'),
                'type' => 'date',
            ],
        ]);
    }

    protected function setupUpdateOperation()
    {
        $this->setupCreateOperation();
    }

    public function store()
    {
        $request = $this->crud->getRequest();

        if (User::where('email', $request->email)->count() === 0) {
            $username = $request->email;
        } else {
            $username = $this->generateUsername($request->firstname.' '.$request->lastname);
        }

        $user = User::create([
            'firstname' => $request->firstname,
            'lastname' => $request->lastname,
            'email' => $request->email,
            'username' => $username,
            'password' => Hash::make(Str::random(12)),
        ]);

        Teacher::create([
            'id' => $user->id,
            'hired_at' => $request->hired_at,
            'max_week_hours' => $request->max_week_hours,
        ]);

        return redirect()->route('teacher.index');
    }
}
