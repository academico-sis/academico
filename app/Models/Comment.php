<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Comment extends Model
{
    use CrudTrait;

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

    public function author()
    {
        return $this->belongsTo(User::class);
    }
}
