<?php

namespace App\Http\Controllers;

use App\Models\CourseTime;
use App\Models\Course;

use Illuminate\Http\Request;

class CourseTimeController extends Controller
{
    /**
    * Display a listing of the resource.
    *
    * @return \Illuminate\Http\Response
    */
   public function show(Course $course)
   {
       // serve info via ajax
       $times = CourseTime::where('course_id', $course->id)->get();

       return $times;
   }

   /**
    * Show the form for creating a new resource.
    *
    * @return \Illuminate\Http\Response
    */
   public function edit(Course $course)
   {
       return view('courses.schedule', compact('course'));
   }

   /**
    * Store a newly created resource in storage.
    *
    * @param  \Illuminate\Http\Request  $request
    * @return \Illuminate\Http\Response
    */
   public function store(Course $course, Request $request)
   {

        // register the new course schedule entry
        $newTime = new CourseTime;

        $newTime->course_id = $course->id;
        $newTime->day = $request->input('day');
        $newTime->start = $request->input('start');
        $newTime->end = $request->input('end');
        $newTime->save();

        // create events for the new course time

        $newTime->create_events();

        //return $newTime->id; // necessary ?
    }


   /**
    * Remove the specified resource from storage.
    *
    * @param  int  $id
    * @return \Illuminate\Http\Response
    */
   public function destroy($id)
   {
      $coursetime = CourseTime::findOrFail($id);

      // delete associated events
      $coursetime->delete_events();

      // delete the course time entry
      CourseTime::findOrFail($id)->delete();

   }
}

