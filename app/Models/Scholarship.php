<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\Models\Scholarship
 *
 * @property int $id
 * @property string $name
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Enrollment[] $enrollments
 * @property-read int|null $enrollments_count
 * @method static \Illuminate\Database\Eloquent\Builder|Scholarship newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Scholarship newQuery()
 * @method static \Illuminate\Database\Query\Builder|Scholarship onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Scholarship query()
 * @method static \Illuminate\Database\Eloquent\Builder|Scholarship whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Scholarship whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Scholarship whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Scholarship whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Scholarship whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|Scholarship withTrashed()
 * @method static \Illuminate\Database\Query\Builder|Scholarship withoutTrashed()
 * @mixin \Eloquent
 */
class Scholarship extends Model
{
    use CrudTrait;
    use SoftDeletes;

    /*
    |--------------------------------------------------------------------------
    | GLOBAL VARIABLES
    |--------------------------------------------------------------------------
    */

    protected $table = 'scholarships';
    // protected $primaryKey = 'id';
    // public $timestamps = false;
    protected $guarded = ['id'];
    // protected $fillable = [];
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

    public function enrollments()
    {
        return $this->belongsToMany(Enrollment::class);
    }

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

    /*
    |--------------------------------------------------------------------------
    | MUTATORS
    |--------------------------------------------------------------------------
    */
}
