<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class AttendanceType extends Model
{
    use HasTranslations;

    public $translatable = ['name'];
    public $timestamps = false;

}
