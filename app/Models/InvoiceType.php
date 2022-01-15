<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

/**
 * @mixin IdeHelperInvoiceType
 */
class InvoiceType extends Model
{
    use HasTranslations;

    public array $translatable = ['description'];

    protected $appends = ['translated_name'];

    public function getTranslatedNameAttribute()
    {
        return $this->getTranslation('description', app()->getLocale());
    }
}
