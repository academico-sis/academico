<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Backpack\CRUD\CrudTrait;

class Result extends Model
{
    use CrudTrait;
    protected $guarded = ['id'];


    public function comments()
    {
        return $this->morphMany('App\Models\Comment', 'commentable');
    }

    public function result_name()
    {
        return $this->belongsTo('App\Models\ResultType', 'result_type_id');
    }

    /**
     * A Result is linked to an Enrollment
     * 
     */
    public function enrollment()
    {
        return $this->belongsTo('App\Models\Enrollment');
    }

    public function getStudentNameAttribute()
    {
        return $this->enrollment['student_data']['firstname'] . ' '. $this->enrollment['student_data']['lastname'];
    }

    public function getCourseNameAttribute()
    {
        return $this->enrollment['course_data']['name'];
    }

    public function getCoursePeriodAttribute()
    {
        return $this->enrollment['course_data']['period']['name'];
    }
    
}
