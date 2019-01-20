<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Backpack\CRUD\CrudTrait;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

class Event extends Model
{
    use CrudTrait;
    use SoftDeletes;

    /*
    |--------------------------------------------------------------------------
    | GLOBAL VARIABLES
    |--------------------------------------------------------------------------
    */

    protected $table = 'events';
    // protected $primaryKey = 'id';
    public $timestamps = false;
    protected $guarded = ['id'];
    //protected $fillable = [];
    // protected $hidden = [];
    // protected $dates = [];
    protected $appends = ['length'];

    /*
    |--------------------------------------------------------------------------
    | FUNCTIONS
    |--------------------------------------------------------------------------
    */
    public function getLengthAttribute()
    {
        return Carbon::parse($this->end)->diffInSeconds(Carbon::parse($this->start)) / 3600;
    }

    /*
    |--------------------------------------------------------------------------
    | RELATIONS
    |--------------------------------------------------------------------------
    */

    public function coursetime()
    {
        return $this->belongsTo('App\Models\CourseTime');
    }
    
    public function course()
    {
        return $this->belongsTo('App\Models\Course');
    }

    public function students()
    {
        return $this->course->enrollments();
    }

    public function attendance()
    {
        return $this->hasMany('App\Models\Attendance');
    }
    

    public function teacher()
    {
        return $this->belongsTo('App\Models\User');
    }

    public function room()
    {
        return $this->belongsTo('App\Models\Room');
    }

    public function getPeriodAttribute()
    {
        return $this->course->period_id;
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

    public function getVolumeAttribute()
    {
        return Carbon::parse($this->start)->diffInMinutes(Carbon::parse($this->end)) / 60;
    }

    public function getAttendanceCountAttribute()
    {
        return $this->attendance->count();
    }

    public function getCourseEnrollmentsCountAttribute()
    {
        return $this->course->enrollments_count;
    }

    public function getTitleAttribute()
    {
        return 'myTitle';
    }
    
    /*
    |--------------------------------------------------------------------------
    | MUTATORS
    |--------------------------------------------------------------------------
    */
}
