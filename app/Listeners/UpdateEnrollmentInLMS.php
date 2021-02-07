<?php

namespace App\Listeners;

use App\Events\EnrollmentCreated;
use App\Events\EnrollmentUpdated;
use App\Events\UserCreated;
use App\Events\StudentDeleting;
use App\Events\StudentUpdated;
use App\Traits\ApolearnApi;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Log;

class UpdateEnrollmentInLMS implements ShouldQueue
{
    use ApolearnApi;

    public function handle(EnrollmentUpdated $event) : void
    {
        // remove student from initial course
        if ($event->student->user->lms_id) {
            $this->lms->removeStudent($event->previousCourse->lms_id, $event->student->user->lms_id);
        }

        // if the new course is synced with LMS, enroll in new course
        if ($event->newCourse->sync_to_lms && $event->newCourse->lms_id) {
            Log::info('enroll student (local ID) ' . $event->student->id . ' into course (local ID) ' . $event->newCourse->id);
            $this->lms->enrollStudent($event->newCourse, $event->student);
        }
    }
}
