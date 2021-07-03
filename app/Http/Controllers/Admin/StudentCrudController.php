<?php

namespace App\Http\Controllers\Admin;

use App\Events\UserCreated;
use App\Exceptions\UserSyncException;
use App\Http\Requests\StudentRequest;
use App\Models\Institution;
use App\Models\LeadType;
use App\Models\Period;
use App\Models\PhoneNumber;
use App\Models\Profession;
use App\Models\Student;
use App\Models\User;
use App\Traits\PeriodSelection;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
use Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
use Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation;
use Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;
use Backpack\CRUD\app\Library\Widget;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use mysql_xdevapi\Exception;

class StudentCrudController extends CrudController
{
    use ListOperation;
    use ShowOperation {
        show as traitShow;
    }
    use UpdateOperation;
    use CreateOperation { store as traitStore; }
    use PeriodSelection;
    use \Backpack\CRUD\app\Http\Controllers\Operations\FetchOperation;

    public function __construct()
    {
        parent::__construct();
        $this->middleware('permission:enrollments.view', ['except' => ['dataAjax', 'show']]);
        $this->middleware('permission:student.edit', ['except' => ['index', 'show', 'search', 'dataAjax']]);
    }

    public function setup()
    {
        CRUD::setModel(Student::class);
        CRUD::setRoute(config('backpack.base.route_prefix').'/student');
        CRUD::setEntityNameStrings(__('student'), __('students'));

        $permissions = backpack_user()->getAllPermissions();

        if ($permissions->contains('enrollments.view')) {
            CRUD::enableExportButtons();
        }
    }

    public function setupListOperation()
    {

        // display lead status counts on page top
        foreach (LeadType::all() as $leadType) {
            $count = Student::computedLeadType($leadType->id)->count();
            if ($count > 0) {
                Widget::add([
                    'type' => 'view',
                    'view' => 'students.lead-type-insights-widget',
                    'studentCount' => $count,
                    'name' => Str::plural($leadType->name),
                    'icon' => $leadType->icon,
                    'leadTypeId' => $leadType->id,
                    'description' => $leadType->description,
                ])->to('before_content');
            }
        }

        // Columns.
        CRUD::setColumns([
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
                    return $query->leftJoin('users', 'users.id', '=', 'students.id')
                        ->orderBy('users.firstname', $columnDirection)->select('students.*');
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
                    return $query->leftJoin('users', 'users.id', '=', 'students.id')
                        ->orderBy('users.email', $columnDirection)->select('students.*');
                },
                'searchLogic' => function ($query, $column, $searchTerm) {
                    $query->orWhereHas('user', function ($q) use ($searchTerm) {
                        $q->where('email', 'like', '%'.$searchTerm.'%');
                    });
                },
            ],

            [
                // n-n relationship (with pivot table)
                'label' => __('Phone number'), // Table column heading
                'type' => 'select_multiple',
                'name' => 'phone', // the method that defines the relationship in your Model
                'attribute' => 'phone_number', // foreign key attribute that is shown to user
                'model' => PhoneNumber::class, // foreign key model
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
            foreach (json_decode($values) as $value) {
                $this->crud->query = $this->crud->query->whereDoesntHave('enrollments', function ($query) use ($value) {
                    return $query->whereHas('course', function ($q) use ($value) {
                        $q->where('period_id', $value);
                    });
                });
            }
        });

        // select2 filter
        $this->crud->addFilter([
            'name'  => 'institution_id',
            'type'  => 'select2',
            'label' => __('Institution'),
        ], function () {
            return Institution::all()->pluck('name', 'id')->toArray();
        }, function ($value) { // if the filter is active
            $this->crud->addClause('where', 'institution_id', $value);
        });

        $this->crud->addFilter([
            'name'  => 'status_type_id',
            'type'  => 'select2',
            'label' => __('Lead Status'),
        ], function () {
            return LeadType::all()->pluck('name', 'id')->toArray();
        }, function ($value) {
            $this->crud->addClause('computedLeadType', $value);
        });
    }

    public function setupCreateOperation()
    {
        CRUD::setValidation(StudentRequest::class);
        CRUD::field('firstname')->label(__('Firstname'))->tab(__('Student Info'));
        CRUD::field('lastname')->label(__('Lastname'))->tab(__('Student Info'));
        CRUD::field('email')->label(__('Email'))->tab(__('Student Info'));
        CRUD::field('idnumber')->label(__('ID number'))->tab(__('Student Info'));
        CRUD::field('birthdate')->label(__('Birthdate'))->tab(__('Student Info'));

        CRUD::addField([
            'type' => "text",
            'name' => 'phone', // the method on your model that defines the relationship
            'tab' => __('Student Info'),
            'label' => __('Phone'),
        ]);

        CRUD::addField([
            'type' => "relationship",
            'name' => 'profession', // the method on your model that defines the relationship
            'inline_create' => true, // assumes the URL will be "/admin/category/inline/create"
            'tab' => __('Student Info'),
            'label' => __('Profession'),
            'attribute' => 'name',
        ]);

        CRUD::addField([
            'type' => "relationship",
            'name' => 'institution', // the method on your model that defines the relationship
            'inline_create' => true, // assumes the URL will be "/admin/category/inline/create"
            'tab' => __('Student Info'),
            'label' => __('Institution'),
            'attribute' => 'name',
        ]);

        CRUD::field('address')->label(__('Address'))->tab(__('Address'));
        CRUD::field('zip_code')->label(__('zip'))->tab(__('Address'));
        CRUD::field('city')->label(__('City'))->tab(__('Address'));
        CRUD::field('state')->label(__('State'))->tab(__('Address'));
        CRUD::field('country')->label(__('Country'))->tab(__('Address'));

        CRUD::field('iban')->label('IBAN')->tab(__('Invoicing Info'));
        CRUD::field('bic')->label('BIC')->tab(__('Invoicing Info'));
    }

    public function setupUpdateOperation()
    {
        CRUD::setValidation(StudentRequest::class);
        CRUD::field('firstname')->label(__('Firstname'))->tab(__('Student Info'));
        CRUD::field('lastname')->label(__('Lastname'))->tab(__('Student Info'));
        CRUD::field('email')->label(__('Email'))->tab(__('Student Info'));
        CRUD::field('idnumber')->label(__('ID number'))->tab(__('Student Info'));
        CRUD::field('birthdate')->label(__('Birthdate'))->tab(__('Student Info'));

        CRUD::addField([
            'type' => "relationship",
            'name' => 'profession', // the method on your model that defines the relationship
            'inline_create' => true, // assumes the URL will be "/admin/category/inline/create"
            'tab' => __('Student Info'),
            'label' => __('Profession'),
            'attribute' => 'name',
        ]);

        CRUD::addField([
            'type' => "relationship",
            'name' => 'institution', // the method on your model that defines the relationship
            'inline_create' => true, // assumes the URL will be "/admin/category/inline/create"
            'tab' => __('Student Info'),
            'label' => __('Institution'),
            'attribute' => 'name',
        ]);

        CRUD::field('address')->label(__('Address'))->tab(__('Address'));
        CRUD::field('zip_code')->label(__('zip'))->tab(__('Address'));
        CRUD::field('city')->label(__('City'))->tab(__('Address'));
        CRUD::field('state')->label(__('State'))->tab(__('Address'));
        CRUD::field('country')->label(__('Country'))->tab(__('Address'));

        CRUD::field('iban')->label('IBAN')->tab(__('Invoicing Info'));
        CRUD::field('bic')->label('BIC')->tab(__('Invoicing Info'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'firstname'                            => 'required|max:255',
            'lastname'                             => 'required|max:255',
            'email'                                => 'required|unique:users',
        ]);

        // update the user info
        $user = User::create([
            'firstname' => $request->firstname,
            'lastname' => $request->lastname,
            'email' => $request->email,
            'password' => Hash::make(Str::random(12)),
        ]);

        try {
            UserCreated::dispatch($user);
        } catch (\Exception) {
            throw new UserSyncException();
        }

        // update the student info

        $student = Student::create([
            'id' => $user->id,
            'idnumber' => $request->idnumber,
            'address' => $request->address,
            'city' => $request->city,
            'state' => $request->state,
            'country' => $request->country,
            'birthdate' => $request->birthdate,
        ]);

        // save phone number
        if ($request->phone) {
            $phone_number = PhoneNumber::firstOrCreate([
                'phone_number' => $request->phone,
                'phoneable_id' => $student->id,
                'phoneable_type' => Student::class,
            ]);

            $student->phone()->save($phone_number);
        }

        // save profession and institution
        if ($request->profession) {
            $profession = Profession::firstOrCreate([
                'name' => $request->profession,
            ]);

            $student->update([
                'profession_id' => $profession->id,
            ]);
        }

        if ($request->institution) {
            $institution = Institution::firstOrCreate([
                'name' => $request->institution,
            ]);

            $student->update([
                'institution_id' => $institution->id,
            ]);
        }

        return redirect()->route('student.index');
    }

    public function show($student)
    {
        $student = Student::findOrFail($student);

        if (! backpack_user()->isTeacher() && ! backpack_user()->can('enrollments.view')) {
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

    protected function fetchInstitution()
    {
        return $this->fetch(Institution::class);
    }

    protected function fetchProfession()
    {
        return $this->fetch(Profession::class);
    }
}
