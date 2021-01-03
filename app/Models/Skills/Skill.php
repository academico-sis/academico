<?php

namespace App\Models\Skills;

use App\Models\Course;
use App\Models\Level;
use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Model;

class Skill extends Model
{
    use CrudTrait;
    protected $guarded = ['id'];
    protected $with = ['level', 'skill_type'];

    /** The category the skill belongs to */
    public function skill_type()
    {
        return $this->belongsTo(SkillType::class);
    }

    /** A skill belongs to a level, this allows to filter available skills when attaching them to courses */
    public function level()
    {
        return $this->belongsTo(Level::class);
    }

    /** a skill has many courses, and a course has many skills
     * Skills are like "criteria" that will need to be evaluated during the course.
     */
    public function courses()
    {
        return $this->belongsToMany(Course::class);
    }

    /** A skill is linked to skill evaluations (themselves linked to enrollments) */
    public function skill_evaluations()
    {
        return $this->hasMany(SkillEvaluation::class);
    }
}
