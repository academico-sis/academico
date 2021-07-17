<?php

namespace App\Models\Skills;

use App\Models\Enrollment;
use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Skills\SkillEvaluation
 *
 * @property int $id
 * @property int|null $course_id
 * @property int|null $student_id
 * @property int|null $enrollment_id
 * @property int $skill_scale_id
 * @property int $skill_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $deleted_at
 * @property-read Enrollment|null $enrollment
 * @property-read \App\Models\Skills\Skill $skill
 * @property-read \App\Models\Skills\SkillScale $skill_scale
 * @method static \Illuminate\Database\Eloquent\Builder|SkillEvaluation newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|SkillEvaluation newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|SkillEvaluation query()
 * @method static \Illuminate\Database\Eloquent\Builder|SkillEvaluation whereCourseId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SkillEvaluation whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SkillEvaluation whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SkillEvaluation whereEnrollmentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SkillEvaluation whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SkillEvaluation whereSkillId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SkillEvaluation whereSkillScaleId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SkillEvaluation whereStudentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SkillEvaluation whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class SkillEvaluation extends Model
{
    protected $guarded = ['id'];
    protected $with = ['skill', 'skill_scale'];

    use CrudTrait;

    public function skill()
    {
        return $this->belongsTo(Skill::class);
    }

    public function enrollment()
    {
        return $this->belongsTo(Enrollment::class);
    }

    public function skill_scale()
    {
        return $this->belongsTo(SkillScale::class);
    }
}
