<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Backpack\CRUD\CrudTrait;
use Spatie\Translatable\HasTranslations;

class ResultType extends Model
{

    use CrudTrait;
    use HasTranslations;

    protected $guarded = ['id'];
    
    public $translatable = ['name'];

}
