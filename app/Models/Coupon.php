<?php

namespace App\Models;

use App\Traits\PriceTrait;
use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;

/**
 * @mixin IdeHelperCoupon
 */
class Coupon extends Model
{
    use CrudTrait;
    use PriceTrait;

    protected $guarded = ['id'];
}
