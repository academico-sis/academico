<?php

namespace App\Listeners;

use App\Events\StudentDeleting;
use App\Events\UserDeleting;
use App\Traits\ApolearnApi;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Log;

class DeleteStudentFromLMS
{
    use ApolearnApi;

    public function handle(StudentDeleting $event) : void
    {
        Log::alert('The user ' . $event->student->user->id . ' has been deleted');

        // remove student from every course they are enrolled in
        foreach ($event->student->enrollments as $enrollment) {
            $this->lms->removeStudent($enrollment->course, $event->student);
        }
    }
}
