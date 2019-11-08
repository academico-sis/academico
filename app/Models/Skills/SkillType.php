<?php

namespace App\Models\Skills;

use Illuminate\Database\Eloquent\Model;
use Backpack\CRUD\app\Models\Traits\CrudTrait;

class SkillType extends Model
{
    use CrudTrait;
    protected $guarded = ['id'];
}
