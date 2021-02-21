<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Course;
use Carbon\Carbon;

class Partner extends Model
{
    use \Backpack\CRUD\app\Models\Traits\CrudTrait;
    use HasFactory;

    protected $guarded = ['id'];

    public function courses()
    {
        return $this->hasMany(Course::class);
    }

    public function getFormattedStartDateAttribute()
    {
        if (!$this->started_on) {
            return '-';
        }

        return Carbon::parse($this->started_on)->locale(app()->getLocale())->isoFormat('Do MMM YYYY');
    }

    public function getFormattedEndDateAttribute()
    {
        if (!$this->expired_on) {
            return '-';
        }

        return Carbon::parse($this->expired_on)->locale(app()->getLocale())->isoFormat('Do MMM YYYY');
    }
}
