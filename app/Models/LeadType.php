<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Translatable\HasTranslations;

class LeadType extends Model
{
    use CrudTrait;
    use HasTranslations;

    protected $fillable = ['name', 'description'];

    public array $translatable = ['name', 'description'];

    protected $appends = ['translated_name'];

    public function students(): HasMany
    {
        return $this->hasMany(Student::class);
    }

    public function getTranslatedNameAttribute()
    {
        return $this->getTranslation('name', app()->getLocale());
    }
}
