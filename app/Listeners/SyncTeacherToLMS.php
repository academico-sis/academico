<?php

namespace App\Listeners;

use App\Events\TeacherCreated;
use App\Events\TeacherUpdated;
use App\Traits\ApolearnApi;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SyncTeacherToLMS implements ShouldQueue
{
    use ApolearnApi;

    public function handle(TeacherCreated|TeacherUpdated $event) : void
    {
        if (!$event->teacher->user->lms_id) {
            // if the user doesn't exist on the LMS yet, create them
            $this->lms->createUser($event->teacher->user);
        } else {
            // otherwise, perform an update.
            $this->lms->updateUser($event->teacher->user);
        }
    }
}
