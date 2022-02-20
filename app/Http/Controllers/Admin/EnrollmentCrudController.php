<?php

namespace App\Http\Controllers\Admin;

use App\Models\Course;
use App\Models\Enrollment;
use App\Models\EnrollmentStatusType;
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
    use UpdateOperation;
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
    }

    /*
    |--------------------------------------------------------------------------
    | CrudPanel Configuration
    |--------------------------------------------------------------------------
    */

    public function setupListOperation()
    {
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

        CRUD::enableExportButtons();

        if ($this->mode === 'course') {
            Widget::add(['type' => 'view',
                'view' => 'partials.course_info',
                'course' => $this->course, ])->to('before_content');

            CRUD::denyAccess(['show']);
            CRUD::addButtonFromView('line', 'showStudent', 'showStudentForEnrollment');

            CRUD::addButtonFromView('top', 'enroll-student-in-course', 'enroll-student-in-course', 'end');
            CRUD::addButtonFromView('top', 'switch-to-photo-roster', 'switch-to-photo-roster', 'end');
        }

        if (config('academico.currency_position') === 'before') {
            $currency = ['prefix' => config('academico.currency_symbol')];
        } else {
            $currency = ['suffix' => config('academico.currency_symbol')];
        }

        CRUD::addColumns([['name' => 'id',
            'label' => 'ID',
            'wrapper' => ['element' => function ($crud, $column, $entry) {
                return $entry->status_id > 2 ? 'del' : 'span';
            }], ],

            ['label' => __('ID number'),
                'type' => 'text',
                'name' => 'student.idnumber',
                'wrapper' => ['element' => function ($crud, $column, $entry) {
                    return $entry->status_id > 2 ? 'del' : 'span';
                }], ],

            ['name' => 'user',
                'key' => 'user_lastname',
                'attribute' => 'lastname',
                'label' => __('Last Name'),
                'type' => 'select',
                'wrapper' => ['element' => function ($crud, $column, $entry) {
                    return $entry->status_id > 2 ? 'del' : 'span';
                }],
                'orderable' => true,
                'orderLogic' => function ($query, $column, $columnDirection) {
                    return $query->leftJoin('users', 'enrollments.student_id', '=', 'users.id')
                        ->orderBy('users.lastname', $columnDirection)->select('enrollments.*');
                },
                'searchLogic' => function ($query, $column, $searchTerm) {
                    $query->orWhereHas('student', function ($q) use ($searchTerm) {
                        $q->whereHas('user', function ($q) use ($searchTerm) {
                            $q->where('lastname', 'like', '%'.$searchTerm.'%');
                        });
                    });
                },
            ],

            ['name' => 'user',
                'key' => 'user_firstname',
                'attribute' => 'firstname',
                'label' => __('First Name'),
                'type' => 'select',
                'wrapper' => ['element' => function ($crud, $column, $entry) {
                    return $entry->status_id > 2 ? 'del' : 'span';
                }],
                'searchLogic' => function ($query, $column, $searchTerm) {
                    $query->orWhereHas('student', function ($q) use ($searchTerm) {
                        $q->whereHas('user', function ($q) use ($searchTerm) {
                            $q->where('firstname', 'like', '%'.$searchTerm.'%');
                        });
                    });
                },
                'orderLogic' => function ($query, $column, $columnDirection) {
                    return $query->leftJoin('users', 'enrollments.student_id', '=', 'users.id')
                        ->orderBy('users.firstname', $columnDirection)->select('enrollments.*');
                },
                'orderable' => true,
            ],

            ['label' => __('Age'),
                'name' => 'student_age', ],

            ['label' => __('Birthdate'),
                'name' => 'student_birthdate', ], ]);

        if ($this->mode === 'global') {
            CRUD::addColumns([
                [
                    'label' => __('Course'),
                    'type' => 'select',
                    'name' => 'course_id',
                    'entity' => 'course',
                    'attribute' => 'name',
                    'model' => Course::class,
                    ],
                ['type' => 'select',
                    'name' => 'course.period',
                    'label' => __('Period'),
                    'attribute' => 'name',
                    'orderLogic' => function ($query, $column, $columnDirection) {
                        return $query->leftJoin('courses', 'enrollments.course_id', '=', 'courses.id')
                            ->orderBy('courses.period_id', $columnDirection)->select('enrollments.*');
                    },
                    'orderable' => true,
                ], ]);
        }

        CRUD::addColumns([['label' => __('Status'),
            'type' => 'select',
            'name' => 'status_id',
            'entity' => 'enrollmentStatus',
            'attribute' => 'name',
            'model' => EnrollmentStatusType::class,
            'wrapper' => ['element' => 'span',
                'class' => function ($crud, $column, $entry) {
                    return 'badge badge-pill badge-'.$entry->enrollmentStatus->styling();
                }, ], ]]);

        if (config('academico.books_module') && $this->mode === 'course') {
            CRUD::addColumn(['name' => 'hasBook',
                'type' => 'model_function',
                'function_name' => 'getHasBookForCourseAttribute',
                'label' => __('Book'),
            ]);
        }

        if (config('invoicing.allow_scheduled_payments')) {
            CRUD::addColumn([
                'name' => 'scheduledPayments',
                'type' => 'select',
                'label' => __('Scheduled Payments'),
                'attribute' => 'date',
                'model' => ScheduledPayment::class,
            ]);
        }

        CRUD::addColumn(array_merge([
            'name' => 'price',
            'label' => __('Price'),
            'type' => 'number',
        ], $currency));

        if (config('invoicing.invoices_contain_enrollments_only')) {
            CRUD::addColumn(array_merge([
                'name' => 'balance',
                'label' => __('Balance'),
                'type' => 'number',
            ], $currency));
        }

        CRUD::addColumns([
            [
                'name' => 'scholarships',
                'type' => 'select',
                'label' => __('Scholarship'),
                'attribute' => 'name',
                'model' => Scholarship::class,
                ],

            [
                'label' => __('Email'),
                'name' => 'user',
                'attribute' => 'email',
                'type' => 'select',
            ],

            [
                'label' => __('Phone Number'),
                'type' => 'select_multiple',
                'name' => 'student.phone',
                'attribute' => 'phone_number',
                'model' => PhoneNumber::class,
            ],
        ]);

        if ($this->mode === 'global') {
            CRUD::addFilter([
                'name' => 'status_id',
                'type' => 'select2_multiple',
                'label' => __('Status'),
            ],
            fn () => EnrollmentStatusType::all()->pluck('name', 'id')->toArray(),
                function ($values) {
                    foreach (json_decode($values, null, 512, JSON_THROW_ON_ERROR) as $value) {
                        CRUD::addClause('orWhere', 'status_id', $value);
                    }
                }
            );

            CRUD::addFilter(['name' => 'period_id',
                'type' => 'select2',
                'label' => __('Period'), ], fn () => Period::all()->pluck('name', 'id')->toArray(), function ($value) {
                    CRUD::addClause('period', $value);
                });

            CRUD::addFilter(['name' => 'scholarship',
                'type' => 'select2',
                'label' => __('Scholarship'), ], fn () => Scholarship::all()->pluck('name', 'id')->toArray(), function ($value) {
                    if ($value == 'all') {
                        CRUD::addClause('whereHas', 'scholarships');
                    } else {
                        CRUD::addClause('whereHas', 'scholarships', function ($q) use ($value) {
                            $q->where('scholarships.id', $value);
                        });
                    }
                });
        }

        if ($this->mode === 'global' && $this->crud->getOperation() === 'list' && $this->crud->filters()->where('name', 'status_id')->count() > 0) {
            if ($this->crud->filters()->where('name', 'status_id')->first()->currentValue && in_array(1, json_decode($this->crud->filters()->where('name', 'status_id')->first()->currentValue))) {
                Widget::add()->type('view')->view('enrollments.total_balance_widget')->to('before_content');
            }
        }
    }

    public function show($enrollment)
    {
        $enrollment = Enrollment::findOrFail($enrollment);

        // then load the page
        $commentsIncludingInvoices = $enrollment->comments
            ->concat($enrollment->invoices()
                ->map(fn ($invoice) => $invoice->comments->map(function ($comment) use ($invoice) {
                    $comment->prefix = '('.__('Invoice').' '.($invoice->invoice_reference ?? $invoice->id).')';

                    return $comment;
                }))
                ->flatten()
            );

        return view('enrollments.show', [
            'enrollment' => $enrollment,
            'comments' => $commentsIncludingInvoices,
            'scholarships' => Scholarship::all(),
            'availablePaymentMethods' => Paymentmethod::all(),
            'writeaccess' => $enrollment->status_id !== 2 && backpack_user()->can('enrollments.edit'),
        ]);
    }

    protected function setupUpdateOperation()
    {
        if (config('academico.currency_position') === 'before') {
            $currency = ['prefix' => config('academico.currency_symbol')];
        } else {
            $currency = ['suffix' => config('academico.currency_symbol')];
        }

        CRUD::addField([
            'label' => __('Course'),
            'type' => 'select2',
            'name' => 'course_id',

            'entity' => 'course',
            'model' => Course::class,
            'attribute' => 'name',

            'options' => (fn ($query) => $query->orderBy('level_id', 'ASC')->where('period_id', $this->crud->getCurrentEntry()->course->period_id)->get()),
        ]);

        CRUD::addField(array_merge([
            'name' => 'price',
            'label' => __('Price'),
            'type' => 'number',
        ], $currency));

        if (config('invoicing.allow_scheduled_payments')) {
            CRUD::addField([
                'name' => 'scheduledPayments',
                'label' => __('Scheduled Payments'),
                'type' => 'relationship',
                'subfields' => [
                    [
                        'name' => 'date',
                        'type' => 'date',
                        'label' => __('Date'),
                        'wrapper' => ['class' => 'form-group col-md-4'],
                    ],
                    array_merge([
                        'name' => 'value',
                        'type' => 'number',
                        'attributes' => ['step' => 0.01,
                            'min' => 0, ],
                        'label' => __('Value'),
                        'wrapper' => ['class' => 'form-group col-md-4'],
                    ], $currency),
                    [
                        'name' => 'status',
                        'type' => 'radio',
                        'label' => __('Status'),
                        'wrapper' => ['class' => 'form-group col-md-4'],
                        'options' => [
                            1 => __('Pending'),
                            2 => __('Paid'),
                        ],
                        'inline' => true,
                    ],
                ],
            ]);
        }

        CRUD::addField([
            'label' => __('Status'),
            'type' => 'select',
            'name' => 'status_id',
            'entity' => 'enrollmentStatus',
            'model' => EnrollmentStatusType::class,
            'attribute' => 'name',
        ]);
    }

    public function destroy($enrollment)
    {
        $enrollment = Enrollment::findOrFail($enrollment);
        $enrollment->cancel();

        Log::notice('Enrollment canceled by user '.backpack_user()->id);
    }
}
