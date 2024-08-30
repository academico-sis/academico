<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Skills\Skill;
use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Level extends Model
{
    use CrudTrait;
    use SoftDeletes;

    public $timestamps = false;

    protected $guarded = ['id'];

    public function skill(): HasMany
    {
        return $this->hasMany(Skill::class);
    }
}
