<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Backpack\CRUD\CrudTrait;

class GradeType extends Model
{
    use CrudTrait;

    protected $fillable = ['name', 'total'];

    public function courses()
    {
        return $this->belongsToMany('App\Models\Course', 'course_grade_type', 'grade_type_id', 'course_id');
    }
}
