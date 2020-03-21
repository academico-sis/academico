<?php

namespace App\Models;

use App\Models\Course;
use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class EvaluationType extends Model
{
    use CrudTrait;
    use HasTranslations;

    public $timestamps = false;

    protected $guarded = ['id'];

    public $translatable = ['name'];

    public function courses()
    {
        return $this->belongsToMany(Course::class);
    }
}
