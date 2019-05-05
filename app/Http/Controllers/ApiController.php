<?php

namespace App\Http\Controllers;

use App\Models\Event;

class ApiController extends Controller
{

public function get_event_attendance(Event $event)
{
    return $event->id;
}


}
