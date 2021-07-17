<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

/**
 * App\Models\LeaveType
 *
 * @property int $id
 * @property array $name
 * @property-read array $translations
 * @method static \Illuminate\Database\Eloquent\Builder|LeaveType newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|LeaveType newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|LeaveType query()
 * @method static \Illuminate\Database\Eloquent\Builder|LeaveType whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LeaveType whereName($value)
 * @mixin \Eloquent
 */
class LeaveType extends Model
{
    use HasTranslations;
    use CrudTrait;

    protected $guarded = ['id'];
    public $timestamps = false;
    public $translatable = ['name'];
}
