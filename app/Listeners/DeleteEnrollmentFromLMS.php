<?php

namespace App\Listeners;

use App\Events\EnrollmentDeleted;
use App\Events\StudentUpdated;
use App\Traits\ApolearnApi;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class DeleteEnrollmentFromLMS implements ShouldQueue
{
    use ApolearnApi;

    public function handle(EnrollmentDeleted $event) : void
    {
        $this->lms->removeStudent($event->enrollment->course->lms_id, $event->enrollment->student->user->lms_id);
    }
}
