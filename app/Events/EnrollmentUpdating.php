<?php

namespace App\Events;

use App\Models\Attendance;
use App\Models\Enrollment;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class EnrollmentUpdating
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    public function __construct(public Enrollment $enrollment)
    {
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
                $child->status_id = $this->enrollment->status_id;
                $child->save();
            }
        }
    }
}
