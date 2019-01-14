<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Course;
use Carbon\Carbon;

class Attendance extends Model
{
    public function student_data()
    {
        return $this->belongsTo('App\Models\User', 'user_id');
    }

    public function event()
    {
        return $this->belongsTo('App\Models\Event');
    }

    // get 'absent' attendance records for the period ; grouped by student
    public function get_absence_count(Period $period)
    {
        return Attendance::selectRaw('
            count(*) AS count,
            user_id,
            users.firstname as firstname,
            users.lastname as lastname,
            courses.name as course_name')
        ->join('events', 'events.id', '=', 'attendances.event_id')
        ->join('courses', 'courses.id', '=', 'events.course_id')
        ->join('users', 'attendances.user_id', '=', 'users.id')
        ->where('courses.period_id', $period->id)
        ->where('attendances.attendance_type_id', 4) // todo make this configurable
        ->groupBy('course_name', 'user_id')
        ->orderBy('count', 'DESC')
        ->get();
    }

    // return events for which the attendance records do not match the course student count
    // todo optimize this method
    public function get_pending_attendance(Period $period)
    {
        // get all events to check
        $events = Event::where('exempt_attendance', '!=', true)
        ->where('course_id', '!=', null)
        ->with('attendance')
        ->with('course.enrollments')
        ->where('start', '<', (new Carbon)->toDateTimeString()) // todo check timezones
        ->whereHas('course', function ($query) use ($period) {
            $query->where('period_id', '=', $period->id);
        })
        ->get();

        
        $pending_events = [];

        foreach ($events as $event)
        {
            // if the attendance record count do not match the enrollment count, push the event to array
            $pending_attendance = $event->course->enrollments->count() - $event->attendance->count();

            if ($pending_attendance > 0)
            {
                $pending_events[$event->id]['event'] = $event->name;
                $pending_events[$event->id]['event_date'] = $event->start;
                $pending_events[$event->id]['teacher'] = $event->teacher->name;
                $pending_events[$event->id]['pending'] = $pending_attendance;
            }

    }

    return $pending_events;
}
}
