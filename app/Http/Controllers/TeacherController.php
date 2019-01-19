<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Event;
use Illuminate\Http\Request;

class TeacherController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $events = Event::all()->toArray(); // todo only get last xxx events
        $teachers = User::teacher()->all();

        $teachers = array_map(function($teacher) {
            return array(
                'id' => $teacher['id'],
                'title' => $teacher['name'],
            );
        }, $teachers);

        $events = array_map(function($event) {
            return array(
                'title' => $event['name'],
                'resourceId' => $event['teacher_id'],
                'start' => $event['start'],
                'end' => $event['end'],
                'groupId' => $event['course_id'],
                'backgroundColor' => '#' . substr(md5($event['course_id']), 0, 6),
                'borderColor' => '#' . substr(md5($event['course_id']), 0, 6),
            );
        }, $events);

        //dd($events);
        return view('calendars.overview', [
            'events' => $events,
            'resources' => $teachers,
        ]);
    }

 /**
     * Display the specified resource.
     */
    public function show(User $teacher)
    {
        $events = $teacher->events->toArray();
        $events = array_map(function($event) {
            return array(
                'title' => $event['name'],
                'start' => $event['start'],
                'end' => $event['end']
            );
        }, $events);

        return view('calendars.simple', [
            'events' => $events,
            'resource' => $teacher,
        ]);
    }

}
