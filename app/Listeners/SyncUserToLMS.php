<?php

namespace App\Listeners;

use App\Events\UserUpdated;
use App\Traits\ApolearnApi;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SyncUserToLMS implements ShouldQueue
{
    use ApolearnApi;

    public function handle(UserUpdated $event) : void
    {
        if (!$event->user->lms_id) {
            // if the user doesn't exist on the LMS yet, create them
            $this->lms->createUser($event->user);
        } else {
            // otherwise, perform an update.
            $this->lms->updateUser($event->user);
        }
    }
}
