<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\EnrollmentUpdateRequest;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\EnrollmentStatusType;
use App\Models\Paymentmethod;
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

    private array $currency;

    public function __construct()
    {
        $this->middleware(['permission:enrollments.view']);
        $this->middleware('permission:enrollments.delete', ['only' => ['destroy']]);

        if (config('academico.currency_position') === 'before') {
            $this->currency = ['prefix' => config('academico.currency_symbol')];
        } else {
            $this->currency = ['suffix' => config('academico.currency_symbol')];
        }

        parent::__construct();
    }

    public function setup()
    {
        CRUD::setModel(Enrollment::class);
        CRUD::setRoute(config('backpack.base.route_prefix').'/enrollment');
        CRUD::setEntityNameStrings(__('enrollment'), __('enrollments'));
    }

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

        if ($this->mode === 'course') {
            Widget::add(['type' => 'view',
                'view' => 'partials.course_info',
                'course' => $this->course, ])->to('before_content');

            CRUD::denyAccess(['show']);
            CRUD::addButtonFromView('line', 'showStudent', 'showStudentForEnrollment');

            CRUD::addButtonFromView('top', 'enroll-student-in-course', 'enroll-student-in-course', 'end');
            CRUD::addButtonFromView('top', 'sendEmail', 'send-students-email','left');
            CRUD::addButtonFromView('top', 'switch-to-photo-roster', 'switch-to-photo-roster', 'left');

        }

        CRUD::addColumns([
            [
                'name' => 'id',
                'label' => 'ID',
                'type' => 'string',
                'wrapper' => ['element' => function ($crud, $column, $entry) {
                    return $entry->status_id > 2 ? 'del' : 'span';
                }],
            ],

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

            [
                'label' => __('Gender'),
                'type' => 'text',
                'name' => 'student.gender_id',
                'value' => function ($entry) {
                    return $entry->student->formatted_gender;
                },
            ],

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
            'name' => 'total_price',
            'label' => __('Price'),
            'type' => 'number',
        ], $this->currency));

        if (config('invoicing.invoices_contain_enrollments_only')) {
            CRUD::addColumn(array_merge([
                'name' => 'balance',
                'label' => __('Balance'),
            ], $this->currency));
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
        CRUD::setValidation(EnrollmentUpdateRequest::class);

        CRUD::addField([
            'label' => __('Course'),
            'type' => 'select',
            'name' => 'course_id',
            'entity' => 'course',
            'model' => Course::class,
            'attribute' => 'name',
            'options' => (fn ($query) => $query->orderBy('level_id', 'ASC')->where('period_id', $this->crud->getCurrentEntry()->course->period_id)->get()),
        ]);

        CRUD::addField(array_merge([
            'name' => 'total_price',
            'label' => __('Price'),
            'type' => 'number',
        ], $this->currency));

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
