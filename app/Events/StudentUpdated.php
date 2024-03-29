<?php

namespace App\Events;

use App\Models\Student;
use App\Models\User;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class StudentUpdated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public User $user;

    public function __construct(public Student $student)
    {
        $this->user = $student->user;
    }
}
