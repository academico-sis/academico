<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

/**
 * @mixin IdeHelperEnrollmentStatusType
 */
class EnrollmentStatusType extends Model
{
    use HasTranslations;

    public array $translatable = ['name'];

    public $timestamps = false;
}
