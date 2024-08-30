<?php

namespace App\Models;

use App\Models\Skills\Skill;
use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphedByMany;

class EvaluationType extends Model
{
    use CrudTrait;

    public $timestamps = false;

    protected $guarded = ['id'];

    protected $with = ['gradeTypes', 'skills'];

    public function courses(): BelongsToMany
    {
        return $this->belongsToMany(Course::class);
    }

    public function gradeTypes(): MorphedByMany
    {
        return $this->morphedByMany(GradeType::class, 'presettable', 'evaluation_type_presets');
    }

    public function skills(): MorphedByMany
    {
        return $this->morphedByMany(Skill::class, 'presettable', 'evaluation_type_presets');
    }
}
