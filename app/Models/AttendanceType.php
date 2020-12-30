<?php

/** @noinspection Annotator */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class AttendanceType extends Model
{
    use HasTranslations;

    public $translatable = ['name'];
    public $timestamps = false;

    protected $appends = ['translated_name'];

    public function getTranslatedNameAttribute()
    {
        return $this->getTranslation('name', app()->getLocale());
    }
}
