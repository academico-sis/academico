<?php

namespace App\Events;

use App\Models\User;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class UserCreated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;


    // we may pass the clear-text password to transmit it to the external API.
    // If left blank, it will not be transmitted.
    public function __construct(public User $user, public ?string $password = null)
    {
        //
    }


}
