<?php

namespace App\Models;

use App\Models\Skills\Skill;
use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @mixin IdeHelperLevel
 */
class Level extends Model
{
    use CrudTrait;
    use SoftDeletes;

    public $timestamps = false;

    protected $guarded = ['id'];

    public function skill()
    {
        return $this->hasMany(Skill::class);
    }
}
