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

    public function course_data()
    {
        return $this->hasOne('\App\Models\Course', 'id', 'course_id');
    }
    
    public function getStudentNameAttribute()
    {
        return $this->student_data['name'];
    }

    public function getStudentIdAttribute()
    {
        return $this->student_data['id'];
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
        return $this->morphMany('App\Models\Comment', 'commentable'); // todo migrate this + old prefactura comments.
    }

    public function result()
    {
        return $this->hasOne('App\Models\Result'); // todo migrate this.
    }

    public function getDateAttribute()
    {
        return Carbon::parse($this->created_at, 'UTC')->toFormattedDateString();
    }

    public function getEnrollmentStatusAttribute()
    {
        return $this->status; // todo add table to translate numbers to text.
    }
}
