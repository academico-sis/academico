<?php

namespace App\Events;

use App\Models\Course;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class CourseUpdated
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    public $course;

    public function __construct(Course $course)
    {
        $this->course = $course;
    }
}
