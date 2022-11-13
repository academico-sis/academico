<?php

namespace App\Models;

use App\Models\Skills\Skill;
use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Model;

/**
 * @mixin IdeHelperEvaluationType
 */
class EvaluationType extends Model
{
    use CrudTrait;

    public $timestamps = false;

    protected $guarded = ['id'];

    protected $with = ['gradeTypes', 'skills'];

    public function courses()
    {
        return $this->belongsToMany(Course::class);
    }

    public function gradeTypes()
    {
        return $this->morphedByMany(GradeType::class, 'presettable', 'evaluation_type_presets');
    }

    public function skills()
    {
        return $this->morphedByMany(Skill::class, 'presettable', 'evaluation_type_presets');
    }
}
