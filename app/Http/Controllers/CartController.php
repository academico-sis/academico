<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cart;
use App\Models\User;

class CartController extends Controller
{

    /**
     * Display the specified user cart.
     */
    public function show($id)
    {
        $this->middleware(['permission:enrollments.view']);

        $products = Cart::get_user_cart($id);
        $student = User::student()->find($id);
        return view('carts.show', compact('products', 'student'));
    }


}
