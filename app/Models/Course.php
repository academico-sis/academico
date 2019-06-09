<?php

namespace App\Models;

use Carbon\Carbon;
use App\Models\Event;
use App\Models\Course;
use App\Models\Period;
use App\Models\Enrollment;
use Backpack\CRUD\CrudTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletes;

class Course extends Model
{
    use CrudTrait;
    use SoftDeletes;


/** model events */

protected static function boot()
{
    parent::boot();
    
    // when a course model is updated, offer to sync teacher through all events
    static::updated(function(Course $course) {
        
        // check whether the events for this course match the teacher from the request.
        $outdated_events = $course->events->where('teacher_id', '!=', $course->teacher_id);
        
        // if a mismatch exists, offer to update the events
        if($outdated_events->count() > 0)
        {
            return view('courses.update_events', [
                'outdated_events' => $outdated_events,
                'course' => $course,
                ]);
            }
            
            // todo idem for the room.
            
    });
        
}




    /*
    |--------------------------------------------------------------------------
    | GLOBAL VARIABLES
    |--------------------------------------------------------------------------
    */

    protected $table = 'courses';
    // protected $primaryKey = 'id';
    public $timestamps = true;
    protected $guarded = ['id'];
    //protected $fillable = [];
    // protected $hidden = [];
    // protected $dates = [];
    //protected $with = ['enrollments'];
    protected $appends = ['course_times'];

    /*
    |--------------------------------------------------------------------------
    | SCOPES
    |--------------------------------------------------------------------------
    */

    /** filter only the courses that have no parent */
    public function scopeParent($query)
    {
        return $query->where('parent_course_id', null);
    }

    public function scopeChildren($query)
    {
        return $query->where('parent_course_id', '!=', null);
    }

    /*
    |--------------------------------------------------------------------------
    | FUNCTIONS
    |--------------------------------------------------------------------------
    */

    /** returns all courses that are open for enrollments */
    public static function get_available_courses(Period $period)
    {
        return Course::where('period_id', $period->id)
        ->where('campus_id', 1)
        ->with('times')
        ->with('teacher')
        ->with('room')
        ->with('rhythm')
        ->with('level')
        ->withCount('enrollments')
        ->get();
    }


    public static function get_courses_offer(Period $period)
    {
        return Course::where('parent_course_id', null)
        ->where('period_id', $period->id)
        ->where('campus_id', 1)
        ->with('room')
        ->with('rhythm')
        ->with('level')
        ->withCount('enrollments')
        ->get();
    }

    /*
    |--------------------------------------------------------------------------
    | RELATIONS
    |--------------------------------------------------------------------------
    */

    /** the scheduled day/times for the course, that repeat throughout the course date span */
    public function times()
    {
        return $this->hasMany('App\Models\CourseTime', 'course_id');
    }

    /** course sessions (classes) with a specific start and end date/time */
    public function events()
    {
        return $this->hasMany('App\Models\Event');
    }

    /** may be null if the teacher is not yet assigned */
    public function teacher()
    {
        return $this->belongsTo('App\Models\Teacher')->withTrashed();
    }

    public function campus()
    {
        return $this->belongsTo('App\Models\Campus');
    }

    /** may be null if the room is not yet assigned */
    public function room()
    {
        return $this->belongsTo('App\Models\Room')->withTrashed();
    }

    /** the "category" of course */
    public function rhythm()
    {
        return $this->belongsTo('App\Models\Rhythm');
    }

    /** a course can only have one level. Parent courses would generally have no level defined */
    public function level()
    {
        return $this->belongsTo('App\Models\Level');
    }

    /** a course needs to belong to a period */
    public function period()
    {
        return $this->belongsTo('App\Models\Period');
    }

    /** children courses = sub-courses, or course modules */
    public function children()
    {
        return $this->hasMany('App\Models\Course', 'parent_course_id');
    }

    public function parent()
    {
        return $this->belongsTo('App\Models\Course', 'parent_course_id');
    }


    /** evaluation methods associated to the course - grades, skill-based evaluation... */
    public function evaluation_type()
    {
        return $this->belongsToMany('App\Models\EvaluationType');
    }

    /** a Grade model = an individual grade, belongs to a student */
    public function grades()
    {
        return $this->hasMany('App\Models\Grade')->with('student');
    }

    /** the different grade types associated to the course */
    public function grade_type()
    {
        return $this->belongsToMany('App\Models\GradeType');
    }

    /** in the case of skills-based evaluation, Skill models are attached to the course */
    public function skills()
    {
        return $this->belongsToMany('App\Models\Skills\Skill')->orderBy('order');
    }

    public function skill_evaluations()
    {
        return $this->hasMany('App\Models\Skills\SkillEvaluation')
        ->with('skill_scale');
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
            ->hasMany(Enrollment::class, 'course_id', 'id')
            ->whereIn('status_id', [1, 2]) // pending or paid enrollments only
            ->with('student');
    }

    /** returns only pending or paid enrollments, without the child enrollments */
    public function real_enrollments()
    {
        return $this->hasMany(Enrollment::class, 'course_id', 'id')
        ->whereIn('status_id', ['1', '2']) // pending or paid
        ->where('parent_id', null);
    }


    /*
    |--------------------------------------------------------------------------
    | ACCESORS
    |--------------------------------------------------------------------------
    */

    /**
     * returns the course repeating schedule
     * todo improve this method
     */
    public function getCourseTimesAttribute()
    {
        $days = "";
        $times = "";

        if ($this->times->count() > 0)
        {
            foreach ($this->times->unique('day') as $time)
            {
                 if ($time->day == '1') { $days .= "L"; }
                 if ($time->day == '2') { $days .= "M"; }
                 if ($time->day == '3') { $days .= "X"; }
                 if ($time->day == '4') { $days .= "J"; }
                 if ($time->day == '5') { $days .= "V"; }
                 if ($time->day == '6') { $days .= "S"; }
                 if ($time->day == '0') { $days .= "D"; }
            }

            foreach ($this->times->unique('start') as $time)
            {
                $times .= Carbon::parse($time->start)->format('g:i') . ' - ' . Carbon::parse($time->end)->format('g:i');
            }

        } elseif($this->children->count() > 0) {
            foreach ($this->children->first()->times->unique('day') as $time)
            {
                 if ($time->day == '1') { $days .= "L"; }
                 if ($time->day == '2') { $days .= "M"; }
                 if ($time->day == '3') { $days .= "X"; }
                 if ($time->day == '4') { $days .= "J"; }
                 if ($time->day == '5') { $days .= "V"; }
                 if ($time->day == '6') { $days .= "S"; }
                 if ($time->day == '0') { $days .= "D"; }
            }


            foreach ($this->children->first()->times->unique('start') as $time)
            {
                $times .= Carbon::parse($time->start)->format('g:i') . ' - ' . Carbon::parse($time->end)->format('g:i');
            }
        }

  
       return $days . " - " . $times;
    }

    public function getCourseRoomNameAttribute()
    {
        return strtoupper($this->room['name']);
    }

    public function getCourseLevelNameAttribute()
    {
        return $this->level['name'];
    }

    public function getCourseRhythmNameAttribute()
    {
        return strtoupper($this->rhythm['name']);
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

    public function getCourseEnrollmentsCountAttribute()
    {
        return $this->enrollments()->count();
    }

    public function getParentAttribute()
    {
        if ($this->parent_course_id !== null)
        {
            return $this->parent_course_id;
        }
        else {
            return $this->id;
        }
    }

    /*
    |--------------------------------------------------------------------------
    | MUTATORS
    |--------------------------------------------------------------------------
    */
}
