<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Paymentmethod
 *
 * @property int $id
 * @property string $name
 * @property string $code
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|Paymentmethod newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Paymentmethod newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Paymentmethod query()
 * @method static \Illuminate\Database\Eloquent\Builder|Paymentmethod whereCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Paymentmethod whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Paymentmethod whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Paymentmethod whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Paymentmethod whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Paymentmethod extends Model
{
    use CrudTrait;

    /*
    |--------------------------------------------------------------------------
    | GLOBAL VARIABLES
    |--------------------------------------------------------------------------
    */
    // protected $primaryKey = 'id';
    // public $timestamps = false;
    // protected $guarded = ['id'];
    protected $fillable = ['name', 'code'];
    // protected $hidden = [];
    // protected $dates = [];

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
    | ACCESORS
    |--------------------------------------------------------------------------
    */

    /*
    |--------------------------------------------------------------------------
    | MUTATORS
    |--------------------------------------------------------------------------
    */
}
