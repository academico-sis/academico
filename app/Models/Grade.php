<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

class Grade extends Model
{
    use LogsActivity;

    protected $guarded = ['id'];
    protected $with = ['grade_type'];
    protected $appends = ['grade_type_category'];
    protected static $logFillable = true;
    protected $casts = [
        'grade' => 'integer'
    ];

    public function grade_type()
    {
        return $this->belongsTo(GradeType::class);
    }

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function getGradeTypeCategoryAttribute()
    {
        return $this->grade_type->category->name;
    }
}
