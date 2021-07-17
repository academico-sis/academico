<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

/**
 * App\Models\Grade
 *
 * @property int $id
 * @property int $grade_type_id
 * @property int|null $enrollment_id
 * @property int|null $student_id
 * @property int|null $course_id
 * @property string $grade
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\Spatie\Activitylog\Models\Activity[] $activities
 * @property-read int|null $activities_count
 * @property-read \App\Models\Enrollment|null $enrollment
 * @property-read mixed $grade_type_category
 * @property-read \App\Models\GradeType $grade_type
 * @method static \Illuminate\Database\Eloquent\Builder|Grade newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Grade newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Grade query()
 * @method static \Illuminate\Database\Eloquent\Builder|Grade whereCourseId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Grade whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Grade whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Grade whereEnrollmentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Grade whereGrade($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Grade whereGradeTypeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Grade whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Grade whereStudentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Grade whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Grade extends Model
{
    use LogsActivity;

    protected $guarded = ['id'];
    protected $with = ['grade_type'];
    protected $appends = ['grade_type_category'];
    protected static $logFillable = true;

    public function grade_type()
    {
        return $this->belongsTo(GradeType::class);
    }

    public function enrollment()
    {
        return $this->belongsTo(Enrollment::class);
    }

    public function getGradeTypeCategoryAttribute()
    {
        return $this->grade_type->category->name;
    }
}
