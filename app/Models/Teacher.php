<?php

namespace App\Models;

use App\Scopes\TeacherScope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;


class Teacher extends BackpackUser
{
    protected $table = 'users';

    /**
     * The "booting" method of the model.
     *
     * @return void
     */
    protected static function boot()
    {
        parent::boot();

        // Restrict the results from this class to users who have the teacher role.
        static::addGlobalScope('id', function (Builder $builder) {
            $builder->role('admin');

        });
    }

}
