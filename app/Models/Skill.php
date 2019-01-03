<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Backpack\CRUD\CrudTrait;

class Skill extends Model
{
    use CrudTrait;
    protected $guarded = ['id'];

    public function skill_type()
    {
        return $this->belongsTo('\App\Models\SkillType');
    }

    public function level()
    {
        return $this->belongsTo('\App\Models\Level');
    }

    public function course()
    {
        return $this->belongsToMany('\App\Models\Course');
    }

    public function skill_evaluation()
    {
        return $this->hasMany('\App\Models\CourseSkillEvaluation');
    }
}
