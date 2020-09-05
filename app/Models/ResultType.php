<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class ResultType extends Model
{
    use CrudTrait;
    use HasTranslations;

    protected $guarded = ['id'];

    public $translatable = ['name', 'description'];

    protected $appends = ['translated_name', 'translated_description'];

    public function getTranslatedNameAttribute()
    {
        return $this->getTranslation('name', app()->getLocale());
    }

    public function getTranslatedDescriptionAttribute()
    {
        return $this->getTranslation('description', app()->getLocale());
    }
}
