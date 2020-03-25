<?php

namespace App\Models\Skills;

use App\Models\Course;
use App\Models\Student;
use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Model;

class SkillEvaluation extends Model
{
    protected $guarded = ['id'];

    use CrudTrait;

    public function skill()
    {
        return $this->belongsTo(Skill::class);
    }

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function skill_scale()
    {
        return $this->belongsTo(SkillScale::class);
    }
}
