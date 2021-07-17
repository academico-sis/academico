<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

/**
 * A RemoteEvent represents hours that do not have a specific date/time, but that should be taken into account in the teacher's total for the month or the period
 *
 * @property int $id
 * @property int|null $teacher_id
 * @property string $name
 * @property int $worked_hours
 * @property int|null $period_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int|null $course_id
 * @property-read \Illuminate\Database\Eloquent\Collection|\Spatie\Activitylog\Models\Activity[] $activities
 * @property-read int|null $activities_count
 * @property-read \App\Models\Course|null $course
 * @property-read \App\Models\Period|null $period
 * @property-read \App\Models\Teacher|null $teacher
 * @method static \Illuminate\Database\Eloquent\Builder|RemoteEvent newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|RemoteEvent newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|RemoteEvent query()
 * @method static \Illuminate\Database\Eloquent\Builder|RemoteEvent whereCourseId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RemoteEvent whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RemoteEvent whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RemoteEvent whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RemoteEvent wherePeriodId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RemoteEvent whereTeacherId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RemoteEvent whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RemoteEvent whereWorkedHours($value)
 * @mixin \Eloquent
 */
class RemoteEvent extends Model
{
    use CrudTrait;
    use LogsActivity;

    protected $guarded = ['id'];
    protected static $logUnguarded = true;

    public function teacher()
    {
        return $this->belongsTo(Teacher::class);
    }

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function period()
    {
        return $this->belongsTo(Period::class);
    }

    /** we store the time volume in seconds */
    public function getWorkedHoursAttribute($value)
    {
        return $value / 3600;
    }

    /** we store the time volume in seconds */
    public function setWorkedHoursAttribute($value)
    {
        $this->attributes['worked_hours'] = $value * 3600;
    }
}
