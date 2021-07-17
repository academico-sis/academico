<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

/**
 * App\Models\EnrollmentStatusType
 *
 * @property int $id
 * @property array $name
 * @property-read array $translations
 * @method static \Illuminate\Database\Eloquent\Builder|EnrollmentStatusType newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|EnrollmentStatusType newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|EnrollmentStatusType query()
 * @method static \Illuminate\Database\Eloquent\Builder|EnrollmentStatusType whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EnrollmentStatusType whereName($value)
 * @mixin \Eloquent
 */
class EnrollmentStatusType extends Model
{
    use HasTranslations;

    public $translatable = ['name'];
    public $timestamps = false;
}
