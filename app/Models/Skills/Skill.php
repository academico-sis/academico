<?php

namespace App\Models\Skills;

use App\Models\Level;
use App\Models\Course;
use Backpack\CRUD\CrudTrait;
use App\Models\Skills\SkillType;
use App\Models\Skills\SkillEvaluation;
use Illuminate\Database\Eloquent\Model;

class Skill extends Model
{
    use CrudTrait;
    protected $guarded = ['id'];

    public function skill_type()
    {
        return $this->belongsTo(SkillType::class);
    }

    public function level()
    {
        return $this->belongsTo(Level::class);
    }

    public function course()
    {
        return $this->belongsToMany(Course::class);
    }

    public function skill_evaluation()
    {
        return $this->hasMany(SkillEvaluation::class);
    }
}
