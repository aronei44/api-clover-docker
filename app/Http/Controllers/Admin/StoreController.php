<?php

namespace App\Http\Controllers\Admin;

use App\Models\Store;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class StoreController extends Controller
{
    public function index(){
        return response()->json([
            'message'=>'success',
            'data'=>Store::with(['user'])->all()
        ],200);
    }
}
