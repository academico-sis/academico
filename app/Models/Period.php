<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Backpack\CRUD\CrudTrait;
use Illuminate\Support\Facades\DB;

class Period extends Model
{
    use CrudTrait;

    protected $table = 'periods';
    // protected $primaryKey = 'id';
    public $timestamps = false;
    // protected $guarded = ['id'];
    protected $fillable = ['name', 'year_id', 'start', 'end'];
    // protected $hidden = [];
    // protected $dates = [];
  
    /** todo allow admin to override this */
    public static function get_default_period()
    {
        //return Period::find(22); todo let user override the default period from the UI
        return Period::where('end', '>=', date('Y-m-d'))
        ->first();
    }

    public function enrollments()
    {
        return $this->hasManyThrough(Enrollment::class, Course::class)
            ->with('course');
    }

    public function courses()
    {
        return $this->hasMany(Course::class);
    }

    /** returns only pending or paid enrollments, without the child enrollments */
    public function real_enrollments()
    {
        return $this->hasManyThrough(Enrollment::class, Course::class)
        ->whereIn('status_id', ['1', '2']) // pending or paid
        ->where('parent_id', null);
    }
    
    /**
     * getPendingEnrollmentsCountAttribute
     * Do not count enrollments in children courses (todo why filter on course parent when we have a prent field on the enrollment record?)
     *
     */
    public function getPendingEnrollmentsCountAttribute()
    {
        return $this
            ->enrollments
            ->where('status_id', 1) // pending
            ->where('course.parent_course_id', null)
            ->count();
    }

    public function getStudentsCountAttribute()
    {
        return DB::table('enrollments')
            ->join('courses', 'enrollments.course_id', 'courses.id')
            ->where('courses.period_id', $this->id)
            ->where('courses.parent_course_id', null)
            ->whereIn('enrollments.status_id', ['1', '2']) // filter out cancelled enrollments, todo make this configurable.
            ->distinct('student_id')
            ->count('enrollments.student_id');
    }

    /**
     * getPaidEnrollmentsCountAttribute
     * Do not count enrollments in children courses
     *
     */
    public function getPaidEnrollmentsCountAttribute()
    {
        return $this
            ->enrollments
            ->where('status_id', 2) // pending
            ->where('course.parent_course_id', null)
            ->count();
    }

    public function getTotalEnrollmentsCountAttribute()
    {
        return $this->paid_enrollments_count + $this->pending_enrollments_count;
    }

    public function year()
    {
        return $this->belongsTo(Year::class);
    }

    public function getPreviousPeriodAttribute()
    {
        $period = Period::where('id', '<', $this->id)->orderBy('id', 'desc')->first();

        if(!$period == null) { return $period; }
        else {  return Period::first(); }
    }

    public function getNextPeriodAttribute()
    {
        return Period::where('id', '<', $this->id)->orderBy('id')->first();
    }

    /** Compute the acquisition rate = the part of students from period P-1 who have been kept in period P */
    public function getAcquisitionRateAttribute()
    {
        // get students enrolled in period P-1
        $previous_period_student_ids = $this->previous_period->real_enrollments->pluck('student_id');
        //dump($previous_period_student_ids);

        // and students enrolled in period P
        $current_students_ids = $this->real_enrollments->pluck('student_id');
        //dump($current_students_ids);

        // students both in period p-1 and period p
        $acquired_students = $previous_period_student_ids->intersect($current_students_ids);

        return number_format(100 * $acquired_students->count() / max($previous_period_student_ids->count(), 1), 1) . '%';
    }

    public function getNewStudentsCountAttribute()
    {
        // get students IDs enrolled in all previous periods
        $previous_period_student_ids = DB::table('enrollments')->join('courses', 'enrollments.course_id', 'courses.id')->where('period_id', '<', $this->id)->pluck('enrollments.student_id');

        // and students enrolled in period P
        $current_students_ids = $this->real_enrollments->unique('student_id');

        // students in period P who have never been enrolled in previous periods
        return $current_students_ids->whereNotIn('student_id', $previous_period_student_ids)->count();
    }

    public function getPeriodTaughtHoursCountAttribute()
    {
        // return the sum of all courses' volume for period
        return $this->courses->where('parent_course_id', null)->sum('volume');
    }

    public function getPeriodSoldHoursCountAttribute()
    {
        $total = 0;
        foreach ($this->courses()->withCount('real_enrollments')->get() as $course)
        {
            $total += $course->volume * $course->real_enrollments_count;
        }
        return $total;
    }
}
