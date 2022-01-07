<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CartController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        // $user = $request->user()->id;
        return response()->json([
            "message"=>"Success",
            "data"=>Cart::with('product')->where('user_id',$request->user()->id)->get()
        ],200);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try {
            $cart = Cart::create([
                'user_id'=>$request->user()->id,
                'product_id'=>$request->product_id
            ]);
            return response()->json([
                "message"=>"Success",
                "data"=>$cart
            ],201);
        } catch (\Throwable $th) {
            return response()->json([
                "message"=>"Failed",
            ],400);
        }

    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Cart  $cart
     * @return \Illuminate\Http\Response
     */
    public function show(Cart $cart)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Cart  $cart
     * @return \Illuminate\Http\Response
     */
    public function edit(Cart $cart)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Cart  $cart
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Cart $cart)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Cart  $cart
     * @return \Illuminate\Http\Response
     */
    // public function destroy(Cart $cart)
    // {
    //     $cart->delete();
    //     return response()->json([
    //         "message"=>"Success Deleted",
    //     ],200);
    // }
    public function destroy(Cart $cart)
    {
        try {
            $cart->delete();
            return response()->json([
                "message"=>"Success Deleted",
            ],200);
        } catch (\Throwable $th) {
            return response()->json([
                "message"=>"delete Failed",
            ],400);
        }

    }
}
