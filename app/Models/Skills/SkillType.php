<?php

namespace App\Models\Skills;

use Database\Factories\SkillTypeFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SkillType extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected static function newFactory(): SkillTypeFactory
    {
        return SkillTypeFactory::new();
    }
}
