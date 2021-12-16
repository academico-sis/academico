<?php

namespace App\Models;

use App\Jobs\WatchAttendance;
use App\Mail\PendingAttendanceReminder;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Spatie\Activitylog\Traits\LogsActivity;

/**
 * @mixin IdeHelperAttendance
 */
class Attendance extends Model
{
    use LogsActivity;

    protected $guarded = ['id'];

    protected $with = ['attendance_type'];

    protected static $logUnguarded = true;

    protected static function boot()
    {
        parent::boot();

        // when an attendance record is added, we check if this is an absence
        static::saved(function (self $attendance) {
            if ($attendance->attendance_type_id == 4) { // todo move to configurable settings
                // Log::info('Absence marked for student '.$attendance->student->name);
                // will check the record again and send a notification if it hasn't changed
                WatchAttendance::dispatch($attendance)
                ->delay(now()); // todo move to configurable settings
            }
        });
    }

    /** RELATIONS */
    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    /** Additional data = contact information associated to the student */
    public function contacts()
    {
        return $this->hasMany(Contact::class, 'student_id', 'id');
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

    public function getStudentNameAttribute() : string
    {
        return $this->student->name ?? '';
    }
}
