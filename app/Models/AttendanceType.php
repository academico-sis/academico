<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

/**
 * @mixin IdeHelperAttendanceType
 */
class AttendanceType extends Model
{
    use HasTranslations;

    public array $translatable = ['name'];

    public $timestamps = false;

    protected $appends = ['translated_name'];

    public function getTranslatedNameAttribute()
    {
        return $this->getTranslation('name', app()->getLocale());
    }
}
