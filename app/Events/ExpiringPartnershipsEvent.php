<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ExpiringPartnershipsEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $partners;

    public function __construct($partners)
    {
        $this->partners = $partners;
    }
}
