<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Model;

/**
 * @mixin IdeHelperYear
 */
class Year extends Model
{
    use CrudTrait;

    public $timestamps = false;

    protected $guarded = ['id'];

    public function periods()
    {
        return $this->hasMany(Period::class);
    }
}
