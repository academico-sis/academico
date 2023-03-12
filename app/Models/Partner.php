<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Partner extends Model
{
    use CrudTrait;

    protected $guarded = ['id'];

    public function courses()
    {
        return $this->hasMany(Course::class);
    }

    public function getFormattedStartDateAttribute()
    {
        if (! $this->started_on) {
            return '-';
        }

        return Carbon::parse($this->started_on)->locale(app()->getLocale())->isoFormat('Do MMM YYYY');
    }

    public function getFormattedEndDateAttribute()
    {
        if (! $this->expired_on) {
            return '-';
        }

        return Carbon::parse($this->expired_on)->locale(app()->getLocale())->isoFormat('Do MMM YYYY');
    }
}
