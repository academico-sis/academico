<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use App\Models\Enrollment;
use Illuminate\Support\Facades\App;

class ScheduledPayment extends Model
{
    /*
    |--------------------------------------------------------------------------
    | GLOBAL VARIABLES
    |--------------------------------------------------------------------------
    */

    protected $table = 'scheduled_payments';
    // protected $primaryKey = 'id';
    // public $timestamps = false;
    protected $guarded = ['id'];
    // protected $fillable = [];
    // protected $hidden = [];
    // protected $dates = [];

    /*
    |--------------------------------------------------------------------------
    | FUNCTIONS
    |--------------------------------------------------------------------------
    */

    /*
    |--------------------------------------------------------------------------
    | RELATIONS
    |--------------------------------------------------------------------------
    */

    public function enrollment()
    {
        return $this->belongsTo(Enrollment::class);
    }

    /*
    |--------------------------------------------------------------------------
    | SCOPES
    |--------------------------------------------------------------------------
    */

    /*
    |--------------------------------------------------------------------------
    | ACCESSORS
    |--------------------------------------------------------------------------
    */

    public function getValueAttribute($value)
    {
        return $value / 100;
    }

    function getDateForHumansAttribute()
    {
        if ($this->date)
        {
            return Carbon::parse($this->date, 'UTC')->locale(App::getLocale())->isoFormat('LL');
        }
        return Carbon::parse($this->created_at, 'UTC')->locale(App::getLocale())->isoFormat('LL');
    }

    public function getDisplayStatusAttribute()
    {
        switch ($this->status)
        {
            case (null):
            case (1):
                return __('Pending');
            case (2):
                return __('Paid');
        }
    }

    /*
    |--------------------------------------------------------------------------
    | MUTATORS
    |--------------------------------------------------------------------------
    */

    public function setValueAttribute($value)
    {
        $this->attributes['value'] = $value * 100;
    }
}
