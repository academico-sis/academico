<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Backpack\CRUD\app\Models\Traits\SpatieTranslatable\HasTranslations;
use Illuminate\Database\Eloquent\Model;

class GradeTypeCategory extends Model
{
    use CrudTrait;
    use HasTranslations;

    protected $table = 'grade_type_categories';

    public $timestamps = false;

    protected $guarded = ['id'];

    public array $translatable = ['name'];

    protected $appends = ['translated_name'];

    public function getTranslatedNameAttribute()
    {
        return $this->getTranslation('name', app()->getLocale());
    }
}
