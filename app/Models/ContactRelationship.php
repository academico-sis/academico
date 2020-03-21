<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class ContactRelationship extends Model
{
    use HasTranslations;

    public $timestamps = false;

    public $translatable = ['name'];
}
