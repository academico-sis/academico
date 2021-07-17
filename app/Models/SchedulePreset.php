<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\SchedulePreset
 *
 * @property int $id
 * @property string $name
 * @property string $presets
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|SchedulePreset newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|SchedulePreset newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|SchedulePreset query()
 * @method static \Illuminate\Database\Eloquent\Builder|SchedulePreset whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SchedulePreset whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SchedulePreset whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SchedulePreset wherePresets($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SchedulePreset whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class SchedulePreset extends Model
{
    use CrudTrait;

    /*
    |--------------------------------------------------------------------------
    | GLOBAL VARIABLES
    |--------------------------------------------------------------------------
    */

    protected $table = 'schedule_presets';
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
