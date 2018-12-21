<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\SoftDeletes;

class Enrollment extends Model
{

    use SoftDeletes;

    public static function show(Course $course)
    {
        return Enrollment::where('course_id', $course->id)
        ->with('student_data')
        ->get();
    }

    public function student_data()
    {
        return $this->hasOne('\App\User', 'id', 'user_id');
    }
    
    public function getStudentNameAttribute()
    {
        return $this->student_data['name'];
    }

    public function getStudentAgeAttribute()
    {
        return Carbon::createFromFormat('Y-m-d', $this->student_data['birthdate'])->age;
    }

    public function getStudentBirthdateAttribute()
    {
        return $this->student_data['birthdate'];
    }

    public function getStudentEmailAttribute()
    {
        return $this->student_data['email'];
    }

    public function comments()
    {
        return $this->morphMany('App\Models\Comment', 'commentable');
    }
}
