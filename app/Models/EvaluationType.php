<?php

namespace App\Models;

use App\Models\Skills\Skill;
use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class EvaluationType extends Model
{
    use CrudTrait;
    use HasTranslations;

    public $timestamps = false;

    protected $guarded = ['id'];
    protected $with = ['gradeTypes', 'skills'];
    public $translatable = ['name'];
    protected $appends = ['translated_name'];

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

    public function getTranslatedNameAttribute()
    {
        return $this->getTranslation('name', app()->getLocale());
    }
}
