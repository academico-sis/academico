<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

/**
 * App\Models\ResultType
 *
 * @property int $id
 * @property array $name
 * @property array|null $description
 * @property string|null $icon
 * @property string|null $class
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read mixed $translated_description
 * @property-read mixed $translated_name
 * @property-read array $translations
 * @method static \Illuminate\Database\Eloquent\Builder|ResultType newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ResultType newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ResultType query()
 * @method static \Illuminate\Database\Eloquent\Builder|ResultType whereClass($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ResultType whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ResultType whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ResultType whereIcon($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ResultType whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ResultType whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ResultType whereUpdatedAt($value)
 * @mixin \Eloquent
 */
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
