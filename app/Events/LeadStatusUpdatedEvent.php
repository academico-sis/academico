<?php

namespace App\Events;

use App\Models\Contact;
use App\Models\Student;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class LeadStatusUpdatedEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $email;

    public $name;

    public $firstname;

    public $lastname;

    public function __construct(Student|Contact $user, public $listId)
    {
        $this->email = $user->email;
        $this->name = $user->firstname;
        $this->lastname = $user->lastname;
    }
}
