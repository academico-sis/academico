<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Model;

/**
 * @mixin IdeHelperProfession
 */
class Profession extends Model
{
    use CrudTrait;

    protected $guarded = ['id'];

    public function identifiableAttribute()
    {
        return $this->name;
    }
}
