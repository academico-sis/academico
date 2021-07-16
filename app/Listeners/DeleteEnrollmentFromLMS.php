<?php

namespace App\Listeners;

use App\Events\EnrollmentDeleted;
use App\Events\EnrollmentCourseUpdated;
use App\Interfaces\LMSInterface;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class DeleteEnrollmentFromLMS implements ShouldQueue
{
    public function __construct(public LMSInterface $lms)
    {
    }

    public function handle(EnrollmentDeleted|EnrollmentCourseUpdated $event) : void
    {
        $this->lms->removeStudent($event->enrollment->course->lms_id, $event->enrollment->student->user->lms_id);
    }
}
