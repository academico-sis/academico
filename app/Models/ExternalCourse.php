<?php

namespace App\Models;

use App\Events\CourseUpdated;
use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Builder;

/**
 * @mixin IdeHelperExternalCourse
 */
class ExternalCourse extends Course
{
    use CrudTrait;

    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope('external', function (Builder $builder) {
            $builder->where('campus_id', 2);
        });
    }

    protected $dispatchesEvents = [
        'updated' => CourseUpdated::class,
    ];

    protected $table = 'courses';

    protected $fillable = [];

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
