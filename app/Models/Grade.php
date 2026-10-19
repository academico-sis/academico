<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;

class Grade extends Model
{
    use HasFactory, LogsActivity;

    protected $guarded = ['id'];

    protected $with = ['gradeType'];

    protected $appends = ['grade_type_category'];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()->logUnguarded();
    }

    public function gradeType(): BelongsTo
    {
        return $this->belongsTo(GradeType::class);
    }

    public function enrollment(): BelongsTo
    {
        return $this->belongsTo(Enrollment::class);
    }

    public function getGradeTypeCategoryAttribute()
    {
        return $this->gradeType->category->name;
    }
}
