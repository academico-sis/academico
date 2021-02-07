<?php

namespace App\Events;

use App\Models\Course;
use App\Models\Student;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class EnrollmentUpdated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    // the ids must refer to lms_id (not the local ones)
    public function __construct(public Student $student, public Course $previousCourse, public Course $newCourse)
    {
        //
    }
}
