<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Backpack\CRUD\CrudTrait;

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

    public function getWorkedHoursAttribute($value)
    {
        return $value / 3600;
    }
}
