<?php

namespace App\Http\Controllers\Admin;

use App\Models\Course;

// VALIDATION: change the requests to match your own file names if you need form validation
use Illuminate\Support\Facades\Log;
use App\Http\Requests\CourseRequest as StoreRequest;
use App\Http\Requests\CourseRequest as UpdateRequest;
use Backpack\CRUD\app\Http\Controllers\CrudController;

/**
 * Class CourseCrudController
 * @package App\Http\Controllers\Admin
 * @property-read CrudPanel $crud
 */
class CourseCrudController extends CrudController
{
    public function __construct()
    {
        parent::__construct();
        $this->middleware(['permission:courses.view']);
    }

    public function setup()
    {
        /*
        |--------------------------------------------------------------------------
        | CrudPanel Basic Information
        |--------------------------------------------------------------------------
        */
        $this->crud->setModel('App\Models\Course');
        $this->crud->setRoute(config('backpack.base.route_prefix') . '/course');
        $this->crud->setEntityNameStrings('course', 'courses');

        $permissions = backpack_user()->getAllPermissions();
        if(!$permissions->contains('name', 'courses.delete')) { $this->crud->denyAccess('delete'); }
        if(!$permissions->contains('name', 'courses.edit')) { $this->crud->denyAccess('update'); }
        if($permissions->contains('name', 'courses.edit')) { $this->crud->allowAccess('clone'); }
        if(!$permissions->contains('name', 'courses.edit')) { $this->crud->denyAccess('create'); }

        if($permissions->contains('name', 'courses.view')) {$this->crud->allowAccess('show'); }
        if($permissions->contains('name', 'attendance.view')) {
            $this->crud->addButtonFromView('line', 'attendance', 'attendance', 'end');
        }

        if($permissions->contains('name', 'evaluation.edit')) {
            $this->crud->addButtonFromView('line', 'skills', 'skills', 'end');
        }

        if($permissions->contains('name', 'evaluation.edit')) {
            $this->crud->addButtonFromView('line', 'skillsevaluation', 'skillsevaluation', 'end');
        }

        if($permissions->contains('name', 'evaluation.edit')) {
            $this->crud->addButtonFromView('line', 'grades', 'grades', 'end');
        }

        if($permissions->contains('name', 'courses.edit')) {
            $this->crud->addButtonFromView('line', 'schedule', 'schedule', 'end');
        }

        $this->crud->addButtonFromView('line', 'children_badge', 'children_badge', 'beginning');
        

        /*
        |--------------------------------------------------------------------------
        | CrudPanel Configuration
        |--------------------------------------------------------------------------
        */

        $this->crud->setColumns([
            [
            // RYTHM
            'label' => __('Rhythm'), // Table column heading
            'type' => "select",
            'name' => 'rhythm_id', // the column that contains the ID of that connected entity;
            'entity' => 'rhythm', // the method that defines the relationship in your Model
            'attribute' => "name", // foreign key attribute that is shown to user
            'model' => "App\Models\Rhythm", // foreign key model
            ],

            [
            // LEVEL
            'label' => __('Level'), // Table column heading
            'type' => "select",
            'name' => 'level_id', // the column that contains the ID of that connected entity;
            'entity' => 'level', // the method that defines the relationship in your Model
            'attribute' => "name", // foreign key attribute that is shown to user
            'model' => "App\Models\Level", // foreign key model
            ],

            [
            'name' => 'name', // The db column name
            'label' => __('Name'), // Table column heading
            ],

            [
            'name' => 'volume', // The db column name
            'label' => __('Volume'), // Table column heading
            'suffix' => "h",
            ],

            [
            // TEACHER
            'label' => __('Teacher'), // Table column heading
            'type' => "select",
            'name' => 'teacher_id', // the column that contains the ID of that connected entity;
            'entity' => 'teacher', // the method that defines the relationship in your Model
            'attribute' => "name", // foreign key attribute that is shown to user
            'model' => "App\Models\Teacher", // foreign key model
            ],

            [
            // ROOM
            'label' => __("Room"), // Table column heading
            'type' => "select",
            'name' => 'room_id', // the column that contains the ID of that connected entity;
            'entity' => 'room', // the method that defines the relationship in your Model
            'attribute' => "name", // foreign key attribute that is shown to user
            'model' => "App\Models\Room", // foreign key model
            ],

            // COURSE SCHEDULED TIMES
            [
            'name' => "times",
            'label' => __("Schedule"), // Table column heading
            'type' => "model_function",
            'function_name' => 'getCourseTimesAttribute', // the method in your Model
            'limit' => 150, // Limit the number of characters shown
            ],

            // EVALUATION METHODS
            [
            // n-n relationship (with pivot table)
            'label' => __("Evaluation method"), // Table column heading
            'type' => "select_multiple",
            'name' => 'evaluation_type', // the method that defines the relationship in your Model
            'entity' => 'evaluation_type', // the method that defines the relationship in your Model
            'attribute' => "name", // foreign key attribute that is shown to user
            'model' => "App\Models\EvaluationType", // foreign key model
            ],


            // ENROLLMENTS COUNT
            [
            'name' => "enrollments",
            'label' => __("Enrollments"), // Table column heading
            'type' => "model_function",
            'function_name' => 'getCourseEnrollmentsCountAttribute', // the method in your Model
            // 'limit' => 100, // Limit the number of characters shown
            ],

            [
            'name' => "start_date", // The db column name
            'label' => __("Start Date"), // Table column heading
            'type' => "date",
                // 'format' => 'l j F Y', // use something else than the base.default_date_format config value
            ],

            [
            'name' => "end_date", // The db column name
            'label' => __("End Date"), // Table column heading
            'type' => "date",
                // 'format' => 'l j F Y', // use something else than the base.default_date_format config value
            ],
             
        ]);


        $this->crud->addFilter([ // select2 filter
            'name' => 'campus_id',
            'type' => 'select2',
            'label'=> __('Campus')
          ], function() {
              return \App\Models\Campus::all()->pluck('name', 'id')->toArray();
          }, function($value) { // if the filter is active
                  $this->crud->addClause('where', 'campus_id', $value);
          },
          function () { // if the filter is NOT active (the GET parameter "checkbox" does not exit)
            $this->crud->addClause('where', 'campus_id', '1');
            $this->crud->request->request->add(['campus_id' => 1]); // to make the filter look active
        });


        $this->crud->addFilter([ // select2 filter
            'name' => 'level_id',
            'type' => 'select2',
            'label'=> __('Level')
          ], function() {
              return \App\Models\Level::all()->pluck('name', 'id')->toArray();
          }, function($value) { // if the filter is active
                  $this->crud->addClause('where', 'level_id', $value);
          },
          function () { // if the filter is NOT active (the GET parameter "checkbox" does not exit)
        });

        $this->crud->addFilter([ // select2 filter
            'name' => 'period_id',
            'type' => 'select2',
            'label'=> __('Period')
          ], function() {
              return \App\Models\Period::all()->pluck('name', 'id')->toArray();
          }, function($value) { // if the filter is active
                  $this->crud->addClause('where', 'period_id', $value);
          },
          function () { // if the filter is NOT active (the GET parameter "checkbox" does not exit)
            $period = \App\Models\Period::get_default_period()->id;
            $this->crud->addClause('where', 'period_id', $period);
            $this->crud->request->request->add(['period_id' => $period]); // to make the filter look active
        });


        $this->crud->addFilter([ // add a "simple" filter called Draft 
            'type' => 'simple',
            'name' => 'parent',
            'label'=> __('Hide Children Courses')
          ],
          false,
          function() {
              $this->crud->addClause('parent'); 
          });


        $this->crud->addFields([
            [
                // RYTHM
                'label' => __("Rhythm"), // Table column heading
                'type' => "select",
                'name' => 'rhythm_id', // the column that contains the ID of that connected entity;
                'entity' => 'rhythm', // the method that defines the relationship in your Model
                'attribute' => "name", // foreign key attribute that is shown to user
                'model' => "App\Models\Rhythm", // foreign key model
                'tab' => __('Course info')
             ],

             [
                // LEVEL
                'label' => __("Level"), // Table column heading
                'type' => "select",
                'name' => 'level_id', // the column that contains the ID of that connected entity;
                'entity' => 'level', // the method that defines the relationship in your Model
                'attribute' => "name", // foreign key attribute that is shown to user
                'model' => "App\Models\Level", // foreign key model
                'tab' => __('Course info')
             ],

             [
                'name' => 'name', // The db column name
                'label' => __("Name"), // Table column heading
                'tab' => __('Course info')
             ],

             [
                'name' => 'price', // The db column name
                'label' => __("Price"), // Table column heading
                'tab' => __('Course info')
             ],

             [
                'name' => 'volume', // The db column name
                'label' => __("Volume"), // Table column heading
                'suffix' => "h",
                'tab' => __('Course info')
             ],

             [
                // TEACHER
                'label' => __("Teacher"), // Table column heading
                'type' => "select",
                'name' => 'teacher_id', // the column that contains the ID of that connected entity;
                'entity' => 'teacher', // the method that defines the relationship in your Model
                'attribute' => "name", // foreign key attribute that is shown to user
                'model' => "App\Models\Teacher", // foreign key model
                'tab' => __('Resources')
             ],

             [
                // ROOM
                'label' => __("Room"), // Table column heading
                'type' => "select",
                'name' => 'room_id', // the column that contains the ID of that connected entity;
                'entity' => 'room', // the method that defines the relationship in your Model
                'attribute' => "name", // foreign key attribute that is shown to user
                'model' => "App\Models\Room", // foreign key model
                'tab' => __('Resources')
             ],

             [
                // RYTHM
                'label' => __("Campus"), // Table column heading
                'type' => "select",
                'name' => 'campus_id', // the column that contains the ID of that connected entity;
                'entity' => 'campus', // the method that defines the relationship in your Model
                'attribute' => "name", // foreign key attribute that is shown to user
                'model' => "App\Models\Campus", // foreign key model
                'tab' => __('Resources')
             ],


             // EVALUATION METHODS
             [
                // n-n relationship (with pivot table)
                'label' => __("Evaluation method"), // Table column heading
                'type' => "select_multiple",
                'name' => 'evaluation_type', // the method that defines the relationship in your Model
                'entity' => 'evaluation_type', // the method that defines the relationship in your Model
                'attribute' => "name", // foreign key attribute that is shown to user
                'model' => "App\Models\EvaluationType", // foreign key model
                'pivot' => true, // on create&update, do you need to add/delete pivot table entries?
                'tab' => __('Pedagogy')

             ],


             [
                // PERIOD
                'label' => __("Period"), // Table column heading
                'type' => "select",
                'name' => 'period_id', // the column that contains the ID of that connected entity;
                'entity' => 'period', // the method that defines the relationship in your Model
                'attribute' => "name", // foreign key attribute that is shown to user
                'model' => "App\Models\Period", // foreign key model
                'tab' => __('Schedule')
             ],

            [
                'name' => "start_date", // The db column name
                'label' => __("Start Date"),
                'type' => "date",
                 // 'format' => 'l j F Y', // use something else than the base.default_date_format config value
                 'tab' => __('Schedule')

             ],

             [
                'name' => "end_date", // The db column name
                'label' => __("End Date"), // Table column heading
                'type' => "date",
                 // 'format' => 'l j F Y', // use something else than the base.default_date_format config value
                 'tab' => __('Schedule')

             ],

             ]);



        // add asterisk for fields that are required in CourseRequest
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
        
        // check whether the course events match the teacher or room now assigned to the course
        $outdated_teacher_events = $this->crud->entry->events
            ->where('teacher_id', '!=', $this->crud->entry->teacher_id);

        $outdated_room_events = $this->crud->entry->events
            ->Where('room_id', '!=', $this->crud->entry->room_id);

        // if a mismatch exists, offer to update the events
        if($outdated_teacher_events->count() > 0 || $outdated_room_events->count() > 0)
        {
            return view('courses.update_events', [
                'outdated_teacher_events' => $outdated_teacher_events,
                'outdated_room_events' => $outdated_room_events,
                'course' => $this->crud->entry,
                ]);
        }

        return $redirect_location;
    }


    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Course  $course
     * @return \Illuminate\Http\Response
     */
    public function show($course)
    {
        $course = Course::findOrFail($course);
        $enrollments = $course->enrollments()->with('student')->get();
        return view('courses/show', compact('course', 'enrollments'));   
    }

    public function clone($id)
{
    $this->crud->hasAccessOrFail('clone');
    $this->crud->setOperation('clone');

    $course = Course::findOrFail($id);

        // clone the course
        $child_course = Course::create([
            'campus_id' => $course->campus_id,
            'rhythm_id' => $course->rhythm_id,
            'level_id' => $course->level_id,
            'volume' => $course->volume,
            'name' => $course->name,
            'price' => $course->price,
            'start_date' => $course->start_date,
            'end_date' => $course->end_date,
            'room_id' => $course->room_id,
            'teacher_id' => $course->teacher_id,
            'parent_course_id' => $course->id,
            'exempt_attendance' => $course->exempt_attendance,
            'period_id' => $course->period_id,
            'opened' => $course->opened,
            'spots' => $course->spots,
        ]);

        // also clone the coursetime events

/*         CourseTime::create([

        ]); */
        
        // the evaluation methods

        // and generate the events

        // delete relations linked to the parent course (evaluation, etc)

        // open edit form for review
        return redirect("/course/$child_course->id/edit");
    }

    public function destroy($id)
   {
      $this->crud->hasAccessOrFail('delete');
      return $this->crud->delete($id);
   }

}
