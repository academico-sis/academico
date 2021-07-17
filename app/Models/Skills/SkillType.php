<?php

namespace App\Models\Skills;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Skills\SkillType
 *
 * @property int $id
 * @property string $shortname
 * @property string|null $name
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|SkillType newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|SkillType newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|SkillType query()
 * @method static \Illuminate\Database\Eloquent\Builder|SkillType whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SkillType whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SkillType whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SkillType whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SkillType whereShortname($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SkillType whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class SkillType extends Model
{
    use CrudTrait;
    protected $guarded = ['id'];
}
