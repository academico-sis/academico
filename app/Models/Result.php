<?php

namespace App\Models;

use App\Mail\ResultNotification;
use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Mail;
use Spatie\Activitylog\Traits\LogsActivity;

/**
 * App\Models\Result
 *
 * @property int $id
 * @property int $enrollment_id
 * @property int $result_type_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\Spatie\Activitylog\Models\Activity[] $activities
 * @property-read int|null $activities_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Comment[] $comments
 * @property-read int|null $comments_count
 * @property-read \App\Models\Enrollment $enrollment
 * @property-read mixed $course_name
 * @property-read mixed $course_period
 * @property-read mixed $result_type
 * @property-read mixed $student_name
 * @property-read \App\Models\ResultType $result_name
 * @method static \Illuminate\Database\Eloquent\Builder|Result newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Result newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Result query()
 * @method static \Illuminate\Database\Eloquent\Builder|Result whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Result whereEnrollmentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Result whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Result whereResultTypeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Result whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Result extends Model
{
    use CrudTrait;
    use LogsActivity;

    protected $guarded = ['id'];
    protected static $logUnguarded = true;

    protected static function boot()
    {
        parent::boot();

        // when a result is added, send a notification
        static::saved(function (self $result) {
            Mail::to($result->enrollment->student->user->email)
            ->locale($result->enrollment->student->user->locale)
            ->queue(new ResultNotification($result->enrollment->course, $result->enrollment->student->user));
        });
    }

    public function comments()
    {
        return $this->morphMany(Comment::class, 'commentable');
    }

    public function result_name()
    {
        return $this->belongsTo(ResultType::class, 'result_type_id');
    }

    public function getResultTypeAttribute()
    {
        return $this->result_name->name;
    }

    /**
     * A Result is linked to an Enrollment.
     */
    public function enrollment()
    {
        return $this->belongsTo(Enrollment::class);
    }

    public function getStudentNameAttribute()
    {
        return $this->enrollment['student']['firstname'].' '.$this->enrollment['student']['lastname'];
    }

    public function getCourseNameAttribute()
    {
        return $this->enrollment['course']['name'];
    }

    public function getCoursePeriodAttribute()
    {
        return $this->enrollment['course']['period']['name'];
    }
}
