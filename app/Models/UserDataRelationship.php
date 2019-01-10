<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class UserDataRelationship extends Model
{
    protected $table = "user_data_relationships";
    public $timestamps = false;

    use HasTranslations;
    public $translatable = ['name'];
}
