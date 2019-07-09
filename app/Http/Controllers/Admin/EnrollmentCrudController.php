<?php

namespace App\Http\Controllers\Admin;

use App\Models\Course;

// VALIDATION: change the requests to match your own file names if you need form validation
use App\Models\Period;
use App\Models\Enrollment;
use App\Models\EnrollmentStatus;
use Illuminate\Support\Facades\Log;
use App\Models\EnrollmentStatusType;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use App\Http\Requests\EnrollmentRequest as StoreRequest;
use App\Http\Requests\EnrollmentRequest as UpdateRequest;

/**
 * Class EnrollmentCrudController
 * @package App\Http\Controllers\Admin
 * @property-read CrudPanel $crud
 */
class EnrollmentCrudController extends CrudController
{

    public function __construct()
    {
        parent::__construct();
        $this->middleware(['permission:enrollments.view']);
    }


    public function setup()
    {
        /*
        |--------------------------------------------------------------------------
        | CrudPanel Basic Information
        |--------------------------------------------------------------------------
        */
        $this->crud->setModel('App\Models\Enrollment');
        $this->crud->setRoute(config('backpack.base.route_prefix') . '/enrollment');
        $this->crud->setEntityNameStrings('enrollment', 'enrollments');

        $this->crud->allowAccess('show');

        $this->crud->denyAccess('update');
        $this->crud->removeButton('delete');
        $this->crud->denyAccess('create');
        //$this->crud->addClause('parent');


        if(backpack_user()->hasRole('admin'))
        {
            $this->crud->enableExportButtons();
        }
        
        /*
        |--------------------------------------------------------------------------
        | CrudPanel Configuration
        |--------------------------------------------------------------------------
        */

        $this->crud->setColumns([

            [
                'name' => 'id',
                'label' => "ID",
            ],

            [
            // STUDENT NAME
            'label' => __("Student"), // Table column heading
            'type' => "select",
            'entity' => 'student', // the method that defines the relationship in your Model
            'attribute' => "name", // foreign key attribute that is shown to user
            'searchLogic' => function ($query, $column, $searchTerm) {
                $query->orWhereHas('student', function ($q) use ($column, $searchTerm) {
                    $q->WhereHas('user', function ($q) use ($column, $searchTerm) {
                        $q->where('firstname', 'like', '%'.$searchTerm.'%')
                        ->orWhere('lastname', 'like', '%'.$searchTerm.'%');
                    });
                });
            }
            ],



            [
            // COURSE NAME
            'label' => __("Course"), // Table column heading
            'type' => "select",
            'name' => 'course_id', // the column that contains the ID of that connected entity;
            'entity' => 'course', // the method that defines the relationship in your Model
            'attribute' => "name", // foreign key attribute that is shown to user
            'model' => "App\Models\Course", // foreign key model
            ],

            [
            'name' => 'course.period.name',
            'label' => __('Period'),
            'type' => 'text'
            ],

            [
                // STATUS
                'label' => __("Status"), // Table column heading
                'type' => "select",
                'name' => 'status_id', // the column that contains the ID of that connected entity;
                'entity' => 'enrollmentStatus', // the method that defines the relationship in your Model
                'attribute' => "name", // foreign key attribute that is shown to user
                'model' => "App\Models\EnrollmentStatusType", // foreign key model
            ],

        ]);
        

        // add asterisk for fields that are required in EnrollmentRequest
        $this->crud->setRequiredFields(StoreRequest::class, 'create');
        $this->crud->setRequiredFields(UpdateRequest::class, 'edit');


          $this->crud->addFilter([
            'name' => 'status_id',
            'type' => 'select2_multiple',
            'label'=> __('Status')
          ], function() {
              return EnrollmentStatusType::all()->pluck('name', 'id')->toArray();
          },
          function($values) { // if the filter is active
             foreach (json_decode($values) as $key => $value) {
                 $this->crud->addClause('orWhere', 'status_id', $value);
             }
             });

          $this->crud->addFilter([
            'type' => 'simple',
            'name' => 'hidechildren',
            'label'=> __('Hide Children')
          ],
          false,
          function() {
              $this->crud->addClause('parent'); 
          });

        $this->crud->addFilter([
            'name' => 'period_id',
            'type' => 'select2',
            'label'=> __('Period')
          ], function() {
              return Period::all()->pluck('name', 'id')->toArray();
          }, function($value) { // if the filter is active
            $this->crud->addClause('period', $value); 
          });



    }

    public function store(StoreRequest $request)
    {
        // your additional operations before save here
        $redirect_location = parent::storeCrud($request);
        // your additional operations after save here
        // use $this->data['entry'] or $this->crud->entry
        return $redirect_location;
    }

    public function update(UpdateRequest $request)
    {
        // your additional operations before save here
        $redirect_location = parent::updateCrud($request);
        // your additional operations after save here
        // use $this->data['entry'] or $this->crud->entry
        return $redirect_location;
    }

    public function show($enrollment)
    {
        $enrollment = Enrollment::findOrFail($enrollment);

        // load the products from the invoice tables
        $products = $enrollment->pre_invoice()
            ->with('pre_invoice_details')
            ->get();
        
        // get related comments
        // todo also get the comments related to children / parent enrollments
        $comments = $enrollment->comments;
        
        $availableCourses = Course::where('period_id', $enrollment->course->period_id)->get();

        // then load the page
        return view('enrollments.show', compact('enrollment', 'products', 'comments', 'availableCourses'));
    }

        /**
     * Change the status of the enrollment to ANULADO
     * todo/Later, use softdeletes instead?
     *
     * @param  \App\Models\Enrollment  $enrollment
     * @return \Illuminate\Http\Response
     */
    public function destroy($enrollment)
    {
        $enrollment = Enrollment::findOrFail($enrollment);
        if ($enrollment->status_id == 1) {
            $enrollment->cancel();
        }
        
        Log::notice('Enrollment canceled by user ' . backpack_user()->id);
    }
}
