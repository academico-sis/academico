<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

class Comment extends Model
{
    use CrudTrait;
    use LogsActivity;

    protected $guarded = ['id'];
    protected static $logUnguarded = true;

    public function getDateAttribute()
    {
        return Carbon::parse($this->updated_at, 'UTC')->toFormattedDateString();
    }

    public function commentable()
    {
        return $this->morphTo();
    }

    public function author()
    {
        return $this->belongsTo(User::class);
    }
}
