<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Room;
use Carbon\Carbon;

class RoomController extends Controller
{
    public function __construct()
    {
        $this->middleware(['permission:calendars.view']);
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Do not fetch all events but only those closest to current date. TODO optimize this.
        $events = Event::with('course')->where('start', '>', (Carbon::now()->subDays(90)))->where('end', '<', (Carbon::now()->addDays(90)))->orderBy('id', 'desc')->get()->toArray();

        $rooms = Room::all()->toArray();

        $rooms = array_map(function ($room) {
            return [
                'id' => $room['id'],
                'title' => $room['name'],
            ];
        }, $rooms);

        array_push($rooms, ['id' => 'tbd', 'title' => 'Unassigned']);

        $events = array_map(function ($event) {
            return [
                'title' => $event['name'],
                'resourceId' => $event['room_id'],
                'start' => $event['start'],
                'end' => $event['end'],
                'groupId' => $event['course_id'],
                'backgroundColor' => $event['course']['color'] ?? '#'.substr(md5($event['course_id'] ?? '0'), 0, 6),
                'borderColor' => $event['course']['color'] ?? '#'.substr(md5($event['course_id'] ?? '0'), 0, 6),
            ];
        }, $events);

        $unassigned_events = Event::where('room_id', null)->get()->toArray();

        $unassigned_events = array_map(function ($event) {
            return [
                'title' => $event['name'],
                'resourceId' => 'tbd',
                'start' => $event['start'],
                'end' => $event['end'],
                'groupId' => $event['course_id'],
                'backgroundColor' => $event['course']['color'] ?? '#'.substr(md5($event['course_id'] ?? '0'), 0, 6),
                'borderColor' => $event['course']['color'] ?? '#'.substr(md5($event['course_id'] ?? '0'), 0, 6),
            ];
        }, $unassigned_events);

        return view('calendars.overview', [
            'events' => $events,
            'resources' => $rooms,
            'unassigned_events' => $unassigned_events,
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Room $room)
    {
        $events = $room->events->toArray();
        $events = array_map(function ($event) {
            return [
                'title' => $event['name'],
                'start' => $event['start'],
                'end' => $event['end'],
                'backgroundColor' => $event['color'],
                'borderColor' => $event['color'],
            ];
        }, $events);

        return view('calendars.simple', [
            'events' => $events,
            'resource' => $room,
        ]);
    }
}
