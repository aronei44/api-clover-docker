<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ReviewProduct;

class ReviewProductController extends Controller
{
    public function store(Request $request){
        $request->validate([
            'product_id'=>'required|numeric',
            'review_score'=>'required|numeric',
            'review_comment'=>'required'
        ]);
        try {
            //code...
            $review = ReviewProduct::create([
                'product_id'=>$request->product_id,
                'user_id'=>$request->user()->id,
                'review_score'=>$request->review_score,
                'review_comment'=>$request->review_comment
            ]);
            return response()->json([
                'message'=>'success',
                'data'=>$review
            ],201);
        } catch (\Throwable $th) {
            //throw $th;
            return response()->json([
                'message'=>'failed',
            ],400);
        }
    }
}
