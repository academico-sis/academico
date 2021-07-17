<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Course;
use Carbon\Carbon;

/**
 * App\Models\Partner
 *
 * @property int $id
 * @property string $name
 * @property string|null $started_on
 * @property string|null $expired_on
 * @property int|null $send_report_on
 * @property string|null $last_alert_sent_at
 * @property int|null $auto_renewal
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|Course[] $courses
 * @property-read int|null $courses_count
 * @property-read mixed $formatted_end_date
 * @property-read mixed $formatted_start_date
 * @method static \Illuminate\Database\Eloquent\Builder|Partner newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Partner newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Partner query()
 * @method static \Illuminate\Database\Eloquent\Builder|Partner whereAutoRenewal($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Partner whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Partner whereExpiredOn($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Partner whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Partner whereLastAlertSentAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Partner whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Partner whereSendReportOn($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Partner whereStartedOn($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Partner whereUpdatedAt($value)
 * @mixin \Eloquent
 */
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
