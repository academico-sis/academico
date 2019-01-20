<?php

namespace App\Models;

use Carbon\Carbon;
use App\Models\Event;
use App\Models\Enrollment;
use Backpack\CRUD\CrudTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;

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
    protected $guarded = ['id'];
    //protected $fillable = [];
    // protected $hidden = [];
    // protected $dates = [];
    //protected $with = ['enrollments'];
    //protected $append = ['enrollments_count'];


    /*
    |--------------------------------------------------------------------------
    | SCOPES
    |--------------------------------------------------------------------------
    */

    public function scopeParent($query)
    {
        return $query->where('parent_course_id', null);
    }


    /*
    |--------------------------------------------------------------------------
    | FUNCTIONS
    |--------------------------------------------------------------------------
    */

    public static function get_available_courses(Period $period)
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
        return $this->hasMany('App\Models\CourseTime', 'course_id');
    }

    public function events()
    {
        return $this->hasMany('App\Models\Event');
    }

    public function teacher()
    {
        return $this->belongsTo('App\Models\User', 'teacher_id');
    }

    public function campus()
    {
        return $this->belongsTo('App\Models\Campus');
    }

    public function room()
    {
        return $this->belongsTo('App\Models\Room');
    }

    public function rythm()
    {
        return $this->belongsTo('App\Models\Rythm');
    }

    public function level()
    {
        return $this->belongsTo('App\Models\Level');
    }

    public function period()
    {
        return $this->belongsTo('App\Models\Period');
    }

    public function children()
    {
        return $this->hasMany('App\Models\Course', 'parent_course_id');
    }


    /** evaluation methods associated to the course - grades, skill-based evaluation... */
    public function evaluation_type()
    {
        return $this->belongsToMany('App\Models\EvaluationType');
    }

    public function grades()
    {
        return $this->hasMany('App\Models\Grade')->with('student');
    }

    public function grade_type()
    {
        return $this->belongsToMany('App\Models\GradeType');
    }

    public function skills()
    {
        return $this->belongsToMany('App\Models\Skill');
    }

    /**
     * return attendance records associated to the course
     * Since the attendance records are linked to the event, we use a hasManyThrough relation.
     */
    public function attendance()
    {
        return $this->hasManyThrough('App\Models\Attendance', 'App\Models\Event');
    }


    public function enrollments()
    {
        return $this
            ->hasMany('App\Models\Enrollment', 'course_id', 'id')
            ->whereIn('status_id', [1, 2]) // pending or paid enrollments only
            ->with('student_data');
    }


    /*
    |--------------------------------------------------------------------------
    | ACCESORS
    |--------------------------------------------------------------------------
    */

    /**
     * getCourseTimesAttribute
     * todo refactor and/or this method
     */
    public function getCourseTimesAttribute()
    {
        $schedule = "";

       foreach ($this->times->unique('day') as $time)
       {
            if ($time->day == '1') { $schedule .= "L"; }
            if ($time->day == '2') { $schedule .= "M"; }
            if ($time->day == '3') { $schedule .= "X"; }
            if ($time->day == '4') { $schedule .= "J"; }
            if ($time->day == '5') { $schedule .= "V"; }
            if ($time->day == '6') { $schedule .= "S"; }
            if ($time->day == '0') { $schedule .= "D"; }
       }

       $schedule .= " - ";

       foreach ($this->times->unique('start') as $time)
       {
            $schedule .= Carbon::parse($time->start)->format('g:i') . ' - ' . Carbon::parse($time->end)->format('g:i');
       }
       
       return $schedule;
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
        return strtoupper($this->teacher['firstname'] . " " . $this->teacher['lastname']);
    }


    public function getChildrenCountAttribute()
    {
        return Course::where('parent_course_id', $this->id)->count();
    }

    public function getChildrenAttribute()
    {
        return Course::where('parent_course_id', $this->id)->get();
    }

    public function getEnrollmentsCountAttribute()
    {
        return $this->enrollments()->count();
    }

    /*
    |--------------------------------------------------------------------------
    | MUTATORS
    |--------------------------------------------------------------------------
    */
}
