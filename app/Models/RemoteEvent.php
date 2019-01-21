<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Backpack\CRUD\CrudTrait;

/** A RemoteEvent represents hours that do not have a specific date/time, but that should be taken into account in the teacher's total for the month or the period */
class RemoteEvent extends Model
{
    use CrudTrait;
    protected $guarded = ['id'];

    public function user()
    {
        return $this->belongsTo('App\Models\User');
    }

    public function period()
    {
        return $this->belongsTo('App\Models\Period');
    }

    /** we store the time volume in seconds */
    public function getWorkedHoursAttribute($value)
    {
        return $value / 3600;
    }
}
