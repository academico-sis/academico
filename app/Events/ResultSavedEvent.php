<?php

namespace App\Events;

use App\Models\Result;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ResultSavedEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(public Result $result)
    {
        //
    }
}
