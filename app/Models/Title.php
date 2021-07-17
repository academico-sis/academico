<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Title
 *
 * @property int $id
 * @property string $title
 * @method static \Illuminate\Database\Eloquent\Builder|Title newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Title newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Title query()
 * @method static \Illuminate\Database\Eloquent\Builder|Title whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Title whereTitle($value)
 * @mixin \Eloquent
 */
class Title extends Model
{
    protected $guarded = ['id'];
    public $timestamps = false;
}
