<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Translatable\HasTranslations;

/**
 * NOTE: In the current configuration, the campus with the ID of 1 represent the school itself
 * the campus model with the ID of 2 represents all external courses
 */
class Campus extends Model
{
    use CrudTrait;
    use SoftDeletes;
    use HasTranslations;

    public array $translatable = ['name'];

    public $timestamps = false;

    protected $fillable = ['name'];
}
