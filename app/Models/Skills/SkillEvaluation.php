<?php

namespace App\Models\Skills;

use App\Models\Enrollment;
use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

/**
 * @mixin IdeHelperSkillEvaluation
 */
class SkillEvaluation extends Model
{
    use CrudTrait;
    use LogsActivity;

    protected $guarded = ['id'];

    protected $with = ['skill', 'skill_scale'];


    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()->logUnguarded();
    }

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
