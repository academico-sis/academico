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

    public ?string $email;

    public string $firstname;

    public string $lastname;

    public function __construct(Student|Contact $user, public $listId)
    {
        $this->email = $user->email;
        $this->firstname = $user->firstname;
        $this->lastname = $user->lastname;
    }
}
