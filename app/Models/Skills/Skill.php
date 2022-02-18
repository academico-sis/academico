<?php

namespace App\Models\Skills;

use App\Models\EvaluationType;
use App\Models\Level;
use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

/**
 * @mixin IdeHelperSkill
 */
class Skill extends Model
{
    use CrudTrait;
    use LogsActivity;

    protected $guarded = ['id'];

    protected $with = ['level', 'skill_type'];

    protected $appends = ['complete_name'];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()->logUnguarded();
    }

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

    /** A skill is linked to skill evaluations (themselves linked to enrollments) */
    public function skill_evaluations()
    {
        return $this->hasMany(SkillEvaluation::class);
    }

    public function presets()
    {
        return $this->morphToMany(EvaluationType::class, 'presettable', 'evaluation_type_presets');
    }

    public function getCompleteNameAttribute(): string
    {
        return '['.($this->level->name ?? '').'] '.($this->skill_type->shortname ?? '').' - '.$this->name ?? '';
    }
}
