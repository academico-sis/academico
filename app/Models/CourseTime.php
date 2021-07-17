<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

/**
 * App\Models\CourseTime
 *
 * @property int $id
 * @property int $course_id
 * @property int $day
 * @property string $start
 * @property string $end
 * @property-read \Illuminate\Database\Eloquent\Collection|\Spatie\Activitylog\Models\Activity[] $activities
 * @property-read int|null $activities_count
 * @property-read \App\Models\Course $course
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Event[] $events
 * @property-read int|null $events_count
 * @method static \Illuminate\Database\Eloquent\Builder|CourseTime newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|CourseTime newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|CourseTime query()
 * @method static \Illuminate\Database\Eloquent\Builder|CourseTime whereCourseId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CourseTime whereDay($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CourseTime whereEnd($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CourseTime whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CourseTime whereStart($value)
 * @mixin \Eloquent
 */
class CourseTime extends Model
{
    use LogsActivity;

    protected static function boot()
    {
        parent::boot();

        // when a coursetime is added, we should create corresponding events
        static::created(function ($coursetime) {
            $coursetime->create_events();
        });

        // when a coursetime is deleted, we should delete all associated future events
        static::deleted(function ($coursetime) {
            $coursetime->events()->delete();
            // todo delete only future events
        });
    }

    /*
    |--------------------------------------------------------------------------
    | GLOBAL VARIABLES
    |--------------------------------------------------------------------------
    */
    // protected $primaryKey = 'id';
    public $timestamps = false;
    protected $guarded = ['id'];
    // protected $fillable = [];
    // protected $hidden = [];
    // protected $dates = [];
    protected static $logUnguarded = true;

    public function identifiableAttribute()
    {
        // process stuff here
        return $this->day;
    }

    /*
    |--------------------------------------------------------------------------
    | FUNCTIONS
    |--------------------------------------------------------------------------
    */

    public function create_events()
    {
        $today = Carbon::parse($this->course->start_date)->startOfDay();
        $end = Carbon::parse($this->course->end_date)->endOfDay();

        // for each day in the course period span
        while ($today <= $end) {

                // if today is a day of class, create the event
            if ($this->day == $today->format('w')) {
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

    /*
    |--------------------------------------------------------------------------
    | RELATIONS
    |--------------------------------------------------------------------------
    */

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

    /*
    |--------------------------------------------------------------------------
    | SCOPES
    |--------------------------------------------------------------------------
    */

    /*
    |--------------------------------------------------------------------------
    | ACCESORS
    |--------------------------------------------------------------------------
    */

    /*
    |--------------------------------------------------------------------------
    | MUTATORS
    |--------------------------------------------------------------------------
    */
}
