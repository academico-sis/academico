<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Scholarship extends Model
{
    use CrudTrait;
    use SoftDeletes;
    use LogsActivity;

    protected $table = 'scholarships';

    protected $guarded = ['id'];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()->logUnguarded();
    }

    public function enrollments()
    {
        return $this->belongsToMany(Enrollment::class);
    }
}
