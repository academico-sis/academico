<?php

namespace App\Events;

use App\Models\Student;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class StudentDeleting
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(public Student $student)
    {
        //
    }
}
