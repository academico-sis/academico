<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Backpack\CRUD\CrudTrait;
use Illuminate\Database\Eloquent\SoftDeletes;

class Course extends Model
{
    use CrudTrait;
    use SoftDeletes;

    /*
    |--------------------------------------------------------------------------
    | GLOBAL VARIABLES
    |--------------------------------------------------------------------------
    */

    protected $table = 'courses';
    // protected $primaryKey = 'id';
    public $timestamps = false;
    // protected $guarded = ['id'];
    protected $fillable = [];
    // protected $hidden = [];
    // protected $dates = [];

    /*
    |--------------------------------------------------------------------------
    | FUNCTIONS
    |--------------------------------------------------------------------------
    */
    public function get_all_internal_courses(Period $period)
    {
        return Course::where('period_id', $period->id)
        ->where('campus_id', 1)
        ->with('times')
        ->with('teacher')
        ->with('room')
        ->with('rythm')
        ->with('level')
        ->withCount('enrollments')
        ->get();
    }
    /*
    |--------------------------------------------------------------------------
    | RELATIONS
    |--------------------------------------------------------------------------
    */

    public function times()
    {
        return $this->hasMany('\App\Models\CourseTime', 'course_id');
    }

    public function teacher()
    {
        return $this->hasOne('\App\User', 'id', 'teacher_id');
    }

    public function room()
    {
        return $this->hasOne('\App\Models\Room', 'id', 'room_id');
    }

    public function rythm()
    {
        return $this->hasOne('\App\Models\Rythm', 'id', 'rythm_id');
    }

    public function level()
    {
        return $this->hasOne('\App\Models\Level', 'id', 'level_id');
    }


    /**
     * enrollments
     * 
     * todo filter out cancelled enrollments
     * 
     * or use soft deletes ?
     *
     * @return void
     */
    public function enrollments()
    {
        return $this->hasMany('\App\Models\Enrollment', 'course_id', 'id');
    }
    /*
    |--------------------------------------------------------------------------
    | SCOPES
    |--------------------------------------------------------------------------
    */

    /*
    |--------------------------------------------------------------------------
    | ACCESORS
    |--------------------------------------------------------------------------
    */

    /**
     * getCourseTimesAttribute
     * 
     * todo refactor this
     * todo add times
     *
     * @param mixed $value
     * @return void
     */
    public function getCourseTimesAttribute($value)
    {
        $days = "";

       foreach ($this->times as $time)
       {
            if ($time->day == '1') { $days .= "L"; }
            if ($time->day == '2') { $days .= "M"; }
            if ($time->day == '3') { $days .= "X"; }
            if ($time->day == '4') { $days .= "J"; }
            if ($time->day == '5') { $days .= "V"; }
            if ($time->day == '6') { $days .= "S"; }
            if ($time->day == '0') { $days .= "D"; }
       }

       return $days;
    }

    public function getCourseRoomNameAttribute()
    {
        return strtoupper($this->room['name']);
    }

    public function getCourseLevelNameAttribute()
    {
        return $this->level['name'];
    }

    public function getCourseRythmNameAttribute()
    {
        return strtoupper($this->rythm['name']);
    }
    
    public function getCourseTeacherNameAttribute()
    {
        return strtoupper($this->teacher['name']);
    }

    public function getCourseEnrolledStudents()
    {
        return $this->enrollments->name;
    }

    /*
    |--------------------------------------------------------------------------
    | MUTATORS
    |--------------------------------------------------------------------------
    */
}
