<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\SoftDeletes;

class Student extends Model
{
    protected $table = 'users';
    use SoftDeletes;
    
    public static function all($columns = ['*'])
    {
        //return \App\Models\BackpackUser::role('student')->get();
        return Student::with('self_data')->get();
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

    public function self_data()
    {
        return $this->hasOne('\App\Models\UserData', 'user_id')->where('relationship_id', 1);
    }

    public function additional_data()
    {
        return $this->hasMany('\App\Models\UserData', 'user_id')->whereNull('relationship_id')->orWhereNotIn('relationship_id', [1]);
    }

    public function getStudentFirstNameAttribute()
    {
        return $this->self_data->firstname;
    }

    public function getStudentLastNameAttribute()
    {
        return $this->self_data->lastname;
    }

    public function getStudentIdnumberAttribute()
    {
        return $this->self_data->idnumber;
    }

    public function getStudentAddressAttribute()
    {
        return $this->self_data->address;
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

    public function enrollments()
    {
        return $this->hasMany('\App\Models\Enrollment', 'user_id');
    }

}
