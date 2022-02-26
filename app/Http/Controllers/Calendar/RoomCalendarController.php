<?php

namespace App\Http\Controllers\Calendar;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\Room;
use Carbon\Carbon;

class RoomCalendarController extends Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->middleware(['permission:calendars.view']);
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Do not fetch all events but only those closest to current date. TODO optimize this.
        $events = Event::with('course')
            ->where('start', '>', Carbon::now()->subDays(30))
            ->where('end', '<', Carbon::now()->addDays(90))
            ->orderBy('id', 'desc')
            ->get()
            ->map(fn ($event) => [
                'title' => $event->name,
                'resourceId' => $event->room_id,
                'start' => $event->start,
                'end' => $event->end,
                'groupId' => $event->course_id,
                'backgroundColor' => $event->color,
                'borderColor' => $event->color,
            ]);

        $rooms = Room::all()->toArray();

        $rooms = array_map(fn ($room) => [
            'id' => $room['id'],
            'title' => $room['name'],
        ], $rooms);

        $rooms[] = ['id' => 'tbd', 'title' => 'Unassigned',];

        $unassigned_events = Event::with('course')
            ->whereNull('room_id')
            ->get()
            ->map(fn ($event) => [
                'title' => $event->name,
                'resourceId' => 'tbd',
                'start' => $event->start,
                'end' => $event->end,
                'groupId' => $event->course_id,
                'backgroundColor' => $event->color,
                'borderColor' => $event->color,
            ]);

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
        $events = $room->events->map(fn ($event) => [
            'title' => $event->name,
            'start' => $event->start,
            'end' => $event->end,
            'backgroundColor' => $event->color,
            'borderColor' => $event->color,
        ]);

        return view('calendars.simple', [
            'events' => $events,
            'resource' => $room,
        ]);
    }
}
