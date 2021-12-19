<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @mixin IdeHelperTitle
 */
class Title extends Model
{
    protected $guarded = ['id'];

    public $timestamps = false;
}
