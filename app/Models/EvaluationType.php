<?php

namespace App\Models;

use App\Models\Skills\Skill;
use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

/**
 * App\Models\EvaluationType
 *
 * @property int $id
 * @property array $name
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Course[] $courses
 * @property-read int|null $courses_count
 * @property-read mixed $translated_name
 * @property-read array $translations
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\GradeType[] $gradeTypes
 * @property-read int|null $grade_types_count
 * @property-read \Illuminate\Database\Eloquent\Collection|Skill[] $skills
 * @property-read int|null $skills_count
 * @method static \Illuminate\Database\Eloquent\Builder|EvaluationType newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|EvaluationType newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|EvaluationType query()
 * @method static \Illuminate\Database\Eloquent\Builder|EvaluationType whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EvaluationType whereName($value)
 * @mixin \Eloquent
 */
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
