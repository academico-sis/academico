<?php

namespace App\Models\Skills;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

/**
 * @mixin IdeHelperSkillScale
 */
class SkillScale extends Model
{
    use CrudTrait;
    use HasTranslations;

    protected $guarded = ['id'];

    public $translatable = ['shortname', 'name'];

    protected $appends = ['scale_name'];

    public function getScaleNameAttribute()
    {
        return $this->name;
    }
}
