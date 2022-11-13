<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Model;

/**
 * @mixin IdeHelperConfig
 */
class Config extends Model
{
    use CrudTrait;

    protected $table = 'config';

    protected $guarded = ['id'];
}
