<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Backpack\CRUD\CrudTrait;

class SkillEvaluation extends Model
{
    protected $table = "skill_evaluations";
    protected $guarded = ['id'];

    use CrudTrait;


    public function skill()
    {
        return $this->belongsTo('\App\Models\Skill');
    }

    public function student()
    {
        return $this->belongsTo('\App\Models\User', 'user_id');
    }
    
    public function course()
    {
        return $this->belongsTo('\App\Models\Course');
    }

    public function skill_scale()
    {
        return $this->belongsTo('\App\Models\SkillScale');
    } 
}
