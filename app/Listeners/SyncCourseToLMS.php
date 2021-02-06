<?php

namespace App\Listeners;

use App\Events\CourseCreated;
use App\Events\CourseUpdated;
use App\Models\Event;
use App\Services\ApolearnService;
use App\Traits\ApolearnApi;
use Carbon\Carbon;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;

class SyncCourseToLMS implements ShouldQueue
{
    use ApolearnApi;

    public function handle($event)
    {
        $course = $event->course;

        if (! $course->sync_to_lms) {
            return ;
        }

        // if the course doesn't exist on the LMS yet, create it
        if (!$course->lms_id) {
            $this->lms->createCourse($course);
        } else {
            // otherwise, update it.
            $this->lms->updateCourse($course);
        }
    }
}
