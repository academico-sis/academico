<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\SoftDeletes;

class Student extends Model
{
    protected $table = 'users';
    use SoftDeletes;
    
    public static function get_all_users()
    {
        //return \App\Models\BackpackUser::role('student')->get();
        return \App\User::all();
    }

    public function enroll(\App\Models\Course $course)
    {
        $enrollment = new \App\Models\Enrollment;
        $enrollment->user_id = $this->id;
        $enrollment->responsible_id = 1;
        $enrollment->course_id = $course->id;
        $enrollment->status = 1;

        $enrollment->save();
    }

    public function invoicable()
    {
        return $this->hasOne('\App\Models\Invoicable', 'student_id');
    }

    public function administrative_comments()
    {
        return $this->morphMany('App\Models\Comment', 'commentable')->where('private', true);
    }

     public function pedagogical_comments()
    {
        return $this->morphMany('App\Models\Comment', 'commentable')->where('private', false);
    }
    
    public function phone()
    {
        return $this->morphMany('App\Models\PhoneNumber', 'phoneable');
    }

    public function getNameAttribute()
    {
        return $this->firstname . ' ' . $this->lastname;
    }

    public function getAgeAttribute()
    {
        return Carbon::createFromFormat('Y-m-d', $this->birthdate)->age;
    }


}
