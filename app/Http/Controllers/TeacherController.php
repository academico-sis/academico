<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Leave;
use App\Models\Teacher;

class TeacherController extends Controller
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
        $events = Event::orderBy('id', 'desc')->limit(10000)->get()->toArray();
        
        $teachers = Teacher::with('user')->get()->toArray();

        $teachers = array_map(function($teacher) {
            return array(
                'id' => $teacher['id'],
                'title' => $teacher['user']['firstname'],
            );
        }, $teachers);

        array_push($teachers, ['id' => 'tbd', 'title' => 'Unassigned']);

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


        $unassigned_events = Event::where('teacher_id', null)->get()->toArray();

        $unassigned_events = array_map(function($event) {
            return array(
                'title' => $event['name'],
                'resourceId' => 'tbd',
                'start' => $event['start'],
                'end' => $event['end'],
                'groupId' => $event['course_id'],
                'backgroundColor' => '#' . substr(md5($event['course_id']), 0, 6),
                'borderColor' => '#' . substr(md5($event['course_id']), 0, 6),
            );
        }, $unassigned_events);

        $leaves = Leave::orderBy('date', 'desc')->limit(10000)->get()->toArray();

        $leaves = array_map(function($event) {
            return array(
                'title' => $event->leaveType->name ?? 'ABS', // todo fix
                'resourceId' => $event['teacher_id'],
                'start' => $event['date'],
                'allDay' => true,
                'resourceEditable' => false,
            );
        }, $leaves);

        return view('calendars.overview', [
            'events' => $events,
            'unassigned_events' => $unassigned_events,
            'resources' => $teachers,
            'leaves' => $leaves,
        ]);
    }

 /**
     * Display the specified resource.
     */
    public function show(Teacher $teacher)
    {
        $events = $teacher->events->toArray();
        $events = array_map(function($event) {
            return array(
                'title' => $event['name'],
                'start' => $event['start'],
                'end' => $event['end']
            );
        }, $events);

        $leaves = $teacher->leaves->toArray();
        $leaves = array_map(function($event) {
            return array(
                'title' => $event->leaveType->name ?? 'vacances',  // todo fix
                'start' => $event['date'],
                'allDay' => true,
            );
        }, $leaves);

        return view('calendars.simple', [
            'events' => $events,
            'resource' => $teacher,
            'leaves' => $leaves,
        ]);
    }

}
