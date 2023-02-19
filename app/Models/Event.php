<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Prologue\Alerts\Facades\Alert;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Event extends Model
{
    use CrudTrait;
    use LogsActivity;

    protected static function boot()
    {
        parent::boot();

        // before adding an event, we check that the teacher is available
        static::saving(function ($event) {
            $teacher = Teacher::find($event->teacher_id);
            // if the teacher is on leave on the day of the event
            if ($event->teacher_id !== null && $teacher) {
                if ($teacher->leaves->contains('date', Carbon::parse($event->start)->toDateString())) {
                    // detach the teacher from the event
                    $event->teacher_id = null;
                    Alert::warning(__('The selected teacher is not available on this date'))->flash();
                }
            }
        });
    }

    public $timestamps = true;

    protected $guarded = ['id'];

    protected $with = ['course'];

    protected $appends = ['length'];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()->logUnguarded();
    }

    /*
    |--------------------------------------------------------------------------
    | FUNCTIONS
    |--------------------------------------------------------------------------
    */

    /*
    |--------------------------------------------------------------------------
    | RELATIONS
    |--------------------------------------------------------------------------
    */

    public function course()
    {
        return $this->belongsTo(Course::class)->withCount('enrollments');
    }

    public function enrollments()
    {
        return $this->course->enrollments();
    }

    public function attendance()
    {
        return $this->hasMany(Attendance::class);
    }

    public function teacher()
    {
        return $this->belongsTo(Teacher::class)->withTrashed();
    }

    public function room()
    {
        return $this->belongsTo(Room::class)->withTrashed();
    }

    /*
    |--------------------------------------------------------------------------
    | SCOPES
    |--------------------------------------------------------------------------
    */

    public function scopeUnassigned($query)
    {
        return $query->whereNull('teacher_id');
    }

    /*
    |--------------------------------------------------------------------------
    | ACCESORS
    |--------------------------------------------------------------------------
    */

    public function getLengthAttribute()
    {
        return Carbon::parse($this->end)->diffInSeconds(Carbon::parse($this->start)) / 3600;
    }

    public function getVolumeAttribute()
    {
        return Carbon::parse($this->start)->diffInMinutes(Carbon::parse($this->end)) / 60;
    }

    public function getAttendanceCountAttribute()
    {
        return $this->attendance->count();
    }

    public function getFormattedDateAttribute()
    {
        return Carbon::parse($this->start)->toFormattedDateString();
    }

    public function getStartTimeAttribute()
    {
        return Carbon::parse($this->start)->toTimeString();
    }

    public function getEndTimeAttribute()
    {
        return Carbon::parse($this->end)->toTimeString();
    }

    public function getEventLengthAttribute()
    {
        return round(Carbon::parse($this->end)->diffInMinutes(Carbon::parse($this->start)) / 60, 2);
    }

    public function getShortDateAttribute()
    {
        return Carbon::parse($this->start)->day.'/'.Carbon::parse($this->start)->month;
    }

    public function getColorAttribute()
    {
        return $this?->course->color ?? ('#'.substr(md5($this->course_id ?? '0'), 0, 6));
    }
}
