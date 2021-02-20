<?php

namespace App\Listeners;

use App\Events\EnrollmentCreated;
use App\Events\EnrollmentUpdated;
use App\Interfaces\LMSInterface;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Log;

class SyncEnrollmentToLMS implements ShouldQueue
{
    public function __construct(public LMSInterface $lms)
    {
    }

    public function handle(EnrollmentCreated|EnrollmentUpdated $event) : void
    {
        if ($event->enrollment->course->sync_to_lms) {
            Log::info('enroll student ' . $event->enrollment->student_id . ' into course ' . $event->enrollment->course_id);
            $this->lms->enrollStudent($event->enrollment->course, $event->enrollment->student);
        }
    }
}
