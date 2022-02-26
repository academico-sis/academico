<?php

namespace App\Listeners;

use App\Events\StudentUpdated;
use App\Events\TeacherUpdated;
use App\Events\UserUpdated;
use App\Models\User;

class UpdateUsername
{
    public function handle(TeacherUpdated|StudentUpdated|UserUpdated $event)
    {
        if (User::whereUsername($event->user->email)->count() === 0) {
            $event->user->update(['username' => $event->user->email]);
        }
    }
}
