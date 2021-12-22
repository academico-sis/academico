<?php

namespace App\Http\Controllers\Admin;

use App\Models\Course;
use App\Models\Enrollment;
use App\Models\EnrollmentStatusType;
use App\Models\Invoice;
use App\Models\Paymentmethod;
use App\Models\Period;
use App\Models\PhoneNumber;
use App\Models\ScheduledPayment;
use App\Models\Scholarship;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;
use Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
use Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation;
use Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;
use Backpack\CRUD\app\Library\Widget;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;

/**
 * Class EnrollmentCrudController
 * This controller is used to view enrollments only.
 * No enrollments may be created or updated from here.
 */
class EnrollmentCrudController extends CrudController
{
    use ListOperation;
    use ShowOperation { show as traitShow; }
    use UpdateOperation { update as traitUpdate; }
    use DeleteOperation;

    protected string $mode = 'global';

    protected ?Course $course = null;

    public function __construct()
    {
        parent::__construct();
        $this->middleware(['permission:enrollments.view']);
        $this->middleware('permission:enrollments.delete', ['only' => ['destroy']]);
    }

    public function setup()
    {
        CRUD::setModel(Enrollment::class);
        CRUD::setRoute(config('backpack.base.route_prefix').'/enrollment');
        CRUD::setEntityNameStrings(__('enrollment'), __('enrollments'));

        if ($this->crud->getRequest()->has('course_id')) {
            $this->mode = 'course';
            $this->course = Course::findOrFail($this->crud->getRequest()->course_id);

            if (Gate::forUser(backpack_user())->denies('view-course', $this->course)) {
                abort(403);
            }

            CRUD::addClause('course', $this->course->id);
        }

        if ($this->mode === 'course') {
            CRUD::denyAccess(['create', 'update', 'delete']);
        }

        if (backpack_user()->hasRole('admin')) {
            CRUD::enableExportButtons();
        }

        if ($this->mode === 'global' && $this->crud->getOperation() === 'list') {
            $pendingBalance = Enrollment::pending()->sum('balance');

            Widget::add()->type('view')->view('enrollments.total_balance_widget')->value($pendingBalance)->to('before_content');
//            Widget::add()->type('view')->view('enrollments.total_income_widget')->value($periodIncome)->to('before_content');
        }

        if ($this->mode === 'course') {
            Widget::add(['type' => 'view', 'view' => 'partials.course_info', 'course' => $this->course])->to('before_content');

            CRUD::denyAccess(['show']);
            CRUD::addButtonFromView('line', 'showStudent', 'showStudentForEnrollment');

            CRUD::addButtonFromView('top', 'enroll-student-in-course', 'enroll-student-in-course', 'end');
            CRUD::addButtonFromView('top', 'switch-to-photo-roster', 'switch-to-photo-roster', 'end');
        }
    }

    /*
    |--------------------------------------------------------------------------
    | CrudPanel Configuration
    |--------------------------------------------------------------------------
    */

    public function setupListOperation()
    {
        if (config('app.currency_position') === 'before') {
            $currency = ['prefix' => config('app.currency_symbol')];
        } else {
            $currency = ['suffix' => config('app.currency_symbol')];
        }

        CRUD::addColumns([

            [
                'name' => 'id',
                'label' => 'ID',
            ],

            [
                'label' => __('ID number'),
                'type' => 'text',
                'name' => 'student.idnumber',
            ],

            [
                'name' => 'user',
                'key'       => 'user_lastname',
                'attribute' => 'lastname',
                'label' => __('Last Name'),
                'type' => 'relationship',
                'searchLogic' => function ($query, $column, $searchTerm) {
                    $query->orWhereHas('student', function ($q) use ($searchTerm) {
                        $q->whereHas('user', function ($q) use ($searchTerm) {
                            $q->where('lastname', 'like', '%'.$searchTerm.'%');
                        });
                    });
                },
            ],

            [
                'name' => 'user',
                'key'       => 'user_firstname',
                'attribute' => 'firstname',
                'label' => __('First Name'),
                'type' => 'relationship',
                'searchLogic' => function ($query, $column, $searchTerm) {
                    $query->orWhereHas('student', function ($q) use ($searchTerm) {
                        $q->whereHas('user', function ($q) use ($searchTerm) {
                            $q->where('firstname', 'like', '%'.$searchTerm.'%');
                        });
                    });
                },
            ],

            [
                'label' => __('Age'),
                'name' => 'student_age',
            ],

            [
                'label' => __('Birthdate'),
                'name' => 'student_birthdate',
            ],
        ]);

        if ($this->mode === 'global') {
            CRUD::addColumns([[// COURSE NAME
                'label' => __('Course'), // Table column heading
                'type' => 'select', 'name' => 'course_id', // the column that contains the ID of that connected entity;
                'entity' => 'course', // the method that defines the relationship in your Model
                'attribute' => 'name', // foreign key attribute that is shown to user
                'model' => Course::class, // foreign key model
            ],

                ['type' => 'relationship', 'name' => 'course.period', 'label' => __('Period'), 'attribute' => 'name'], ]);
        }

        CRUD::addColumns([
            [
                // STATUS
                'label' => __('Status'), // Table column heading
                'type' => 'select',
                'name' => 'status_id', // the column that contains the ID of that connected entity;
                'entity' => 'enrollmentStatus', // the method that defines the relationship in your Model
                'attribute' => 'name', // foreign key attribute that is shown to user
                'model' => EnrollmentStatusType::class, // foreign key model
            ],

            array_merge([
                'name' => 'balance',
                'label' => __('Balance'),
                'type' => 'number',
            ],
            $currency),
        ]);

        if (config('invoicing.allow_scheduled_payments')) {
            CRUD::addColumn([
                'name' => 'scheduledPayments', // name of relationship method in the model
                'type' => 'relationship', 'label' => __('Scheduled Payments'), // OPTIONAL
                'attribute' => 'date', 'model' => ScheduledPayment::class, // foreign key model
            ]);
        }

        CRUD::addColumns([

            [
                // any type of relationship
                'name'         => 'scholarships', // name of relationship method in the model
                'type'         => 'relationship',
                'label'        => __('Scholarship'),
                // OPTIONAL
                // 'entity'    => 'tags', // the method that defines the relationship in your Model
                'attribute' => 'name', // foreign key attribute that is shown to user
                'model'     => Scholarship::class, // foreign key model
            ],

            [
                'label'     => __('Email'), // Table column heading
                'name' => 'user',
                'attribute' => 'email',
                'type' => 'relationship',
            ],

            [
                'label'     => __('Phone number'), // Table column heading
                'type'      => 'select_multiple',
                'name'      => 'student.phone', // the method that defines the relationship in your Model
                'attribute' => 'phone_number', // foreign key attribute that is shown to user
                'model' => PhoneNumber::class, // foreign key model
            ],

        ]);

        if ($this->mode === 'global') {
            CRUD::addFilter([
                'name' => 'status_id',
                'type' => 'select2_multiple',
                'label'=> __('Status'),
            ], fn () => EnrollmentStatusType::all()->pluck('name', 'id')->toArray(),
            function ($values) {
                // if the filter is active
                foreach (json_decode($values, null, 512, JSON_THROW_ON_ERROR) as $value) {
                    CRUD::addClause('orWhere', 'status_id', $value);
                }
            });

            CRUD::addFilter([
                'name' => 'period_id',
                'type' => 'select2',
                'label'=> __('Period'),
            ], fn () => Period::all()->pluck('name', 'id')->toArray(), function ($value) {
                // if the filter is active
                CRUD::addClause('period', $value);
            });

            CRUD::addFilter(
                [
                    'name' => 'scholarship',
                    'type' => 'select2',
                    'label'=> __('Scholarship'),
                ],
                fn () => Scholarship::all()->pluck('name', 'id')->toArray(),
                function ($value) { // if the filter is active
                    if ($value == 'all') {
                        CRUD::addClause('whereHas', 'scholarships');
                    } else {
                        CRUD::addClause('whereHas', 'scholarships', function ($q) use ($value) {
                            $q->where('scholarships.id', $value);
                        });
                    }
                }
            );
        }
    }

    public function show($enrollment)
    {
        // if mode is global, display enrollment
        $enrollment = Enrollment::findOrFail($enrollment);

        // load the products from the invoice tables
        $products = $enrollment->invoices()
            ->with('invoiceDetails')
            ->get();

        // get related comments
        $comments = $enrollment->invoices->map(fn (Invoice $invoice) => $invoice->comments)->flatten()->concat($enrollment->comments);

        $scholarships = Scholarship::all();

        $availablePaymentMethods = Paymentmethod::all();

        //$enrollment->load('invoices')->load('invoices.payments');

        $writeaccess = $enrollment->status_id !== 2 && backpack_user()->can('enrollments.edit');

        // then load the page
        return view('enrollments.show', compact('enrollment', 'products', 'comments', 'scholarships', 'availablePaymentMethods', 'writeaccess'));
    }

    protected function setupUpdateOperation()
    {
        if (config('app.currency_position') === 'before') {
            $currency = ['prefix' => config('app.currency_symbol')];
        } else {
            $currency = ['suffix' => config('app.currency_symbol')];
        }

        CRUD::addField([
            'label'     => __('Course'),
            'type'      => 'select2',
            'name'      => 'course_id', // the db column for the foreign key

            'entity'    => 'course', // the method that defines the relationship in your Model
            'model'     => \App\Models\Course::class, // foreign key model
            'attribute' => 'name', // foreign key attribute that is shown to user

            'options'   => (fn ($query) => $query->orderBy('level_id', 'ASC')->where('period_id', $this->crud->getCurrentEntry()->course->period_id)->get()),
        ]);

        CRUD::addField(array_merge([
            'name' => 'price', // The db column name
            'label' => __('Price'), // Table column heading
            'type' => 'number',
        ], $currency));

        if (config('invoicing.allow_scheduled_payments')) {
            CRUD::addField(['name' => 'scheduledPayments', 'label' => __('Scheduled Payments'), 'type' => 'repeatable', 'fields' => [['name' => 'date', 'type' => 'date', 'label' => __('Date'), 'wrapper' => ['class' => 'form-group col-md-4']], array_merge(['name' => 'value', 'type' => 'number', 'attributes' => ['step' => 0.01, 'min' => 0], 'label' => __('Value'), 'wrapper' => ['class' => 'form-group col-md-4']], $currency), ['name' => 'status', 'type' => 'radio', 'label' => __('Status'), 'wrapper' => ['class' => 'form-group col-md-4'], 'options' => [1 => __('Pending'), 2 => __('Paid')], 'inline' => true]]]);
        }

        CRUD::addField([
            'label'     => __('Status'),
            'type'      => 'select',
            'name'      => 'status_id', // the db column for the foreign key

            // optional
            // 'entity' should point to the method that defines the relationship in your Model
            // defining entity will make Backpack guess 'model' and 'attribute'
            'entity'    => 'enrollmentStatus',

            // optional - manually specify the related model and attribute
            'model'     => \App\Models\EnrollmentStatusType::class, // related model
            'attribute' => 'name', // foreign key attribute that is shown to user
        ]);
    }

    public function update()
    {
        $enrollment = $this->crud->getCurrentEntry();
        $newScheduledPayments = collect(json_decode($this->crud->getRequest()->input('scheduledPayments'), null, 512, JSON_THROW_ON_ERROR));
        $enrollment->saveScheduledPayments($newScheduledPayments);
        $response = $this->traitUpdate();

        return $response;
    }

    public function destroy($enrollment)
    {
        $enrollment = Enrollment::findOrFail($enrollment);
        $enrollment->cancel();

        Log::notice('Enrollment canceled by user '.backpack_user()->id);
    }
}
