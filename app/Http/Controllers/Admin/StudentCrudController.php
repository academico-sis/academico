<?php

namespace App\Http\Controllers\Admin;

use App\Events\UserCreated;
use App\Exceptions\UserSyncException;
use App\Models\Institution;
use App\Models\Period;
use App\Models\PhoneNumber;
use App\Models\Profession;
use App\Models\Student;
use App\Models\User;
use App\Traits\PeriodSelection;
use App\Traits\UsernameTrait;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
use Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;
use Backpack\CRUD\app\Http\Controllers\Operations\FetchOperation;
use Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
use Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation;
use Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class StudentCrudController extends CrudController
{
    use ListOperation;
    use ShowOperation { show as traitShow; }
    use UpdateOperation { update as traitUpdate; }
    use CreateOperation { store as traitStore; }
    use PeriodSelection;
    use DeleteOperation { destroy as traitDelete; }
    use FetchOperation;
    use UsernameTrait;

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
        CRUD::enableExportButtons();

        $permissions = backpack_user()->getAllPermissions();
        if ($permissions->contains('name', 'enrollments.edit')) {
            CRUD::addButtonFromView('line', 'selectCourse', 'selectCourse', 'beginning');
        }
    }

    public function setupListOperation()
    {
        CRUD::setColumns([
            [
                'label' => __('ID number'),
                'type' => 'text',
                'name' => 'idnumber',
                'visibleInExport' => false,
                'key' => 'idnumber_display',
            ],

            [
                'label' => __('ID number'),
                'type' => 'text',
                'name' => 'idnumber',
                'exportOnlyField' => true,
                'limit' => 200,
                'key' => 'idnumber_export',
            ],

            [
                'visibleInExport' => false,
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
                'exportOnlyField' => true,
                'limit' => 200,
                'label' => __('Last Name'),
                'type' => 'select',
                'key' => 'lastname_export',
                'name' => 'user',
                'attribute' => 'lastname',
                'model' => User::class,
            ],

            [
                'visibleInExport' => false,
                'label' => __('First Name'),
                'type' => 'select',
                'key' => 'firstname',
                'name' => 'user',
                'attribute' => 'firstname',
                'model' => User::class,
                'orderable' => true,
                'orderLogic' => fn ($query, $column, $columnDirection) => $query->leftJoin('users', 'users.id', '=', 'students.id')
                    ->orderBy('users.firstname', $columnDirection)->select('students.*'),
                'searchLogic' => function ($query, $column, $searchTerm) {
                    $query->orWhereHas('user', function ($q) use ($searchTerm) {
                        $q->where('firstname', 'like', '%'.$searchTerm.'%');
                    });
                },
            ],

            [
                'exportOnlyField' => true,
                'limit' => 200,
                'label' => __('First Name'),
                'type' => 'select',
                'key' => 'firstname_export',
                'name' => 'user',
                'attribute' => 'firstname',
                'model' => User::class,
            ],

            [
                'visibleInExport' => false,
                'label' => __('Email'),
                'type' => 'select',
                'name' => 'user',
                'attribute' => 'email',
                'key' => 'email_display',
                'model' => User::class,
                'orderable' => true,
                'orderLogic' => fn ($query, $column, $columnDirection) => $query->leftJoin('users', 'users.id', '=', 'students.id')
                    ->orderBy('users.email', $columnDirection)->select('students.*'),
                'searchLogic' => function ($query, $column, $searchTerm) {
                    $query->orWhereHas('user', function ($q) use ($searchTerm) {
                        $q->where('email', 'like', '%'.$searchTerm.'%');
                    });
                },
            ],

            [
                'exportOnlyField' => true,
                'limit' => 200,
                'label' => __('Email'),
                'type' => 'select',
                'name' => 'user',
                'attribute' => 'email',
                'key' => 'email_export',
                'model' => User::class,
            ],

            [
                'visibleInExport' => false,
                'label' => __('Username'),
                'type' => 'select',
                'key' => 'username',
                'name' => 'user',
                'attribute' => 'username',
                'model' => User::class,
                'orderable' => false,
                'searchLogic' => false,
            ],

            [
                'label' => __('Age'),
                'name' => 'student_age',
            ],

            [
                'label' => __('Birthdate'),
                'name' => 'student_birthdate',
            ],

            [
                'label' => __('Phone number'),
                'type' => 'select_multiple',
                'name' => 'phone',
                'attribute' => 'phone_number',
                'model' => PhoneNumber::class,
            ],

        ]);

        CRUD::addFilter(
            [
                'name' => 'enrolled',
                'type' => 'select2',
                'label' => __('Is Enrolled in'),
            ],
            fn () => Period::all()->pluck('name', 'id')->toArray(),
            function ($value) {
                $this->crud->query = $this->crud->query->whereHas('enrollments', fn ($query) => $query->whereHas('course', function ($q) use ($value) {
                    $q->where('period_id', $value);
                }));
            },
            function () { // if the filter is NOT active (the GET parameter "checkbox" does not exit)
            }
        );

        CRUD::addFilter([
            'name' => 'notenrolled',
            'type' => 'select2_multiple',
            'label' => __('Is Not Enrolled in'),
        ], fn () => Period::all()->pluck('name', 'id')->toArray(), function ($values) {
            foreach (json_decode($values, null, 512, JSON_THROW_ON_ERROR) as $value) {
                $this->crud->query = $this->crud->query->whereDoesntHave('enrollments', fn ($query) => $query->whereHas('course', function ($q) use ($value) {
                    $q->where('period_id', $value);
                }));
            }
        });

        CRUD::addFilter(
            [
                'name' => 'new_students',
                'type' => 'select2',
                'label' => __('New In'),
            ],
            fn () => Period::all()->pluck('name', 'id')->toArray(),
            function ($value) {
                CRUD::addClause('newInPeriod', $value);
            }
        );

        CRUD::addFilter([
            'name' => 'institution_id',
            'type' => 'select2',
            'label' => __('Institution'),
        ], fn () => Institution::all()->pluck('name', 'id')->toArray(), function ($value) {
            $this->crud->addClause('where', 'institution_id', $value);
        });

        CRUD::addFilter([
            'name' => 'age',
            'type'       => 'range',
            'label'      => __('Age'),
            'label_from' => 'min',
            'label_to'   => 'max',
        ],
            false,
            function($value) {
                $range = json_decode($value);
                if ($range->from && (int) $range->from > 0) {
                    $minDate = Carbon::now()->subYears($range->from);
                    $this->crud->addClause('where', 'birthdate', '<=', $minDate);
                }
                if ($range->to) {
                    $maxDate = Carbon::now()->subYears($range->to);
                    $this->crud->addClause('where', 'birthdate', '>=', $maxDate);
                }
            });
    }

    public function setupCreateOperation()
    {
        CRUD::addField([
            'name' => 'firstname',
            'label' => __('Firstname'),
            'tab' => __('Student Info'),
            'validationRules' => 'required|string|max:30',
        ]);

        CRUD::addField([
            'name' => 'lastname',
            'label' => __('Lastname'),
            'tab' => __('Student Info'),
            'validationRules' => 'required|string|max:30',
        ]);

        CRUD::addField([
            'name' => 'email',
            'label' => __('Email'),
            'tab' => __('Student Info'),
            'validationRules' => 'nullable|email|max:60',
        ]);

        CRUD::field('idnumber')->label(__('ID number'))->tab(__('Student Info'))->validationRules('nullable|string');
        CRUD::field('birthdate')->label(__('Birthdate'))->tab(__('Student Info'))->validationRules('nullable|date');

        $this->crud->addField([
            'name' => 'gender_id',
            'label' => __('Gender'),
            'type' => 'radio',
            'options' => [
                0 => __('Other / Rather not say'),
                1 => __('Female'),
                2 => __('Male'),
            ],
            'inline' => true,
            'tab' => __('Student Info'),
            'validationRules' => 'required|integer',
        ]);

        if (config('backpack.base.license_code')) {
            CRUD::addField([
                'type' => 'relationship',
                'force_delete' => true,
                'name' => 'phone',
                'tab' => __('Student Info'),
                'label' => __('Phone'),
                'subfields' => [
                    [
                        'name' => 'phone_number',
                        'type' => 'text',
                        'wrapper' => [
                            'class' => 'form-group col-md-3',
                        ],
                    ],
                ],
            ]);
        }

        $this->crud->addField([
            'label' => __('Profile Picture'),
            'name' => 'image',
            'type' => 'image',
            'crop' => true,
            'tab' => __('Student Info'),
        ]);

        if (config('backpack.base.license_code')) {
            CRUD::addField(['type' => 'relationship', 'name' => 'profession', 'inline_create' => true, 'tab' => __('Student Info'), 'label' => __('Profession'), 'attribute' => 'name']);
        } else {
            CRUD::addField(['type' => 'select', 'name' => 'profession', 'tab' => __('Student Info'), 'label' => __('Profession'), 'attribute' => 'name']);
        }

        if (config('backpack.base.license_code')) {
            CRUD::addField(['type' => 'relationship', 'name' => 'institution', 'inline_create' => true, 'tab' => __('Student Info'), 'label' => __('Institution'), 'attribute' => 'name']);
        } else {
            CRUD::addField(['type' => 'select', 'name' => 'institution', 'tab' => __('Student Info'), 'label' => __('Institution'), 'attribute' => 'name']);
        }

        CRUD::field('address')->label(__('Address'))->tab(__('Address'))->validationRules('nullable|string|max:60');
        CRUD::field('zip_code')->label(__('zip'))->tab(__('Address'))->validationRules('nullable|string|max:10');
        CRUD::field('city')->label(__('City'))->tab(__('Address'))->validationRules('nullable|string|max:30');
        CRUD::field('state')->label(__('State'))->tab(__('Address'))->validationRules('nullable|string|max:30');
        CRUD::field('country')->label(__('Country'))->tab(__('Address'))->validationRules('nullable|string|max:20');

        CRUD::field('iban')->label('IBAN')->tab(__('Invoicing Info'))->validationRules('nullable|string|max:90');
        CRUD::field('bic')->label('BIC')->tab(__('Invoicing Info'))->validationRules('nullable|string|max:30');

        CRUD::setValidation();
    }

    public function setupUpdateOperation()
    {
        $this->setupCreateOperation();
    }

    public function store()
    {
        $request = $this->crud->getRequest();

        if ($request->email && User::where('email', $request->email)->count() === 0) {
            $username = $request->email;
        } else {
            $username = $this->generateUsername($request->firstname.' '.$request->lastname);
        }

        // update the user info
        $user = User::create([
            'firstname' => $request->firstname,
            'lastname' => $request->lastname,
            'email' => $request->email ?? null,
            'username' => $username,
            'password' => Hash::make(Str::random(12)),
        ]);

        try {
            UserCreated::dispatch($user);
        } catch (Exception) {
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
            'gender_id' => $request->gender_id,
        ]);

        // save phone number
        if ($request->phone) {
            foreach ($request->phone as $phone_number) {
                $phone_number = PhoneNumber::firstOrCreate(['phone_number' => $phone_number['phone_number'], 'phoneable_id' => $student->id, 'phoneable_type' => Student::class]);

                $student->phone()->save($phone_number);
            }
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

    public function update()
    {
        $this->crud->hasAccessOrFail('update');
        $request = $this->crud->validateRequest();
        $this->crud->registerFieldEvents();

        $this->crud->getCurrentEntry()->user()->update([
            'firstname' => $request->firstname,
            'lastname' => $request->lastname,
            'email' => $request->email,
        ]);

        $this->crud->getRequest()->request->remove('firstname');
        $this->crud->getRequest()->request->remove('lastname');
        $this->crud->getRequest()->request->remove('email');

        $item = $this->crud->update(
            $request->get($this->crud->model->getKeyName()),
            $this->crud->getStrippedSaveRequest($request)
        );
        $this->data['entry'] = $this->crud->entry = $item;

        \Alert::success(trans('backpack::crud.update_success'))->flash();

        $this->crud->setSaveAction();

        return $this->crud->performSaveAction($item->getKey());
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
            'attendances' => $student->periodAttendance()->get(),
            'writeaccess' => backpack_user()->can('enrollments.edit') ?? 0,
        ]);
    }

    public function destroy($id)
    {
        $this->crud->hasAccessOrFail('delete');

        // get entry ID from Request (makes sure its the last ID for nested resources)
        $id = $this->crud->getCurrentEntryId() ?? $id;

        $this->crud->delete($id);

        return User::where('id', $id)->forceDelete();
    }

    protected function fetchInstitution()
    {
        return $this->fetch([
            'model' => Institution::class,
            'searchable_attributes' => ['name'],
        ]);
    }

    protected function fetchProfession()
    {
        return $this->fetch([
            'model' => Profession::class,
            'searchable_attributes' => ['name'],
        ]);
    }
}
