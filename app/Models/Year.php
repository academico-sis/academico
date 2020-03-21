<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Year extends Model
{
    use CrudTrait;

    /*
    |--------------------------------------------------------------------------
    | GLOBAL VARIABLES
    |--------------------------------------------------------------------------
    */

    protected $table = 'years';
    // protected $primaryKey = 'id';
    public $timestamps = false;
    protected $guarded = ['id'];
    //protected $fillable = [];
    // protected $hidden = [];
    // protected $dates = [];

    /*
    |--------------------------------------------------------------------------
    | FUNCTIONS
    |--------------------------------------------------------------------------
    */

    /*
    |--------------------------------------------------------------------------
    | RELATIONS
    |--------------------------------------------------------------------------
    */

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

    public function getYearDistinctStudentsCountAttribute()
    {
        return DB::table('enrollments')
            ->join('courses', 'enrollments.course_id', 'courses.id')
            ->join('periods', 'courses.period_id', 'periods.id')
            ->where('periods.year_id', $this->id)
            ->whereIn('enrollments.status_id', ['1', '2']) // filter out cancelled enrollments, todo make this configurable.
            ->where('enrollments.parent_id', null)
            ->where('enrollments.deleted_at', null)
            ->distinct('student_id')
            ->count('enrollments.student_id');
    }

    /*
    |--------------------------------------------------------------------------
    | MUTATORS
    |--------------------------------------------------------------------------
    */
}
