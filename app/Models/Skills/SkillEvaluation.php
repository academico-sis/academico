<?php

namespace App\Models\Skills;

use App\Models\Enrollment;
use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Model;

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
