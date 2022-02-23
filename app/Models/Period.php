<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

/**
 * @mixin IdeHelperPeriod
 */
class Period extends Model
{
    use CrudTrait;
    use LogsActivity;

    public $timestamps = false;

    protected $fillable = ['name', 'year_id', 'start', 'end'];

    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope('order', function (Builder $builder) {
            $builder->orderBy('year_id')->orderBy('order')->orderBy('id');
        });
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()->logUnguarded();
    }

    /**
     * Return the current period to be used as a default system-wide.
     * First look in Config DB table; otherwise select current or closest next period.
     */
    public static function get_default_period()
    {
        $configPeriod = Config::where('name', 'current_period');

        if ($configPeriod->exists()) {
            $currentPeriodId = $configPeriod->first()->value;

            if (self::where('id', $currentPeriodId)->count() > 0) {
                return self::find($currentPeriodId);
            } else {
                return self::where('end', '>=', date('Y-m-d'))->first();
            }
        }

        return self::first();
    }

    /**
     * Return the period to preselect for all enrollment-related methods.
     */
    public static function get_enrollments_period()
    {
        $selected_period = Config::where('name', 'default_enrollment_period')->first()->value;

        if (self::where('id', $selected_period)->count() > 0) {
            return self::find($selected_period);
        } else {
            // if the current period ends within 15 days, switch to the next one
            $default_period = self::get_default_period();

            // the number of days between the end and today is 2x less than the number of days between start and end
            if (Carbon::parse($default_period->end)->diffInDays() < 0.5 * Carbon::parse($default_period->start)->diffInDays($default_period->end)) {
                return self::where('id', '>', $default_period->id)->orderBy('id')->first();
            } else {
                return $default_period;
            }
        }
    }

    public function enrollments()
    {
        return $this->hasManyThrough(Enrollment::class, Course::class)
            ->with('course');
    }

    public function courses()
    {
        return $this->hasMany(Course::class);
    }

    public function year()
    {
        return $this->belongsTo(Year::class);
    }

    /** returns only pending or paid enrollments, without the child enrollments */
    public function real_enrollments()
    {
        return $this->hasManyThrough(Enrollment::class, Course::class)
        ->whereIn('status_id', ['1', '2']) // pending or paid
        ->where('parent_id', null);
    }

    public function previousPeriod()
    {
        $period = self::where('id', '<', $this->id)->orderBy('id', 'desc')->first();

        if (! $period == null) {
            return $period;
        } else {
            return self::first();
        }
    }

    /** Compute the acquisition rate = the part of students from period P-1 who have been kept in period P */
    public function getAcquisitionRateAttribute()
    {
        // get students enrolled in period P-1
        $previous_period_student_ids = $this->previousPeriod()->real_enrollments->pluck('student_id');

        // and students enrolled in period P
        $current_students_ids = $this->real_enrollments->pluck('student_id');

        // students both in period p-1 and period p
        $acquired_students = $previous_period_student_ids->intersect($current_students_ids);

        return number_format((100 * $acquired_students->count()) / max($previous_period_student_ids->count(), 1), 1).'%';
    }

    public function newStudents()
    {
        // get students IDs enrolled in all previous periods
        $previous_period_student_ids = DB::table('enrollments')->join('courses', 'enrollments.course_id', 'courses.id')->where('period_id', '<', $this->id)->pluck('enrollments.student_id');

        // and students enrolled in period P
        $current_students_ids = $this->real_enrollments->unique('student_id');

        // students in period P who have never been enrolled in previous periods
        return $current_students_ids->whereNotIn('student_id', $previous_period_student_ids);
    }

    public function getTakingsAttribute()
    {
        return $this->real_enrollments->sum('total_paid_price');
    }

    /** TODO this method can be furthered optimized and refactored */
    public function getCoursesWithPendingAttendanceAttribute()
    {
        // get all courses for period and preload relations
        $courses = $this->courses()->where(function ($query) {
            $query->where('exempt_attendance', '!=', true);
            $query->where('exempt_attendance', '!=', 1);
            $query->orWhereNull('exempt_attendance');
        })->whereHas('events')->with('attendance')->whereNotNull('exempt_attendance')->get();
        $coursesWithMissingAttendanceCount = 0;

        // loop through all courses and get the number of events with incomplete attendance
        foreach ($courses as $course) {
            foreach ($course->eventsWithExpectedAttendance as $event) {
                foreach ($course->enrollments as $enrollment) {
                    // if a student has no attendance record for the class (event)
                    $hasNotAttended = $course->attendance->where('student_id', $enrollment->student_id)
                     ->where('event_id', $event->id)
                     ->isEmpty();

                    // count one and break loop
                    if ($hasNotAttended) {
                        $coursesWithMissingAttendanceCount++;
                        break 2;
                    }
                }
            }
        }

        // sort by number of events with missing attendance
        return $coursesWithMissingAttendanceCount;
    }
}
