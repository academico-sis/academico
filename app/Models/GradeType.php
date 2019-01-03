<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Backpack\CRUD\CrudTrait;

class GradeType extends Model
{
    use CrudTrait;

    protected $fillable = ['course_id', 'grade_type_id'];

}
