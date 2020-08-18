<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Model;

class GradeType extends Model
{
    use CrudTrait;

    protected $guarded = ['id'];
    protected $with = ['category'];

    public function courses()
    {
        return $this->belongsToMany(Course::class, 'course_grade_type', 'grade_type_id', 'course_id');
    }

    public function category()
    {
        return $this->belongsTo(GradeTypeCategory::class, 'grade_type_category_id');
    }
}
