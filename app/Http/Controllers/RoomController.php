<?php

namespace App\Http\Controllers;

use App\Models\Room;
use App\Models\Event;

use Illuminate\Http\Request;

class RoomController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $events = Event::all()->toArray(); // todo only get last xxx events
        $rooms = Room::all()->toArray();

        $rooms = array_map(function($room) {
            return array(
                'id' => $room['id'],
                'title' => $room['name'],
            );
        }, $rooms);

        $events = array_map(function($event) {
            return array(
                'title' => $event['name'],
                'resourceId' => $event['room_id'],
                'start' => $event['start'],
                'end' => $event['end']
            );
        }, $events);

        return view('calendars.overview', [
            'events' => $events,
            'ressources' => $rooms,
        ]);
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
