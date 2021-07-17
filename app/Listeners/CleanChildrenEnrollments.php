<?php

namespace App\Listeners;

use App\Events\CourseUpdated;
use App\Events\EnrollmentUpdated;
use App\Events\EnrollmentUpdating;
use App\Models\Attendance;
use App\Models\Enrollment;
use App\Models\Event;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class CleanChildrenEnrollments
{
    public function handle(EnrollmentUpdating $event)
    {
        $enrollment = $event->enrollment;

        // If the course was changed, also update children
        if ($enrollment->isDirty('course_id'))
        {
            // if enrollment has children, delete them
            Enrollment::where('parent_id', $enrollment->id)->delete();

            // delete attendance
            foreach ($enrollment->course->events as $event) {
                Attendance::where('event_id', $event->id)->where('student_id', $enrollment->student_id)->delete();
            }

            foreach ($enrollment->course->children as $child) {
                foreach ($child->events as $event) {
                    Attendance::where('event_id', $event->id)->where('student_id', $enrollment->student_id)->delete();
                }
            }

            // TODO delete grades and/or skills
            // TODO dispatch event to LMS
        }

        // If the status has changed to paid, also update children
        if ($enrollment->isDirty('status_id'))
        {
            foreach ($enrollment->childrenEnrollments as $child) {
                $child->status_id = $enrollment->status_id;
                $child->save();
            }
        }
    }
}
