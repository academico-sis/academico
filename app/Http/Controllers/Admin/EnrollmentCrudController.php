<?php

namespace App\Http\Controllers\Admin;

use App\Models\Enrollment;

// VALIDATION: change the requests to match your own file names if you need form validation
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

        $permissions = backpack_user()->getAllPermissions();
        if($permissions->contains('name', 'enrollments.view')) {
            $this->crud->allowAccess('show');
        }

        $this->crud->denyAccess('update');
        $this->crud->removeButton('delete');
        $this->crud->denyAccess('create');


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
            'label' => "Student", // Table column heading
            'type' => "select",
            'name' => 'user_id', // the column that contains the ID of that connected entity;
            'entity' => 'student_data', // the method that defines the relationship in your Model
            'attribute' => "name", // foreign key attribute that is shown to user
            'model' => "App\Models\User", // foreign key model
            ],


            [
            // COURSE NAME
            'label' => "Course", // Table column heading
            'type' => "select",
            'name' => 'course_id', // the column that contains the ID of that connected entity;
            'entity' => 'course_data', // the method that defines the relationship in your Model
            'attribute' => "name", // foreign key attribute that is shown to user
            'model' => "App\Models\Course", // foreign key model
            ],

            [
            'name' => 'course_data.period.name',
            'label' => 'Period',
            'type' => 'text'
            ],
        ]);
        

        // add asterisk for fields that are required in EnrollmentRequest
        $this->crud->setRequiredFields(StoreRequest::class, 'create');
        $this->crud->setRequiredFields(UpdateRequest::class, 'edit');



        $this->crud->addFilter([ // add a "simple" filter called Draft 
            'type' => 'simple',
            'name' => 'pending',
            'label'=> 'Pending'
          ],
          false,
          function() {
              $this->crud->addClause('pending'); 
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
        
        // then load the page
        return view('enrollments.show', compact('enrollment', 'products'));
    }

        /**
     * Change the status of the enrollment to ANULADO
     * 
     * todo/Later, use softdeletes instead?
     * anyway, move the logic to the model
     *
     * @param  \App\Models\Enrollment  $enrollment
     * @return \Illuminate\Http\Response
     */
    public function destroy($enrollment)
    {
        $enrollment = Enrollment::findOrFail($enrollment);
        if ($enrollment->status == 1) {
            $enrollment->status = 3;
            $enrollment->save();
        }
    }
}
