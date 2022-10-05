<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

/**
 * @mixin IdeHelperCourseTime
 */
class CourseTime extends Model
{
    use LogsActivity;
    use CrudTrait;

    public $timestamps = false;

    protected $guarded = ['id'];

    protected static function boot()
    {
        parent::boot();

        // when a coursetime is added, we should create corresponding events
        static::created(function ($coursetime) {
            $coursetime->createEvents();
        });

        static::updated(function ($coursetime) {
            $coursetime->events()->delete();
            $coursetime->createEvents();
        });

        // when a coursetime is deleted, we should delete all associated future events
        static::deleted(function ($coursetime) {
            $coursetime->events()->delete();
            // todo delete only future events
        });
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()->logUnguarded();
    }

    public function identifiableAttribute()
    {
        return $this->day;
    }

    public function createEvents()
    {
        $today = Carbon::parse($this->course->start_date)->startOfDay();
        $end = Carbon::parse($this->course->end_date)->endOfDay();

        $teacher = $this->course->teacher;

        // for each day in the course period span
        while ($today <= $end) {

                // if today is a day of class, create the event
            if ($this->day == $today->format('w') && (!$teacher || ! $teacher->leaves->contains('date', $today->toDateString()))) {
                Event::create([
                    'course_id' => $this->course->id,
                    'teacher_id' => $this->course->teacher_id,
                    'room_id' => $this->course->room_id,
                    'start' => $today->setTimeFromTimeString($this->start)->toDateTimeString(),
                    'end' => $today->setTimeFromTimeString($this->end)->toDateTimeString(),
                    'name' => $this->course->name,
                    'course_time_id' => $this->id,
                    'exempt_attendance' => $this->course->exempt_attendance,
                ]);
            }
            $today->addDay();
        }
    }

    /** events = class sessions.
     * An Event is related to the CourseTime that generated it. This is needed to update related events when updating the course schedule.
     */
    public function events()
    {
        return $this->hasMany(Event::class);
    }

    public function course()
    {
        return $this->belongsTo(Course::class);
    }
}
