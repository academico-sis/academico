<?php

namespace App\Models;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Course;
use App\Models\Contact;
use App\Jobs\WatchAttendance;

use App\Models\AttendanceType;
use App\Mail\AbsenceNotification;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use App\Mail\PendingAttendanceReminder;
use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    protected $guarded = ['id'];
    protected $with = ['attendance_type'];
 
    protected static function boot()
    {
        parent::boot();

        // when an attendance record is added, we check if this is an absence
        static::saved(function(Attendance $attendance) {
            if($attendance->attendance_type_id == 4) // todo move to configurable settings
            {
                Log::info('Absence marked for student ' . $attendance->student->name);
                // will check the record again and send a notification if it hasn't changed
                WatchAttendance::dispatch($attendance)
                ->delay(now()); // todo move to configurable settings
            }
        });

    }

    /** RELATIONS */

    public function student()
    {
        return $this->belongsTo(Student::class, 'student_id');
    }

    /** Additional data = contact information associated to the student */
    // todo optimize the references
    public function contacts()
    {
        return $this->hasMany(Contact::class, 'student_id', 'user_id');
    }

    /** event = one instance of the course */
    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    public function attendance_type()
    {
        return $this->belongsTo(AttendanceType::class);
    }


    /** METHODS */

    /** Send email reminders to all teachers who have classes with incomplete attendance records */
    public function remindPendingAttendance()
    {
        $period = Period::get_default_period();
        foreach (Teacher::all() as $teacher)
        {
            $events = $teacher->events_with_pending_attendance
            ->where('start', '<', (Carbon::parse('24 hours ago'))->toDateTimeString());

            if ($events->count() > 0)
            {
                Mail::to($teacher->email)
                ->locale('fr')
                ->queue(new PendingAttendanceReminder($teacher, $events));
            }
        }

    }

    /**
     * get absences count per student
     * this is useful for monitoring the absences
     */
    public function get_absence_count_per_student(Period $period)
    {
        // return attendance records for period
        $coursesIds = $period->courses->pluck('id');
        $eventsIds = Event::whereIn('course_id', $coursesIds)->pluck('id');
        $attendances = Attendance::with('event.course')->with('student')->whereIn('event_id', $eventsIds)->whereIn('attendance_type_id', [3,4])->get()->groupBy('student_id');

        return $attendances;
    }



    /**
     * Return events for which the attendance records do not match the course student count
     * 
     * todo - optimize this method (reduce the number of queries and avoid the foreach loop)
     * but filtering the collection increases the number of DB queries... (why ?)
     * TODO update this method to the new way of counting events with missing attendance
     * 
     */
    public function get_pending_attendance()
    {

        $events = Event::where(function($query) {
            $query->where('exempt_attendance', '!=', true);
            $query->where('exempt_attendance', '!=', 1);
            $query->orWhereNull('exempt_attendance');
        })
        ->where('course_id', '!=', null)
        ->with('attendance')
        ->with('teacher')
        ->with('course.enrollments')
        ->where('start', '<', Carbon::now(env('COURSES_TIMEZONE'))->toDateTimeString())
        ->get();
        
        $pending_events = [];

        foreach ($events as $event)
        {
            // if the attendance record count do not match the enrollment count, push the event to array
            $pending_attendance = $event->course->enrollments->count() - $event->attendance->count();

            if ($pending_attendance != 0)
            {
                $pending_events[$event->id]['event'] = $event->name ?? "";
                $pending_events[$event->id]['event_id'] = $event->id;
                $pending_events[$event->id]['course_id'] = $event->course_id;
                $pending_events[$event->id]['event_date'] = Carbon::parse($event->start)->toDateString();
                $pending_events[$event->id]['teacher'] = $event->teacher->name ?? "";
                $pending_events[$event->id]['pending'] = $pending_attendance ?? "";
            }
    }

    return $pending_events;
}
}
