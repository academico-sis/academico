<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\User;
use App\Models\Student;
use Illuminate\Http\Request;

class CartController extends Controller
{

    /**
     * Display the specified user cart.
     */
    public function show($id)
    {
        $this->middleware(['permission:enrollments.view']);

        $products = Cart::get_user_cart($id);
        $student = Student::where('user_id', $id)->firstOrFail();
        return view('carts.show', compact('products', 'student'));
    }


}
