<?php

namespace App\Events;

use App\Models\Teacher;
use App\Models\User;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class TeacherUpdated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public User $user;

    public function __construct(public Teacher $teacher) {
        $this->user = $teacher->user;
    }
}
