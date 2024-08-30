<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Year extends Model
{
    use CrudTrait;

    public $timestamps = false;

    protected $guarded = ['id'];

    public function periods(): HasMany
    {
        return $this->hasMany(Period::class);
    }
}
