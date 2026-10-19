<?php

namespace App\Models;

use App\Events\ResultSavedEvent;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;

class Result extends Model
{
    use HasFactory, LogsActivity;

    protected $guarded = ['id'];

    protected $dispatchesEvents = [
        'saved' => ResultSavedEvent::class,
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()->logUnguarded();
    }

    public function comments(): MorphMany
    {
        return $this->morphMany(Comment::class, 'commentable');
    }

    public function result_name(): BelongsTo
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
    public function enrollment(): BelongsTo
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
