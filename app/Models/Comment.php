<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\SoftDeletes;

class Comment extends Model
{

    protected $guarded = ['id'];

    use SoftDeletes;

    public function getDateAttribute()
    {
        return Carbon::parse($this->updated_at, 'UTC')->toFormattedDateString();
    }

    public function commentable()
    {
        return $this->morphTo();
    }
}
