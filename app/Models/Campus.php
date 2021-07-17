<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Translatable\HasTranslations;

/**
 * App\Models\Campus
 *
 * @property int $id
 * @property array $name
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read array $translations
 * @method static \Illuminate\Database\Eloquent\Builder|Campus newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Campus newQuery()
 * @method static \Illuminate\Database\Query\Builder|Campus onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Campus query()
 * @method static \Illuminate\Database\Eloquent\Builder|Campus whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Campus whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Campus whereName($value)
 * @method static \Illuminate\Database\Query\Builder|Campus withTrashed()
 * @method static \Illuminate\Database\Query\Builder|Campus withoutTrashed()
 * @mixin \Eloquent
 */
class Campus extends Model
{
    use CrudTrait;
    use SoftDeletes;
    use HasTranslations;

    public $translatable = ['name'];
    // protected $primaryKey = 'id';
    public $timestamps = false;
    // protected $guarded = ['id'];
    protected $fillable = ['name'];
    // protected $hidden = [];
    // protected $dates = [];

    /* in the current configuration, the campus with the ID of 1 represent the school itself
     * the campus model with the ID of 2 represents all external courses
     */
}
