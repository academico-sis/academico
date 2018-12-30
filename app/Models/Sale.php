<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sale extends Model
{

    protected $fillable = ['user_id', 'product_id', 'product_type'];

    public static function add_default_products($student)
    {
        $product = Sale::firstOrNew([
            'user_id' => $student,
            'product_id' => 1,
            'product_type' => Fee::class
        ]);

        $product->save();
        return $product->id;
    }

    public function books()
    {
        return $this->morphedByMany('App\Models\Book', 'product');
    }

    public function fees()
    {
        return $this->morphedByMany('App\Models\Fee', 'product');
    }

}
