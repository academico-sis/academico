<?php

namespace App\Models;

use App\Events\AttendanceSavedEvent;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Attendance extends Model
{
    use LogsActivity;

    protected $guarded = ['id'];

    protected $with = ['attendanceType'];

    protected $dispatchesEvents = [
        'saved' => AttendanceSavedEvent::class,
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()->logUnguarded();
    }

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    public function attendanceType()
    {
        return $this->belongsTo(AttendanceType::class);
    }

    /**
     * get absences count per student
     * this is useful for monitoring the absences.
     */
    public function get_absence_count_per_student(Period $period)
    {
        // return attendance records for period
        $coursesIds = $period->courses->pluck('id');
        $eventsIds = Event::whereIn('course_id', $coursesIds)->pluck('id');

        return self::with('event.course')->with('student')->whereIn('event_id', $eventsIds)->whereIn('attendance_type_id', [3, 4])->get()->groupBy('student_id');
    }

    public function getStudentNameAttribute(): string
    {
        return $this->student->name ?? '';
    }
}
