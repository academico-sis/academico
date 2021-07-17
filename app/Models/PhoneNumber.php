<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

/**
 * App\Models\PhoneNumber
 *
 * @property int $id
 * @property int $phoneable_id
 * @property string $phoneable_type
 * @property string $phone_number
 * @property-read \Illuminate\Database\Eloquent\Collection|\Spatie\Activitylog\Models\Activity[] $activities
 * @property-read int|null $activities_count
 * @property-read Model|\Eloquent $phoneable
 * @method static \Illuminate\Database\Eloquent\Builder|PhoneNumber newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PhoneNumber newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PhoneNumber query()
 * @method static \Illuminate\Database\Eloquent\Builder|PhoneNumber whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PhoneNumber wherePhoneNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PhoneNumber wherePhoneableId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PhoneNumber wherePhoneableType($value)
 * @mixin \Eloquent
 */
class PhoneNumber extends Model
{
    use LogsActivity;

    public $timestamps = false;
    protected $guarded = ['id'];
    protected static $logUnguarded = true;

    public function identifiableAttribute()
    {
        return $this->phone_number;
    }

    public function phoneable()
    {
        return $this->morphTo();
    }
}
