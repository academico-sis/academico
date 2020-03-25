<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Model;

class GradeType extends Model
{
    use CrudTrait;

    protected $fillable = ['name', 'total'];

    public function courses()
    {
        return $this->belongsToMany(Course::class, 'course_grade_type', 'grade_type_id', 'course_id');
    }
}
