<?php

namespace App\Models\Skills;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Model;

class SkillType extends Model
{
    use CrudTrait;
    protected $guarded = ['id'];
}
