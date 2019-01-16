<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Course;

use App\Models\CourseTime;
use Illuminate\Http\Request;

class CourseTimeController extends Controller
{
    /**
    * Returns a list of coursetime records for the specified course
    */
   public function show(Course $course)
   {
        $this->middleware(['permission:courses.view']);

        $times = CourseTime::where('course_id', $course->id)->get();

        return $times;
   }

   /**
    * Show the form for editing the course schedule
    */
   public function edit(Course $course)
   {
        $this->middleware(['permission:courses.edit']);

        return view('courses.schedule', compact('course'));
   }

   /**
    * Store a newly created coursetime record
    * Events creation is triggered by the Model observers
    */
   public function store(Course $course, Request $request)
   {
        $this->middleware(['permission:courses.edit']);

        // register the new course schedule entry
        $newTime = new CourseTime;

        $newTime->course_id = $course->id;
        $newTime->day = $request->input('day');
        $newTime->start = $request->input('start');
        $newTime->end = $request->input('end');
        $newTime->save();
    }


   /**
    * Delete the specified course time.
    * Model hooks will also delete the associated events
    */
   public function destroy($id)
   {
      CourseTime::findOrFail($id)->delete();
   }
}

