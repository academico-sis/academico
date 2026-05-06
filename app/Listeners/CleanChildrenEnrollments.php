<?php

namespace App\Listeners;

use App\Events\EnrollmentUpdating;
use App\Models\Attendance;
use App\Models\Enrollment;
use App\Models\Grade;
use App\Models\Skills\SkillEvaluation;
use App\Traits\ReportsErrors;

class CleanChildrenEnrollments
{
    use ReportsErrors;

    public function handle(EnrollmentUpdating $event): void
    {
        $enrollment = $event->enrollment;

        try {
            // If the course was changed, also update children
            if ($enrollment->isDirty('course_id')) {
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

                Grade::where('enrollment_id', $enrollment->id)->delete();
                SkillEvaluation::where('enrollment_id', $enrollment->id)->delete();
            }

            // If the status has changed to paid, also update children
            if ($enrollment->isDirty('status_id')) {
                foreach ($enrollment->childrenEnrollments as $child) {
                    $child->status_id = $enrollment->status_id;
                    $child->save();
                }
            }
        } catch (\Throwable $e) {
            $this->reportError($e, 'CleanChildrenEnrollments::handle', [
                'enrollment_id' => $enrollment->id,
            ]);
        }
    }
}
