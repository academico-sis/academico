<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Backpack\CRUD\app\Models\Traits\SpatieTranslatable\HasTranslations;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\GradeTypeCategory
 *
 * @property int $id
 * @property array $name
 * @property-read mixed $translated_name
 * @property-read array $translations
 * @method static \Illuminate\Database\Eloquent\Builder|GradeTypeCategory newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|GradeTypeCategory newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|GradeTypeCategory query()
 * @method static \Illuminate\Database\Eloquent\Builder|GradeTypeCategory whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GradeTypeCategory whereName($value)
 * @mixin \Eloquent
 */
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
