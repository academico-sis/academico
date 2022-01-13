<?php

namespace App\Listeners;

use App\Events\UserDeleting;
use App\Models\Student;
use App\Models\Teacher;

class DeleteUserData
{
    public function handle(UserDeleting $event): void
    {
        // Delete student and teacher
        Student::where('id', $event->user->id)->delete();
        Teacher::where('id', $event->user->id)->delete();
    }
}
