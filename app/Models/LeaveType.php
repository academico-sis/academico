<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

/**
 * @mixin IdeHelperLeaveType
 */
class LeaveType extends Model
{
    use HasTranslations;
    use CrudTrait;

    protected $guarded = ['id'];

    public $timestamps = false;

    public array $translatable = ['name'];
}
