<?php

namespace App\Listeners;

use App\Events\StudentDeleting;
use App\Models\Attendance;
use App\Models\Enrollment;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class DeleteStudentData
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
     * @param  StudentDeleting  $event
     * @return void
     */
    public function handle(StudentDeleting $event)
    {
        Attendance::where('student_id', $event->student->id)->delete();
        Enrollment::where('student_id', $event->student->id)->delete();
    }
}
