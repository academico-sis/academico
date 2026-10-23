<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;

class Event extends Model
{
    use HasFactory, LogsActivity;

    protected static function boot()
    {
        parent::boot();

        // a teacher on leave cannot teach: the event waits for a substitute
        static::saving(function ($event) {
            if ($event->teacher_id !== null && Teacher::find($event->teacher_id)?->isOnLeave($event->start)) {
                $event->teacher_id = null;
            }
        });
    }

    public $timestamps = true;

    protected $guarded = ['id'];

    protected $with = ['course'];

    protected $appends = ['length'];

    protected function casts(): array
    {
        return [
            'start' => 'datetime',
            'end' => 'datetime',
        ];
    }

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

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class)->withCount('enrollments');
    }

    public function enrollments()
    {
        return $this->course->enrollments();
    }

    public function attendance(): HasMany
    {
        return $this->hasMany(Attendance::class);
    }

    public function teacher(): BelongsTo
    {
        return $this->belongsTo(Teacher::class)->withTrashed();
    }

    public function room(): BelongsTo
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
        return Carbon::parse($this->start)->diffInSeconds(Carbon::parse($this->end)) / 3600;
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
        return round(Carbon::parse($this->start)->diffInMinutes(Carbon::parse($this->end)) / 60, 2);
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
