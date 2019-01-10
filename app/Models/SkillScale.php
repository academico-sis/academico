<?php

namespace App\Models;

use Backpack\CRUD\CrudTrait;
use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class SkillScale extends Model
{
    use CrudTrait;
    protected $guarded = ['id'];
    use HasTranslations;
    public $translatable = ['name'];
}
