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
       //$times = CourseTime::where('course_id', $course->id)->get();
       return view('courses/create4', compact('course'));
       // after refactor with VueSJS, no need for additionnal views
   }

   /**
    * Store a newly created resource in storage.
    *
    * @param  \Illuminate\Http\Request  $request
    * @return \Illuminate\Http\Response
    */
   public function store(Course $course, Request $request)
   {

        $newTime = new CourseTime;

        $newTime->course_id = 1489;
        $newTime->day = $request->input('day');
        $newTime->start = $request->input('start');
        $newTime->end = $request->input('end');
        $newTime->save();

        // return something ?
    }


   /**
    * Remove the specified resource from storage.
    *
    * @param  int  $id
    * @return \Illuminate\Http\Response
    */
   public function destroy($id)
   {
       CourseTime::findOrFail($id)->delete();
   }
}

