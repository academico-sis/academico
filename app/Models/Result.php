<?php

namespace App\Models;

use App\Models\Comment;
use App\Models\Enrollment;
use App\Models\ResultType;
use Backpack\CRUD\CrudTrait;
use Illuminate\Database\Eloquent\Model;

class Result extends Model
{
    use CrudTrait;
    protected $guarded = ['id'];


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
     * A Result is linked to an Enrollment
     * 
     */
    public function enrollment()
    {
        return $this->belongsTo(Enrollment::class);
    }

    public function getStudentNameAttribute()
    {
        return $this->enrollment['student']['firstname'] . ' '. $this->enrollment['student']['lastname'];
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
