<?php

namespace App\Models;

use App\Models\Student;
use App\Models\GradeType;
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
