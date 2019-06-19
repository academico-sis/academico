<?php

namespace App\Models;

use App\Models\Student;
use Backpack\CRUD\CrudTrait;
use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{

    use CrudTrait;

    protected $fillable = ['student_id'];

    /** This method allows to define "default" products that will need to be added to every cart
     * For instance, in our case, an administrative Fee (seeded in the DB with the ID of 1)
     */
    public static function add_default_products($student)
    {

        // todo make this configurable

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

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    /** we use a polymorphic relation because a Cart model can be linked to an enrollment, a book, a fee... */

    public function course()
    {
        return $this->morphedByMany(Course::class, 'product', 'cart_product');
    }


    public function fee()
    {
        return $this->morphedByMany(Fee::class, 'product', 'cart_product');
    }

    
    public function book()
    {
        return $this->morphedByMany(Book::class, 'product', 'cart_product');
    }


    /** triggered when the billing process has been completed  */
    public static function clear_cart_for_user(Student $id)
    {
        Cart::where('student_id', $id)->delete();
    }

}
