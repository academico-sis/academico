<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

/**
 * App\Models\LeadType
 *
 * @property int $id
 * @property array $name
 * @property array|null $description
 * @property string|null $icon
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read mixed $translated_name
 * @property-read array $translations
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Student[] $students
 * @property-read int|null $students_count
 * @method static \Illuminate\Database\Eloquent\Builder|LeadType newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|LeadType newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|LeadType query()
 * @method static \Illuminate\Database\Eloquent\Builder|LeadType whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LeadType whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LeadType whereIcon($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LeadType whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LeadType whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LeadType whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class LeadType extends Model
{
    use CrudTrait;
    use HasTranslations;

    /*
    |--------------------------------------------------------------------------
    | GLOBAL VARIABLES
    |--------------------------------------------------------------------------
    */
    // protected $primaryKey = 'id';
    // public $timestamps = false;
    // protected $guarded = ['id'];
    protected $fillable = ['name', 'description'];
    public $translatable = ['name', 'description'];
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

    public function students()
    {
        return $this->hasMany(Student::class);
    }

    /*
    |--------------------------------------------------------------------------
    | SCOPES
    |--------------------------------------------------------------------------
    */

    /*
    |--------------------------------------------------------------------------
    | ACCESORS
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
