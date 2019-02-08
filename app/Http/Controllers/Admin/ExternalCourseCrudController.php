<?php

namespace App\Http\Controllers\Admin;

use Backpack\CRUD\app\Http\Controllers\CrudController;

// VALIDATION: change the requests to match your own file names if you need form validation
use App\Http\Requests\ExternalCourseRequest as StoreRequest;
use App\Http\Requests\ExternalCourseRequest as UpdateRequest;
use Backpack\CRUD\CrudPanel;

/**
 * Class ExternalCourseCrudController
 * @package App\Http\Controllers\Admin
 * @property-read CrudPanel $crud
 */
class ExternalCourseCrudController extends CrudController
{
    public function setup()
    {
        /*
        |--------------------------------------------------------------------------
        | CrudPanel Basic Information
        |--------------------------------------------------------------------------
        */
        $this->crud->setModel('App\Models\ExternalCourse');
        $this->crud->setRoute(config('backpack.base.route_prefix') . '/external');
        $this->crud->setEntityNameStrings('externalcourse', 'external_courses');

        /*
        |--------------------------------------------------------------------------
        | CrudPanel Configuration
        |--------------------------------------------------------------------------
        */

        $this->crud->setColumns([

            [
                'label' => "Course", // Table column heading
                'type' => "select",
                'name' => 'course', // the column that contains the ID of that connected entity;
                'entity' => 'course', // the method that defines the relationship in your Model
                'attribute' => "name", // foreign key attribute that is shown to user
                'model' => "App\Models\Course", // foreign key model
                ],

            // ENROLLMENTS COUNT
            [
                'label' => "Students Count", // Table column heading
                'type' => "number",
                'name' => 'students_count',
                ],

             
        ]);

        $this->crud->addFields([
            [
                'label' => "Course", // Table column heading
                'type' => "select",
                'attributes' => ['disabled' => 'disabled'],
                'name' => 'course_id',
                'entity' => 'course', // the method that defines the relationship in your Model
                'attribute' => "name", // foreign key attribute that is shown to user
                'model' => "App\Models\Course", // foreign key model
            ],

            [
                'label' => 'Students Count',
                'type' => 'number',
                'name' => 'students_count',
            ]
            ]);

        // add asterisk for fields that are required in ExternalCourseRequest
        $this->crud->setRequiredFields(StoreRequest::class, 'create');
        $this->crud->setRequiredFields(UpdateRequest::class, 'edit');
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
}
