<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\Models\Course;

/**
 * App\Models\Year
 *
 * @property int $id
 * @property string $name
 * @property-read mixed $partnerships
 * @property-read mixed $year_distinct_students_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Period[] $periods
 * @property-read int|null $periods_count
 * @method static \Illuminate\Database\Eloquent\Builder|Year newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Year newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Year query()
 * @method static \Illuminate\Database\Eloquent\Builder|Year whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Year whereName($value)
 * @mixin \Eloquent
 */
class Year extends Model
{
    use CrudTrait;

    /*
    |--------------------------------------------------------------------------
    | GLOBAL VARIABLES
    |--------------------------------------------------------------------------
    */
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

    public function periods()
    {
        return $this->hasMany(Period::class);
    }

    public function getPartnershipsAttribute()
    {
        return Course::whereIn('period_id', $this->periods->pluck('id'))->pluck('partner_id')->unique()->count();
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
