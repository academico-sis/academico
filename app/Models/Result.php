<?php

namespace App\Models;

use App\Events\ResultSavedEvent;
use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Model;
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

    protected $dispatchesEvents = [
        'saved' => ResultSavedEvent::class,
    ];
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()->logUnguarded();
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
