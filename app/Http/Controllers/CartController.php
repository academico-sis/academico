<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cart;
use App\Models\User;

class CartController extends Controller
{

    /**
     * Display the specified user cart.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $products = Cart::get_user_cart($id);
        $student = User::student()->find($id);
        return view('carts.show', compact('products', 'student'));
    }


}
