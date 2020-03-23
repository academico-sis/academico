<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Leave;
use App\Models\Teacher;
use Illuminate\Support\Facades\Gate;

class TeacherCalendarController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:calendars.view', ['except' => 'show']);
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Do not fetch all events but only those closest to current date. TODO optimize this.
        $events = Event::where('start', '>', (Carbon::now()->subDays(90)))->where('end', '<', (Carbon::now()->addDays(90)))->orderBy('id', 'desc')->get()->get()->toArray();

        $teachers = Teacher::with('user')->get()->toArray();

        $teachers = array_map(function ($teacher) {
            return [
                'id' => $teacher['id'],
                'title' => $teacher['user']['firstname'],
            ];
        }, $teachers);

        array_push($teachers, ['id' => 'tbd', 'title' => 'Unassigned']);

        $events = array_map(function ($event) {
            return [
                'title' => $event['name'],
                'resourceId' => $event['teacher_id'],
                'start' => $event['start'],
                'end' => $event['end'],
                'groupId' => $event['course_id'],
                'backgroundColor' => '#'.substr(md5($event['course_id']), 0, 6),
                'borderColor' => '#'.substr(md5($event['course_id']), 0, 6),
            ];
        }, $events);

        $unassigned_events = Event::where('teacher_id', null)->get()->toArray();

        $unassigned_events = array_map(function ($event) {
            return [
                'title' => $event['name'],
                'resourceId' => 'tbd',
                'start' => $event['start'],
                'end' => $event['end'],
                'groupId' => $event['course_id'],
                'backgroundColor' => '#'.substr(md5($event['course_id']), 0, 6),
                'borderColor' => '#'.substr(md5($event['course_id']), 0, 6),
            ];
        }, $unassigned_events);

        $leaves = Leave::orderBy('date', 'desc')->limit(10000)->get()->toArray();

        $leaves = array_map(function ($event) {
            return [
                'title' => $event->leaveType->name ?? 'ABS', // todo fix
                'resourceId' => $event['teacher_id'],
                'start' => $event['date'],
                'allDay' => true,
                'resourceEditable' => false,
            ];
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
        // If the user is not allowed to perform this action
        if (Gate::forUser(backpack_user())->denies('view-teacher-calendar', $teacher)) {
            abort(403);
        }

        $events = $teacher->events->toArray();
        $events = array_map(function ($event) {
            return [
                'title' => $event['name'],
                'start' => $event['start'],
                'end' => $event['end'],
            ];
        }, $events);

        $leaves = $teacher->leaves->toArray();
        $leaves = array_map(function ($event) {
            return [
                'title' => $event->leaveType->name ?? 'vacances',  // todo fix
                'start' => $event['date'],
                'allDay' => true,
            ];
        }, $leaves);

        return view('calendars.simple', [
            'events' => $events,
            'resource' => $teacher,
            'leaves' => $leaves,
        ]);
    }

}
