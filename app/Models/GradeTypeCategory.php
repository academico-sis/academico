<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Model;
use Backpack\CRUD\app\Models\Traits\SpatieTranslatable\HasTranslations;

class GradeTypeCategory extends Model
{
    use CrudTrait;
    use HasTranslations;

    /*
    |--------------------------------------------------------------------------
    | GLOBAL VARIABLES
    |--------------------------------------------------------------------------
    */

    protected $table = 'grade_type_categories';
    // protected $primaryKey = 'id';
    public $timestamps = false;
    protected $guarded = ['id'];
    // protected $fillable = [];
    public $translatable = ['name'];
    // protected $hidden = [];
    // protected $dates = [];
    protected $appends = ['translated_name'];

    /*
    |--------------------------------------------------------------------------
    | FUNCTIONS
    |--------------------------------------------------------------------------
    */

    /*
    |--------------------------------------------------------------------------
    | RELATIONS
    |--------------------------------------------------------------------------
    */

    /*
    |--------------------------------------------------------------------------
    | SCOPES
    |--------------------------------------------------------------------------
    */

    /*
    |--------------------------------------------------------------------------
    | ACCESSORS
    |--------------------------------------------------------------------------
    */

    public function getTranslatedNameAttribute()
    {
        return $this->getTranslation('name', app()->getLocale());
    }

    /*
    |--------------------------------------------------------------------------
    | MUTATORS
    |--------------------------------------------------------------------------
    */
}
