<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Grade extends Model
{

    protected $fillable = ['user_id', 'grade_type_id', 'grade', 'course_id'];

    public function grade_type()
    {
        return $this->belongsTo('\App\Models\GradeType');
    }

    public function student()
    {
        return $this->belongsTo('\App\Models\User', 'user_id');
    }
}
