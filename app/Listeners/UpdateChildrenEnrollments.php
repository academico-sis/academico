<?php

namespace App\Listeners;

use App\Events\EnrollmentUpdated;
use App\Models\Enrollment;

class UpdateChildrenEnrollments
{
    public function handle(EnrollmentUpdated $event)
    {
        $enrollment = $event->enrollment;

        // If the status has changed to paid, also update children
        if ($enrollment->isDirty('status_id')) {
            foreach ($enrollment->childrenEnrollments as $child) {
                $child->status_id = $enrollment->status_id;
                $child->save();
            }
        }

        if ($enrollment->isDirty('course_id')) {
            // if the new course has children, create an enrollment as well
            foreach ($enrollment->course->children as $children_course) {
                $child_enrollment = Enrollment::firstOrNew([
                    'student_id' =>  $enrollment->student_id,
                    'course_id' => $children_course->id,
                    'parent_id' => $enrollment->id,
                ]);
                $child_enrollment->responsible_id = $enrollment->responsible_id;
                $child_enrollment->save();
            }
        }
    }
}
