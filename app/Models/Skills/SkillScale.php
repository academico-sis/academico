<?php

namespace App\Models\Skills;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

/**
 * App\Models\Skills\SkillScale
 *
 * @property int $id
 * @property array $shortname
 * @property array $name
 * @property string $value
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $deleted_at
 * @property-read mixed $scale_name
 * @property-read mixed $style
 * @property-read array $translations
 * @method static \Illuminate\Database\Eloquent\Builder|SkillScale newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|SkillScale newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|SkillScale query()
 * @method static \Illuminate\Database\Eloquent\Builder|SkillScale whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SkillScale whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SkillScale whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SkillScale whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SkillScale whereShortname($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SkillScale whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SkillScale whereValue($value)
 * @mixin \Eloquent
 */
class SkillScale extends Model
{
    use CrudTrait;
    use HasTranslations;

    protected $guarded = ['id'];

    public $translatable = ['shortname', 'name'];

    public function getScaleNameAttribute()
    {
        return $this->name;
    }

    public function getStyleAttribute()
    {
        if ($this->value > 0.75) {
            return 'success';
        }

        if ((0.4 <= $this->value) && (0.75 >= $this->value)) {
            return 'warning';
        }

        return 'danger';
    }
}
