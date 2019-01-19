<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Course;
use App\Models\User;
use App\Models\Room;

use Illuminate\Http\Request;

class EventController extends Controller
{
    /**
     * get all events for a course
     */
    public function get_course_events($course)
    {
        $this->middleware(['permission:courses.view']);

        return Course::findOrFail($course)->events;
    }

    public function update_course_teacher(Request $request)
    {
        $course = Course::findOrFail($request->input('course_id'));
        $teacher = User::findOrFail($request->input('resource_id'));

        $course->teacher_id = $teacher->id;
        $course->save();
        $course->events()->update(['teacher_id' => $course->teacher_id]);
    }

    public function update_course_room(Request $request)
    {
        $course = Course::findOrFail($request->input('course_id'));
        $room = Room::findOrFail($request->input('resource_id'));

        $course->room_id = $room->id;
        $course->save();
        $course->events()->update(['room_id' => $course->room_id]);
    }

    /**
     * Rewrite teacher for all events associated to a course
     */
    public function update_teacher(Course $course)
    {
        Event::where('course_id', $course->id)->update(['teacher_id' => $course->teacher_id]);
    }

}
