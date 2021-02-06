<?php

namespace App\Listeners;

use App\Events\StudentDeleting;
use App\Models\Attendance;
use App\Models\Enrollment;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class DeleteStudentData
{
    public function handle(StudentDeleting $event) : void
    {
        Attendance::where('student_id', $event->student->id)->delete();
        Enrollment::where('student_id', $event->student->id)->delete();
    }
}
