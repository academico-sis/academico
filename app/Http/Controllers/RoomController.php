<?php

namespace App\Http\Controllers;

use App\Models\Room;
use Illuminate\Http\Request;

class RoomController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }


    /**
     * Display the specified resource.
     */
    public function show(Room $room)
    {
        $events = $room->events->toArray();
        $events = array_map(function($event) {
            return array(
                'title' => $event['name'],
                'start' => $event['start'],
                'end' => $event['end']
            );
        }, $events);

        return view('calendars.simple', [
            'events' => $events,
            'ressource' => $room,
        ]);
    }

}
