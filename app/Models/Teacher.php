<?php

namespace App\Models;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Course;
use Backpack\CRUD\CrudTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Teacher extends Model
{
    use CrudTrait;
    use SoftDeletes;
  
    public $timestamps = true;
    protected $guarded = ['id'];
    protected $with = ['user'];
    
    /** relations */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }


    /** attributes */
    public function getFirstnameAttribute()
    {
        return $this->user->firstname;
    }

    public function getLastnameAttribute()
    {
        return $this->user->lastname;
    }

    public function getNameAttribute()
    {
        return $this->firstname . ' ' . $this->lastname;
    }

    public function getEmailAttribute()
    {
        return $this->user->email;
    }


    public function period_courses(Period $period)
    {
        return $this->courses()
            ->where('period_id', $period->id)
            ->withCount('children')
            ->withCount('enrollments')
            ->get()
            ->where('children_count', 0);
    }

    public function period_events(Period $period)
    {
        return $this->events
            ->where('start', '>=', Carbon::parse($period->start)->setTime(0, 0, 0)->toDateTimeString())
            ->where('end', '<=', Carbon::parse($period->end)->setTime(23, 59, 0)->toDateTimeString());
    }

    public function period_remote_events(Period $period)
    {
        return $this->remote_events
            ->where('period_id', $period->id);
    }

    public function events()
    {
        return $this->hasMany(Event::class);
    }

    public function remote_events()
    {
        return $this->hasMany(RemoteEvent::class);
    }

    public function leaves()
    {
        return $this->hasMany(Leave::class)->with('leaveType');
    }

    public function courses()
    {
        return $this->hasMany(Course::class);
    }
    

    public function period_planned_hours(Period $period)
    {
        return $this
            ->events
            ->where('start', '>=', Carbon::parse($period->start)->setTime(0, 0, 0)->toDateTimeString())
            ->where('end', '<=', Carbon::parse($period->end)->setTime(23, 59, 0)->toDateTimeString())
            ->sum('length');
    }

    public function period_worked_hours(Period $period)
    {
        return $this
            ->events
            ->where('start', '>=', Carbon::parse($period->start)->setTime(0, 0, 0)->toDateTimeString())
            ->where('end', '<=', Carbon::parse($period->end)->setTime(23, 59, 0)->toDateTimeString())
            ->where('end', '<=', (new Carbon)->toDateTimeString())
            ->sum('length');
    }

    public function periodRemoteHours(Period $period)
    {
        return $this
            ->remote_events
            ->where('period_id', $period->id)
            ->sum('worked_hours');
    }

    public function period_max_hours(Period $period)
    {
        $dailyHours = $this->max_week_hours / 7;

        $period_days = Carbon::parse($period->start)->diffInDays(Carbon::parse($period->end));
        
        $teacherLeaves = $this->leaves()
            ->where('date', '>=', Carbon::parse($period->start)->toDateString())
            ->where('date', '<=', Carbon::parse($period->end)->toDateString())
            ->count();

        return round($dailyHours * ($period_days - $teacherLeaves), 0);
    }


    public function getEventsWithPendingAttendanceAttribute()
    {
        return $this->events()
            ->where(function($query) {
                $query->where('exempt_attendance', '!=', true);
                $query->where('exempt_attendance', '!=', 1);
                $query->orWhereNull('exempt_attendance');
            })
            ->where('course_id', '!=', null)
            ->withCount('attendance')
            ->with('course')
            ->where('start', '<', Carbon::now(env('COURSES_TIMEZONE'))->addMinutes(20)->toDateTimeString())
            ->get()
            ->filter(function ($event, $key) {
                return $event->course->enrollments_count != $event->attendance_count; // todo improve check
            })
            ->values(); // reset the array keys to start at 0
    }
}
