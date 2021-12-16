<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\TeacherRequest as StoreRequest;
// VALIDATION: change the requests to match your own file names if you need form validation
use App\Models\PhoneNumber;
use App\Models\Teacher;
use App\Models\User;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
use Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
use Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class TeacherCrudController extends CrudController
{
    use ListOperation;
    use CreateOperation { store as traitStore; }
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
        CRUD::addColumns([
            [
                'label' => __('ID number'),
                'type' => 'text',
                'name' => 'idnumber',
            ],
            [
                // 1-n relationship
                'label'     => __('Last Name'), // Table column heading
                'type'      => 'relationship',
                'key'  => 'lastname',
                'name'    => 'user', // the method that defines the relationship in your Model
                'attribute' => 'lastname', // foreign key attribute that is shown to user
                'model'     => 'App\Models\User', // foreign key model
                'orderable' => true,
                'orderLogic' => function ($query, $column, $columnDirection) {
                    return $query->leftJoin('users', 'users.id', '=', 'students.id')
                        ->orderBy('users.lastname', $columnDirection)->select('students.*');
                },
                'searchLogic' => function ($query, $column, $searchTerm) {
                    $query->orWhereHas('user', function ($q) use ($searchTerm) {
                        $q->where('lastname', 'like', '%'.$searchTerm.'%');
                    });
                },
            ],

            [
                // 1-n relationship
                'label'     => __('First Name'), // Table column heading
                'type'      => 'relationship',
                'key'  => 'firstname',
                'name'    => 'user', // the method that defines the relationship in your Model
                'attribute' => 'firstname', // foreign key attribute that is shown to user
                'model'     => 'App\Models\User', // foreign key model
                'orderable' => true,
                'orderLogic' => function ($query, $column, $columnDirection) {
                    return $query->leftJoin('users', 'users.id', '=', 'teachers.id')
                        ->orderBy('users.firstname', $columnDirection)->select('teachers.*');
                },
                'searchLogic' => function ($query, $column, $searchTerm) {
                    $query->orWhereHas('user', function ($q) use ($searchTerm) {
                        $q->where('firstname', 'like', '%'.$searchTerm.'%');
                    });
                },
            ],

            [
                // 1-n relationship
                'label'     => __('Email'), // Table column heading
                'type'      => 'relationship',
                'name'    => 'user', // the method that defines the relationship in your Model
                'attribute' => 'email', // foreign key attribute that is shown to user
                'model'     => 'App\Models\User', // foreign key model
                'orderable' => true,
                'orderLogic' => function ($query, $column, $columnDirection) {
                    return $query->leftJoin('users', 'users.id', '=', 'teachers.id')
                        ->orderBy('users.email', $columnDirection)->select('teachers.*');
                },
                'searchLogic' => function ($query, $column, $searchTerm) {
                    $query->orWhereHas('user', function ($q) use ($searchTerm) {
                        $q->where('email', 'like', '%'.$searchTerm.'%');
                    });
                },
            ],
        ]);

        CRUD::addColumns([
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

        CRUD::field('firstname')->label(__('Firstname'));
        CRUD::field('lastname')->label(__('Lastname'));
        CRUD::field('email')->label(__('Email'));

        CRUD::addFields([
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
        CRUD::setValidation(StoreRequest::class);

        CRUD::field('firstname')->label(__('Firstname'));
        CRUD::field('lastname')->label(__('Lastname'));
        CRUD::field('email')->label(__('Email'));

        CRUD::addFields([
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

    public function store(Request $request)
    {
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

        $teacher = Teacher::create([
            'id' => $user->id,
            'hired_at' => $request->hired_at,
            'max_week_hours' => $request->max_week_hours,
        ]);

        return redirect()->route('teacher.index');
    }

    protected function generateUsername($fullName) : string
    {
        $username_parts = array_filter(explode(' ', strtolower($fullName)));
        $username_parts = array_slice($username_parts, -2);

        $part1 = (! empty($username_parts[0])) ? substr($username_parts[0], 0, 3) : '';
        $part2 = (! empty($username_parts[1])) ? substr($username_parts[1], 0, 8) : '';
        $part3 = rand(999, 9999);

        $username = $part1.$part2.$part3; //str_shuffle to randomly shuffle all characters

        return $username;
    }
}
