<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class InvoiceType extends Model
{
    use HasTranslations;

    public $translatable = ['description'];

    protected $appends = ['translated_name'];

    public function getTranslatedNameAttribute()
    {
        return $this->getTranslation('description', app()->getLocale());
    }
}
