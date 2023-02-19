<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Room extends Model
{
    use CrudTrait;
    use SoftDeletes;
    use LogsActivity;

    public $timestamps = false;

    protected $guarded = ['id'];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()->logUnguarded();
    }

    public function events()
    {
        return $this->hasMany(Event::class);
    }

    public function campus()
    {
        return $this->belongsTo(Campus::class);
    }
}
