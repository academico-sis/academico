<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Course;

use App\Models\CourseTime;
use Illuminate\Http\Request;

class CourseTimeController extends Controller
{

     public function __construct()
     {
         $this->middleware('permission:courses.edit');
     }


    /**
    * Returns a list of coursetime records for the specified course
    */
   public function get(Course $course)
   {
        $times = CourseTime::where('course_id', $course->id)->get();

        return $times;
   }

   /**
    * Show the form for editing the course schedule
    */
   public function edit(Course $course)
   {
        return view('courses.schedule', compact('course'));
   }

   /**
    * Store a newly created coursetime record
    * Events creation is triggered by the Model observers
    */
    public function store(Course $course, Request $request)
    {
     // register the new course schedule entry
     
     // if the course has children, register the coursetime for all children instead.
     if ($course->children->count() > 0)
     {
          foreach ($course->children as $child)
          {
               $newTime = new CourseTime;
               $newTime->course_id = $child->id;
               $newTime->day = $request->input('day');
               $newTime->start = $request->input('start');
               $newTime->end = $request->input('end');
               $newTime->save();
          }
     }
     else
     {
          $newTime = new CourseTime;
          $newTime->course_id = $course->id;
          $newTime->day = $request->input('day');
          $newTime->start = $request->input('start');
          $newTime->end = $request->input('end');
          $newTime->save();
     }
     
     
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

