<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\GradeType
 *
 * @property int $id
 * @property int $grade_type_category_id
 * @property string $name
 * @property int $total
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $deleted_at
 * @property-read \App\Models\GradeTypeCategory $category
 * @property-read mixed $complete_name
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\EvaluationType[] $presets
 * @property-read int|null $presets_count
 * @method static \Illuminate\Database\Eloquent\Builder|GradeType newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|GradeType newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|GradeType query()
 * @method static \Illuminate\Database\Eloquent\Builder|GradeType whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GradeType whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GradeType whereGradeTypeCategoryId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GradeType whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GradeType whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GradeType whereTotal($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GradeType whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class GradeType extends Model
{
    use CrudTrait;

    protected $guarded = ['id'];
    protected $with = ['category'];
    protected $appends = ['complete_name'];

    public function category()
    {
        return $this->belongsTo(GradeTypeCategory::class, 'grade_type_category_id');
    }

    public function presets()
    {
        return $this->morphToMany(EvaluationType::class, 'presettable', 'evaluation_type_presets');
    }

    public function getCompleteNameAttribute()
    {
        if ($this->category) {
            return '[' . $this->category->name . '] ' . $this->name;
        }

        return null;
    }
}
