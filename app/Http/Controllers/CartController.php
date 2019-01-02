<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cart;
use App\Models\User;

class CartController extends Controller
{
    /**
     * Display the carts for all users
     * 
     * Monitoring purposes
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

    }

    /**
     * Add the product to the cart for checkout
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Enrollment $enrollment)
    {
         
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

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


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Clear the specified cart.
     * 
     * For instance after an preinvoice has been generated.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        Cart::destroy($id);
    }
}
