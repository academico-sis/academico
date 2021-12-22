<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class EnrollmentStatusType extends Model
{
    use HasTranslations;

    public array $translatable = ['name'];

    public $timestamps = false;
}
