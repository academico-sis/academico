<?php

namespace App\Listeners;

use App\Events\CourseUpdated;
use App\Models\Event;

class UpdateCourseEvents
{
    public function handle(CourseUpdated $event)
    {
        $course = $event->course;

        // If the course time itself has been modified, it is already handled by CourseTime model events

        // we need to update the events if the dates have changed
        if ($course->isDirty('start_date') || $course->isDirty('end_date')) {
            Event::where('course_id', $course->id)->delete();

            foreach ($course->times as $coursetime) {
                $coursetime->createEvents();
            }
        }

        // update course events with new room, teacher and name
        Event::where('course_id', $course->id)->update(['room_id' => $course->room_id]);
        Event::where('course_id', $course->id)->update(['teacher_id' => $course->teacher_id]);
        Event::where('course_id', $course->id)->update(['name' => $course->name]);
    }
}
