<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{

    protected $fillable = ['user_id', 'product_id', 'product_type'];

    public static function add_default_products($student)
    {
        $product = Cart::firstOrNew([
            'user_id' => $student,
            'product_id' => 1,
            'product_type' => Fee::class
        ]);

        $product->save();
        return $product->id;
    }

    public static function get_user_cart($id)
    {
        return Cart::where('user_id', $id)->with('product')->get();
    }

    /**
     * returns the products registered in the cart
     *
     * @return void
     */
    public function product()
    {
        return $this->morphTo();
    }

    public static function clear_cart_for_user($id)
    {
        Cart::where('user_id', $id)->delete();
    }

}
