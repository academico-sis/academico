<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Course;

class ChildCourseController extends Controller
{
    public function store(Course $course)
    {
        // clone the course
        $child_course = Course::create([
            'campus_id' => $course->campus_id,
            'rythm_id' => $course->rythm_id,
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

        /* CourseTime::crate([

        ]); */
        // the evaluation methods

        // and generate the events

        // delete relations linked to the parent course (evaluation, etc)

        // open edit form for review
        return redirect("/course/$child_course->id");

    }
}
