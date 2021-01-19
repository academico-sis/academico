<?php

namespace App\Listeners;

use App\Events\CourseCreated;
use App\Events\CourseUpdated;
use App\Models\Event;
use App\Services\ApolearnService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class SyncCourseToLMS
{
    public function __construct()
    {
        $this->lms = new ApolearnService();
    }

    public function handle($event)
    {
        $course = $event->course;

        // if the course doesn't exist on the LMS yet, create it
        if (!$course->lms_id) {
            $this->lms->createCourse($course);
        } else {
            // otherwise, update it.
            $this->lms->updateCourse($course);
        }

    }
}
