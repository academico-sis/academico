<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Year extends Model
{
    use CrudTrait;

    public $timestamps = false;

    protected $guarded = ['id'];

    public function periods()
    {
        return $this->hasMany(Period::class);
    }

    public function getPartnershipsAttribute()
    {
        return Course::whereIn('period_id', $this->periods->pluck('id'))->pluck('partner_id')->unique()->count();
    }

    public function studentCount($gender = null)
    {
        if (in_array($gender, [1,2])) {
            return DB::table('enrollments')
                ->join('courses', 'enrollments.course_id', 'courses.id')
                ->join('periods', 'courses.period_id', 'periods.id')
                ->join('students', 'enrollments.student_id', 'students.id')
                ->where('periods.year_id', $this->id)
                ->whereIn('enrollments.status_id', ['1', '2']) // filter out cancelled enrollments, todo make this configurable.
                ->where('enrollments.parent_id', null)->where('enrollments.deleted_at', null)
                ->where('students.gender_id', $gender)
                ->distinct('student_id')->count('enrollments.student_id');
        }

        if ($gender === 0) {
            return DB::table('enrollments')
                ->join('courses', 'enrollments.course_id', 'courses.id')
                ->join('periods', 'courses.period_id', 'periods.id')
                ->join('students', 'enrollments.student_id', 'students.id')
                ->where('periods.year_id', $this->id)
                ->whereIn('enrollments.status_id', ['1', '2']) // filter out cancelled enrollments, todo make this configurable.
                ->where('enrollments.parent_id', null)->where('enrollments.deleted_at', null)
                ->where(function($query) {
                    return $query->where('students.gender_id', 0)->orWhereNull('students.gender_id');
                })
                ->distinct('student_id')->count('enrollments.student_id');
        }

        return DB::table('enrollments')->join('courses', 'enrollments.course_id', 'courses.id')->join('periods', 'courses.period_id', 'periods.id')->where('periods.year_id', $this->id)->whereIn('enrollments.status_id', ['1', '2']) // filter out cancelled enrollments, todo make this configurable.
        ->where('enrollments.parent_id', null)->where('enrollments.deleted_at', null)->distinct('student_id')->count('enrollments.student_id');
    }
}
