<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class AttendanceType extends Model
{
    use HasTranslations;

    public array $translatable = ['name'];

    public $timestamps = false;

    protected $appends = ['translated_name'];

    protected $fillable = ['color'];

    public function getTranslatedNameAttribute()
    {
        return $this->getTranslation('name', app()->getLocale());
    }
}
