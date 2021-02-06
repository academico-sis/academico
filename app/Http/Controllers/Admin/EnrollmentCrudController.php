<?php

namespace App\Http\Controllers\Admin;

// VALIDATION: change the requests to match your own file names if you need form validation
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\EnrollmentStatusType;
use App\Models\Period;
use App\Models\PhoneNumber;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;
use Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
use Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;
use Illuminate\Support\Facades\Log;

/**
 * Class EnrollmentCrudController
 * This controller is used to view enrollments only.
 * No enrollments may be created or updated from here.
 */
class EnrollmentCrudController extends CrudController
{
    use ListOperation;
    use ShowOperation;
    use DeleteOperation;

    public function __construct()
    {
        parent::__construct();
        $this->middleware(['permission:enrollments.view']);
        $this->middleware('permission:enrollments.delete', ['only' => ['destroy']]);
    }

    public function setup()
    {
        /*
        |--------------------------------------------------------------------------
        | CrudPanel Basic Information
        |--------------------------------------------------------------------------
        */
        CRUD::setModel(Enrollment::class);
        CRUD::setRoute(config('backpack.base.route_prefix').'/enrollment');
        CRUD::setEntityNameStrings(__('enrollment'), __('enrollments'));

        CRUD::allowAccess('show');

        CRUD::denyAccess('delete'); // hide the button from the list view

        if (backpack_user()->hasRole('admin')) {
            CRUD::enableExportButtons();
        }
    }

    /*
    |--------------------------------------------------------------------------
    | CrudPanel Configuration
    |--------------------------------------------------------------------------
    */

    public function setupListOperation()
    {
        CRUD::setColumns([

            [
                'name' => 'id',
                'label' => 'ID',
            ],

            [
                'name' => 'user.lastname',
                'label' => __('Last Name'),
                'type' => 'text',
                'searchLogic' => function ($query, $column, $searchTerm) {
                    $query->orWhereHas('student', function ($q) use ($searchTerm) {
                        $q->whereHas('user', function ($q) use ($searchTerm) {
                            $q->where('lastname', 'like', '%'.$searchTerm.'%');
                        });
                    });
                },
            ],

            [
                'name' => 'user.firstname',
                'label' => __('First Name'),
                'type' => 'text',
                'searchLogic' => function ($query, $column, $searchTerm) {
                    $query->orWhereHas('student', function ($q) use ($searchTerm) {
                        $q->whereHas('user', function ($q) use ($searchTerm) {
                            $q->where('firstname', 'like', '%'.$searchTerm.'%');
                        });
                    });
                },
            ],

            [
                // COURSE NAME
                'label' => __('Course'), // Table column heading
                'type' => 'select',
                'name' => 'course_id', // the column that contains the ID of that connected entity;
                'entity' => 'course', // the method that defines the relationship in your Model
                'attribute' => 'name', // foreign key attribute that is shown to user
                'model' => Course::class, // foreign key model
            ],

            [
                'name' => 'course.period.name',
                'label' => __('Period'),
                'type' => 'text',
            ],

            [
                // STATUS
                'label' => __('Status'), // Table column heading
                'type' => 'select',
                'name' => 'status_id', // the column that contains the ID of that connected entity;
                'entity' => 'enrollmentStatus', // the method that defines the relationship in your Model
                'attribute' => 'name', // foreign key attribute that is shown to user
                'model' => EnrollmentStatusType::class, // foreign key model
            ],

            [
                // n-n relationship (with pivot table)
                'label' => __('Phone number'), // Table column heading
                'type' => 'select_multiple',
                'name' => 'student.phone', // the method that defines the relationship in your Model
                'attribute' => 'phone_number', // foreign key attribute that is shown to user
                'model' => PhoneNumber::class, // foreign key model
            ],

        ]);

        CRUD::addFilter([
            'name' => 'status_id',
            'type' => 'select2_multiple',
            'label'=> __('Status'),
        ], function () {
            return EnrollmentStatusType::all()->pluck('name', 'id')->toArray();
        },
        function ($values) {
            // if the filter is active
            foreach (json_decode($values) as $value) {
                CRUD::addClause('orWhere', 'status_id', $value);
            }
        });

        CRUD::addFilter([
            'name' => 'period_id',
            'type' => 'select2',
            'label'=> __('Period'),
        ], function () {
            return Period::all()->pluck('name', 'id')->toArray();
        }, function ($value) {
            // if the filter is active
            CRUD::addClause('period', $value);
        });
    }

    public function show($enrollment)
    {
        $enrollment = Enrollment::findOrFail($enrollment);

        // get related comments
        $comments = $enrollment->comments;

        // then load the page
        return view('enrollments.show', compact('enrollment', 'comments'));
    }

    public function destroy($enrollment)
    {
        $enrollment = Enrollment::findOrFail($enrollment);
        $enrollment->cancel();

        Log::notice('Enrollment canceled by user '.backpack_user()->id);
    }
}
