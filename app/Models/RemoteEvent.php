<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

/** A RemoteEvent represents hours that do not have a specific date/time, but that should be taken into account in the teacher's total for the month or the period */
class RemoteEvent extends Model
{
    use CrudTrait;
    use LogsActivity;

    protected $guarded = ['id'];
    protected static $logUnguarded = true;

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
