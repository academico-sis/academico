<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Grade extends Model
{
    use LogsActivity;

    protected $guarded = ['id'];

    protected $with = ['gradeType'];

    protected $appends = ['grade_type_category'];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()->logUnguarded();
    }

    public function gradeType()
    {
        return $this->belongsTo(GradeType::class);
    }

    public function enrollment()
    {
        return $this->belongsTo(Enrollment::class);
    }

    public function getGradeTypeCategoryAttribute()
    {
        return $this->gradeType->category->name;
    }
}
