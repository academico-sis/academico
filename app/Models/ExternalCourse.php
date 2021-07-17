<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Builder;
use App\Events\CourseUpdated;

/**
 * App\Models\ExternalCourse
 *
 * @property int $id
 * @property int $campus_id
 * @property int|null $rhythm_id
 * @property int|null $level_id
 * @property int $volume
 * @property string $name
 * @property string $price
 * @property string|null $hourly_price
 * @property \Illuminate\Support\Carbon $start_date
 * @property \Illuminate\Support\Carbon $end_date
 * @property int|null $room_id
 * @property int|null $teacher_id
 * @property int|null $parent_course_id
 * @property int|null $exempt_attendance
 * @property int $period_id
 * @property int|null $opened
 * @property int|null $spots
 * @property int|null $head_count
 * @property int|null $new_students
 * @property string|null $color
 * @property int|null $evaluation_type_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int|null $partner_id
 * @property string|null $remote_volume
 * @property int|null $sync_to_lms
 * @property int|null $lms_id
 * @property-read \Illuminate\Database\Eloquent\Collection|\Spatie\Activitylog\Models\Activity[] $activities
 * @property-read int|null $activities_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Enrollment[] $enrollments
 * @property-read int|null $enrollments_count
 * @property-read bool $accepts_new_students
 * @property-read mixed $children
 * @property-read mixed $children_count
 * @property-read mixed $course_enrollments_count
 * @property-read mixed $course_level_name
 * @property-read mixed $course_period_name
 * @property-read mixed $course_rhythm_name
 * @property-read mixed $course_room_name
 * @property-read mixed $course_teacher_name
 * @property-read mixed $course_times
 * @property-read mixed $description
 * @property-read mixed $formatted_end_date
 * @property-read mixed $formatted_start_date
 * @property-read mixed $parent
 * @property-read mixed $pending_attendance
 * @property-read mixed $price_with_currency
 * @property-read mixed $shortname
 * @property-read mixed $sortable_id
 * @property-read bool $takes_attendance
 * @property-read mixed $total_volume
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Enrollment[] $real_enrollments
 * @property-read int|null $real_enrollments_count
 * @method static \Illuminate\Database\Eloquent\Builder|Course children()
 * @method static \Illuminate\Database\Eloquent\Builder|Course external()
 * @method static \Illuminate\Database\Eloquent\Builder|Course internal()
 * @method static Builder|ExternalCourse newModelQuery()
 * @method static Builder|ExternalCourse newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Course parent()
 * @method static Builder|ExternalCourse query()
 * @method static \Illuminate\Database\Eloquent\Builder|Course realcourses()
 * @method static Builder|ExternalCourse whereCampusId($value)
 * @method static Builder|ExternalCourse whereColor($value)
 * @method static Builder|ExternalCourse whereCreatedAt($value)
 * @method static Builder|ExternalCourse whereEndDate($value)
 * @method static Builder|ExternalCourse whereEvaluationTypeId($value)
 * @method static Builder|ExternalCourse whereExemptAttendance($value)
 * @method static Builder|ExternalCourse whereHeadCount($value)
 * @method static Builder|ExternalCourse whereHourlyPrice($value)
 * @method static Builder|ExternalCourse whereId($value)
 * @method static Builder|ExternalCourse whereLevelId($value)
 * @method static Builder|ExternalCourse whereLmsId($value)
 * @method static Builder|ExternalCourse whereName($value)
 * @method static Builder|ExternalCourse whereNewStudents($value)
 * @method static Builder|ExternalCourse whereOpened($value)
 * @method static Builder|ExternalCourse whereParentCourseId($value)
 * @method static Builder|ExternalCourse wherePartnerId($value)
 * @method static Builder|ExternalCourse wherePeriodId($value)
 * @method static Builder|ExternalCourse wherePrice($value)
 * @method static Builder|ExternalCourse whereRemoteVolume($value)
 * @method static Builder|ExternalCourse whereRhythmId($value)
 * @method static Builder|ExternalCourse whereRoomId($value)
 * @method static Builder|ExternalCourse whereSpots($value)
 * @method static Builder|ExternalCourse whereStartDate($value)
 * @method static Builder|ExternalCourse whereSyncToLms($value)
 * @method static Builder|ExternalCourse whereTeacherId($value)
 * @method static Builder|ExternalCourse whereUpdatedAt($value)
 * @method static Builder|ExternalCourse whereVolume($value)
 * @mixin \Eloquent
 */
class ExternalCourse extends Course
{
    use CrudTrait;

    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope('external', function (Builder $builder) {
            $builder->where('campus_id', 2);
        });
    }

    protected $dispatchesEvents = [
        'updated' => CourseUpdated::class,
    ];

    /*
    |--------------------------------------------------------------------------
    | GLOBAL VARIABLES
    |--------------------------------------------------------------------------
    */

    protected $table = 'courses';
    // protected $primaryKey = 'id';
    // public $timestamps = false;
    // protected $guarded = ['id'];
    protected $fillable = [];
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

    /*
    |--------------------------------------------------------------------------
    | MUTATORS
    |--------------------------------------------------------------------------
    */
}
