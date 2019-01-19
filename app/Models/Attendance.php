<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Course;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use App\Mail\PendingAttendanceReminder;

use Carbon\Carbon;

class Attendance extends Model
{
    protected $guarded = ['id'];
    
    public function student_data()
    {
        return $this->belongsTo('App\Models\User', 'user_id');
    }

    public function event()
    {
        return $this->belongsTo('App\Models\Event');
    }

    public function remindPendingAttendance()
    {
        $period = $period = Period::get_default_period();
        foreach (User::teacher()->all() as $teacher)
        {
            $events = $this->events_with_pending_attendance($teacher);

            if ($events->count() > 0)
            {
                Mail::to($teacher->email)
                ->queue(new PendingAttendanceReminder($teacher, $events));
            }
        }

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


    private function events_with_pending_attendance(User $teacher)
    {
        return Event::where('exempt_attendance', '!=', true)
        ->where('course_id', '!=', null)
        ->where('teacher_id', $teacher->id)
        ->with('attendance')
        ->with('course.enrollments')
        ->where('start', '<', (new Carbon)->toDateTimeString()) // todo check timezones
        ->get()
        ->filter(function ($event, $key) {
            return $event->course->enrollments->count() > $event->attendance->count();
        });

    }


    // return events for which the attendance records do not match the course student count
    // todo optimize this method to reduce the number of queries
    // todo add         ->orWhereNull('exempt_attendance')
    public function get_pending_attendance(User $teacher = null)
    {

        if(isset($teacher))
        {
            // get all events to check
            $events = $this->events_with_pending_attendance($teacher);
        }

        else
        {
        // get all events to check
        $events = Event::where('exempt_attendance', '!=', true)
        ->where('course_id', '!=', null)
        ->with('attendance')
        ->with('course.enrollments')
        ->where('start', '<', (new Carbon)->toDateTimeString()) // todo check timezones
        ->get();
        }

        
        $pending_events = [];

        foreach ($events as $event)
        {
            // if the attendance record count do not match the enrollment count, push the event to array
            $pending_attendance = $event->course->enrollments->count() - $event->attendance->count();

            if ($pending_attendance > 0)
            {
                $pending_events[$event->id]['event'] = $event->name;
                $pending_events[$event->id]['event_id'] = $event->id;
                $pending_events[$event->id]['event_date'] = Carbon::parse($event->start)->toDateString();
                $pending_events[$event->id]['teacher'] = $event->teacher->name;
                $pending_events[$event->id]['pending'] = $pending_attendance;
            }
    }

    return $pending_events;
}
}
