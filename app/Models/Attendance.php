<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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
        // get all events to check (past)
        $events = Event::select('events.id')
        ->join('courses', 'courses.id', '=', 'events.course_id')
        ->where('courses.period_id', $period->id)
        ->where('events.start', '<', 'now()') // todo careful with timezones
        ->get();

        foreach ($events as $event)
        {
            // get the course enrollment count

            // check if the number of attendance records matches the number of students
            $missing = $attendance - $course_enrollments;
            if ($missing > 0) {

            }
        }

        
    }
}
