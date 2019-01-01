<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Course;

class Student extends Model
{
    protected $table = 'users';
    use SoftDeletes;
    

    public static function all($columns = ['*'])
    {
        return \App\User::role('student')->get();
    }

    /**
     * enroll the student in a course.
     * If the course has any children, we also enroll the student in the children courses.
     */
    public function enroll(Course $course)
    {
        // avoid duplicates by retrieving an eventual existing enrollment for the same course
        $enrollment = Enrollment::firstOrNew([
            'user_id' =>  $this->id,
            'course_id' => $course->id
        ]);
        $enrollment->responsible_id = backpack_user()->id;
        $enrollment->save();
        
        dump($enrollment);
        // if the course has children, enroll in children as well.
        if($course->children_count > 0)
        {
            foreach($course->children as $children_course)
            {
                $child_enrollment = Enrollment::firstOrNew([
                    'user_id' =>  $this->id,
                    'course_id' => $children_course->id,
                    'parent_id' => $enrollment->id
                ]);
                $child_enrollment->responsible_id = backpack_user()->id;
                $child_enrollment->save();
            }
        }

        return $enrollment->id;
    }

    public function additional_data()
    {
        return $this->hasMany('\App\Models\UserData', 'user_id')->whereNull('relationship_id')->orWhereNotIn('relationship_id', [1]);
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
        return $this->hasMany('\App\Models\Enrollment', 'user_id')
            ->with('course_data');
    }

}
