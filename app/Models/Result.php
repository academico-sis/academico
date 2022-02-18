<?php

namespace App\Models;

use App\Mail\ResultNotification;
use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Mail;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

/**
 * @mixin IdeHelperResult
 */
class Result extends Model
{
    use CrudTrait;
    use LogsActivity;

    protected $guarded = ['id'];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()->logUnguarded();
    }

    protected static function boot()
    {
        parent::boot();

        if (config('academico.send_emails_for_results')) {
            // when a result is added, send a notification
            static::saved(function (self $result) {
                Mail::to($result->enrollment->student->user->email)->locale($result->enrollment->student->user->locale)->queue(new ResultNotification($result->enrollment->course, $result->enrollment->student->user));
            });
        }
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
