<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Leave;
use App\Models\Teacher;
use Carbon\Carbon;
use Illuminate\Support\Facades\Gate;

class TeacherCalendarController extends Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->middleware('permission:calendars.view', ['except' => 'show']);
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Do not fetch all events but only those closest to current date.
        $events = Event::with('course')
            ->where('start', '>', Carbon::now()->subDays(30))->where('end', '<', Carbon::now()->addDays(30))->orderBy('id', 'desc') // TODO optimize this.
            ->get()
            ->map(fn ($event) => [
                'title' => $event->name ?? '',
                'resourceId' => $event->teacher_id,
                'start' => $event->start,
                'end' => $event->end,
                'groupId' => $event->course_id,
                'backgroundColor' => $event->color,
                'borderColor' => $event->color,
            ]);

        $teachers = Teacher::all()->toArray();

        $teachers = array_map(fn ($teacher) => [
            'id' => $teacher['id'],
            'title' => $teacher['name'] ?? '',
        ], $teachers);

        $teachers[] = ['id' => 'tbd', 'title' => 'Unassigned',];

        $unassigned_events = Event::unassigned()->get()->map(fn ($event) => [
            'title' => $event->name ?? '',
            'resourceId' => 'tbd',
            'start' => $event->start,
            'end' => $event->end,
            'groupId' => $event->course_id,
            'backgroundColor' => $event->color,
            'borderColor' => $event->color,
        ]);

        $leaves = Leave::orderBy('date', 'desc')->limit(10000)->get()->map(fn ($event) => [
            'title' => $event->leaveType->name ?? 'ABS',
            // todo fix
            'resourceId' => $event['teacher_id'],
            'start' => $event['date'],
            'allDay' => true,
            'resourceEditable' => false,
        ]);

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

        $events = $teacher->events->map(fn ($event) => [
            'title' => $event['name'],
            'start' => $event['start'],
            'end' => $event['end'],
            'backgroundColor' => $event['color'],
            'borderColor' => $event['color'],
        ]);

        $leaves = $teacher->leaves->map(fn ($event) => [
            'title' => $event->leaveType->name ?? 'vacances',
            // todo fix
            'start' => $event['date'],
            'allDay' => true,
        ]);

        return view('calendars.simple', [
            'events' => $events,
            'resource' => $teacher,
            'leaves' => $leaves,
        ]);
    }
}
