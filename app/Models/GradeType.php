<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Model;

class GradeType extends Model
{
    use CrudTrait;

    protected $guarded = ['id'];
    protected $with = ['category'];
    protected $appends = ['complete_name'];

    public function category()
    {
        return $this->belongsTo(GradeTypeCategory::class, 'grade_type_category_id');
    }

    public function presets()
    {
        return $this->morphToMany(EvaluationType::class, 'presettable', 'evaluation_type_presets');
    }

    public function getCompleteNameAttribute()
    {
        return '[' . $this->category->name . '] ' . $this->name;
    }
}
