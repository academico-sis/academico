<?php

namespace App\Models\Skills;

use App\Models\Enrollment;
use Database\Factories\SkillEvaluationFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class SkillEvaluation extends Model
{
    use HasFactory, LogsActivity;

    protected $guarded = ['id'];

    protected $with = ['skill', 'skill_scale'];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()->logUnguarded();
    }

    public function skill(): BelongsTo
    {
        return $this->belongsTo(Skill::class);
    }

    public function enrollment(): BelongsTo
    {
        return $this->belongsTo(Enrollment::class);
    }

    public function skill_scale(): BelongsTo
    {
        return $this->belongsTo(SkillScale::class);
    }

    protected static function newFactory(): SkillEvaluationFactory
    {
        return SkillEvaluationFactory::new();
    }
}
