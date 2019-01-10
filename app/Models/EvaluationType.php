<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Backpack\CRUD\CrudTrait;
use Spatie\Translatable\HasTranslations;

class EvaluationType extends Model
{
    public $timestamps = false;
    use CrudTrait;
    use HasTranslations;

    protected $guarded = ['id'];
    public $translatable = ['name'];

    public function courses()
    {
        return $this->belongsToMany('\App\Models\Course');
    }
}
