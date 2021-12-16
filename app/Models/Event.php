<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Prologue\Alerts\Facades\Alert;
use Spatie\Activitylog\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Builder;

/**
 * App\Models\Event
 *
 * @property int $id
 * @property int|null $course_id
 * @property int|null $teacher_id
 * @property int|null $room_id
 * @property string $start
 * @property string $end
 * @property string $name
 * @property int|null $course_time_id
 * @property int|null $exempt_attendance
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\Spatie\Activitylog\Models\Activity[] $activities
 * @property-read int|null $activities_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Attendance[] $attendance
 * @property-read int|null $attendance_count
 * @property-read \App\Models\Course|null $course
 * @property-read \App\Models\CourseTime $coursetime
 * @property-read mixed $color
 * @property-read mixed $end_time
 * @property-read mixed $event_length
 * @property-read mixed $formatted_date
 * @property-read mixed $length
 * @property-read mixed $period
 * @property-read mixed $short_date
 * @property-read mixed $start_time
 * @property-read mixed $unassigned_teacher
 * @property-read mixed $volume
 * @property-read \App\Models\Room|null $room
 * @property-read \App\Models\Teacher|null $teacher
 * @method static Builder|Event newModelQuery()
 * @method static Builder|Event newQuery()
 * @method static Builder|Event query()
 * @method static Builder|Event whereCourseId($value)
 * @method static Builder|Event whereCourseTimeId($value)
 * @method static Builder|Event whereCreatedAt($value)
 * @method static Builder|Event whereEnd($value)
 * @method static Builder|Event whereExemptAttendance($value)
 * @method static Builder|Event whereId($value)
 * @method static Builder|Event whereName($value)
 * @method static Builder|Event whereRoomId($value)
 * @method static Builder|Event whereStart($value)
 * @method static Builder|Event whereTeacherId($value)
 * @method static Builder|Event whereUpdatedAt($value)
 * @mixin \Eloquent
 */
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

    /*
    |--------------------------------------------------------------------------
    | GLOBAL VARIABLES
    |--------------------------------------------------------------------------
    */
    // protected $primaryKey = 'id';
    public $timestamps = true;
    protected $guarded = ['id'];
    //protected $fillable = [];
    // protected $hidden = [];
    // protected $dates = [];
    protected $appends = ['length'];
    //protected $with = ['course'];
    protected static $logUnguarded = true;

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

    public function coursetime()
    {
        return $this->belongsTo(CourseTime::class);
    }

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

    public function getPeriodAttribute()
    {
        return $this->course->period_id;
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
        return $this->course->color ?? ('#'.substr(md5($this->course_id ?? '0'), 0, 6));
    }

    /*
    |--------------------------------------------------------------------------
    | MUTATORS
    |--------------------------------------------------------------------------
    */
}
