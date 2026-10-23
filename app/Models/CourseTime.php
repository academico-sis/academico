<?php

namespace App\Models;

use App\Services\CourseEventScheduler;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;

class CourseTime extends Model
{
    use HasFactory, LogsActivity;

    public $timestamps = false;

    protected $guarded = ['id'];

    protected static function boot()
    {
        parent::boot();

        // keep the sessions (events) of the course in line with its weekly schedule
        static::created(fn (self $timeSlot) => app(CourseEventScheduler::class)->timeSlotCreated($timeSlot));
        static::updated(fn (self $timeSlot) => app(CourseEventScheduler::class)->timeSlotUpdated($timeSlot));
        static::deleting(fn (self $timeSlot) => app(CourseEventScheduler::class)->timeSlotDeleting($timeSlot));
    }

    /** Times are stored with seconds: '09:00' and '09:00:00' must not be considered as a change */
    protected function start(): Attribute
    {
        return Attribute::make(set: fn ($value) => $value === null ? null : Carbon::parse($value)->format('H:i:s'));
    }

    protected function end(): Attribute
    {
        return Attribute::make(set: fn ($value) => $value === null ? null : Carbon::parse($value)->format('H:i:s'));
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()->logUnguarded();
    }

    /** events = class sessions.
     * An Event is related to the CourseTime that generated it. This is needed to update related events when updating the course schedule.
     */
    public function events(): HasMany
    {
        return $this->hasMany(Event::class);
    }

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }
}
