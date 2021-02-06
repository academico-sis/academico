<?php

namespace App\Listeners;

use App\Events\StudentCreated;
use App\Events\StudentUpdated;
use App\Services\ApolearnService;
use App\Traits\ApolearnApi;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SyncStudentToLMS implements ShouldQueue
{
    use ApolearnApi;

    public function handle(StudentUpdated|StudentCreated $event) : void
    {
        if (!$event->student->user->lms_id) {
            // if the user doesn't exist on the LMS yet, create them
            $this->lms->createUser($event->student->user);
        } else {
            // otherwise, perform an update.
            $this->lms->updateUser($event->student->user);
        }
    }
}
