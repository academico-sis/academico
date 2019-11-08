<?php

namespace App\Models;

use App\Models\Teacher;
use App\Models\Period;
use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Model;

/** A RemoteEvent represents hours that do not have a specific date/time, but that should be taken into account in the teacher's total for the month or the period */
class RemoteEvent extends Model
{
    use CrudTrait;
    protected $guarded = ['id'];

    public function teacher()
    {
        return $this->belongsTo(Teacher::class);
    }

    public function period()
    {
        return $this->belongsTo(Period::class);
    }

    /** we store the time volume in seconds */
    public function getWorkedHoursAttribute($value)
    {
        return $value / 3600;
    }

    /** we store the time volume in seconds */
    public function setWorkedHoursAttribute($value)
    {
        $this->attributes['worked_hours'] = $value * 3600;
    }
}
