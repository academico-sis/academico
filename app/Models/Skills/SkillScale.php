<?php

namespace App\Models\Skills;

use Illuminate\Database\Eloquent\Model;
use Backpack\CRUD\CrudTrait;
use Spatie\Translatable\HasTranslations;

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
        if($this->value > 0.75) { return "success"; }
        elseif(0.4 <= $this->value && 0.75 >= $this->value) { return "warning"; }
        else { return "danger"; }
    }
}
