<?php

namespace App\Listeners;

use App\Events\StudentUpdated;
use App\Services\ApolearnService;
use App\Traits\ApolearnApi;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SyncStudentToLMS
{
    use ApolearnApi;

    /**
     * Handle the event.
     *
     * @param  StudentUpdated  $event
     * @return void
     */
    public function handle(StudentUpdated $event)
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
