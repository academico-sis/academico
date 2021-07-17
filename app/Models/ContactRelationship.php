<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

/**
 * App\Models\ContactRelationship
 *
 * @property int $id
 * @property array $name
 * @property-read mixed $translated_name
 * @property-read array $translations
 * @method static \Illuminate\Database\Eloquent\Builder|ContactRelationship newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ContactRelationship newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ContactRelationship query()
 * @method static \Illuminate\Database\Eloquent\Builder|ContactRelationship whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ContactRelationship whereName($value)
 * @mixin \Eloquent
 */
class ContactRelationship extends Model
{
    use HasTranslations;

    public $timestamps = false;

    public $translatable = ['name'];

    protected $appends = ['translated_name'];

    public function getTranslatedNameAttribute()
    {
        return $this->getTranslation('name', app()->getLocale());
    }
}
