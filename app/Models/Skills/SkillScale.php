<?php

namespace App\Models\Skills;

use App\Models\Concerns\HasFallbackTranslations;
use Database\Factories\SkillScaleFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SkillScale extends Model
{
    use HasFactory, HasFallbackTranslations;

    protected $guarded = ['id'];

    public array $translatable = ['shortname', 'name'];

    protected $appends = ['scale_name'];

    public function getScaleNameAttribute()
    {
        return $this->name;
    }

    protected static function newFactory(): SkillScaleFactory
    {
        return SkillScaleFactory::new();
    }
}
