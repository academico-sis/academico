<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Spatie\Translatable\HasTranslations;

class LeaveType extends Model
{
    use HasTranslations;
    use CrudTrait;

    protected $guarded = ['id'];
    public $timestamps = false;
    public $translatable = ['name'];


}
