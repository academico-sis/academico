<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Enrollment extends Model
{

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
        return 1;
    }

    public function getStudentBirthdateAttribute()
    {
        return $this->student_data['birthdate'];
    }

    public function getStudentEmailAttribute()
    {
        return $this->student_data['email'];
    }
}
