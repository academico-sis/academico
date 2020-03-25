<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Grade extends Model
{
    protected $fillable = ['student_id', 'grade_type_id', 'grade', 'course_id'];

    public function grade_type()
    {
        return $this->belongsTo(GradeType::class);
    }

    public function student()
    {
        return $this->belongsTo(Student::class);
    }
}
