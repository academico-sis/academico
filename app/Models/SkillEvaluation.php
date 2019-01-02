<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SkillEvaluation extends Model
{
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
}
