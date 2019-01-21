<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{

    protected $fillable = ['user_id', 'product_id', 'product_type'];

    /** This method allows to define "default" products that will need to be added to every cart
     * For instance, in our case, an administrative Fee (seeded in the DB with the ID of 1)
     */
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

    /** returns the products added to the specified user's cart */
    public static function get_user_cart($id)
    {
        return Cart::where('user_id', $id)->with('product')->get();
    }

    /** we use a polymorphic relation because a Cart model can be linked to an enrollment, a book, a fee... */
    public function product()
    {
        return $this->morphTo();
    }

    /** triggered when the billing process has been completed  */
    public static function clear_cart_for_user($id)
    {
        Cart::where('user_id', $id)->delete();
    }

}
