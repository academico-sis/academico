<?php

namespace App\Events;

use App\Models\Leave;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class LeaveUpdated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(public Leave $leave)
    {
        //
    }
}
