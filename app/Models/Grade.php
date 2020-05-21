<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

class Grade extends Model
{
    use LogsActivity;

    protected $fillable = ['student_id', 'grade_type_id', 'grade', 'course_id'];
    protected static $logFillable = true;

    public function grade_type()
    {
        return $this->belongsTo(GradeType::class);
    }

    public function student()
    {
        return $this->belongsTo(Student::class);
    }
}
