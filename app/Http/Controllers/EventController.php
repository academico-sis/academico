<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Room;
use App\Models\Teacher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class EventController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:courses.view');
        $this->middleware('permission:courses.edit', ['only' => ['syncEventsTeacher', 'update_course_teacher', 'update_course_room', 'syncEventsRoom', 'store', 'destroy']]);
    }

    /**
     * get all events for a course.
     */
    public function getCourseEvents($course)
    {
        $this->middleware(['permission:courses.view']);

        return Course::findOrFail($course)->events;
    }

    public function update_course_teacher(Request $request)
    {
        Log::notice('Calendar events updated by user '.backpack_user()->id);
        $course = Course::findOrFail($request->input('course_id'));
        if ($request->input('resource_id') == 'tbd') {
            $teacher_id = null;
        } else {
            $teacher_id = Teacher::findOrFail($request->input('resource_id'))->id;
        }

        $course->teacher_id = $teacher_id;
        $course->save();
        $course->events()->update(['teacher_id' => $teacher_id]);

        // if the course has a parent, update the children of the parent as well.
        if ($course->parent_course_id !== null) {
            $parent = Course::find($course->parent_course_id);
            $parent->teacher_id = $teacher_id;
            foreach ($parent->children as $child) {
                $child->events()->update(['teacher_id' => $teacher_id]);
            }
        }
    }

    public function update_course_room(Request $request)
    {
        Log::notice('Calendar events updated by user '.backpack_user()->id);

        $course = Course::findOrFail($request->input('course_id'));
        if ($request->input('resource_id') == 'tbd') {
            $room_id = null;
        } else {
            $room_id = Room::findOrFail($request->input('resource_id'))->id;
        }

        $course->room_id = $room_id;
        $course->save();
        $course->events()->update(['room_id' => $room_id]);
        // if the course has a parent, update the children of the parent as well.
        if ($course->parent_course_id !== null) {
            $parent = Course::find($course->parent_course_id);
            $parent->room_id = $room_id;
            foreach ($parent->children as $child) {
                $child->events()->update(['room_id' => $room_id]);
            }
        }
    }
}
