<?php

namespace App\Listeners;

use App\Events\UserDeleting;
use App\Models\Attendance;
use App\Models\Student;
use App\Models\Teacher;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class DeleteUserData
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  UserDeleting  $event
     * @return void
     */
    public function handle(UserDeleting $event)
    {
        // Delete student and teacher
        Student::where('id', $event->user->id)->delete();
        Teacher::where('id', $event->user->id)->delete();
    }
}
