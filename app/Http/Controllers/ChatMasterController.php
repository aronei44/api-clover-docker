<?php

namespace App\Http\Controllers;

use App\Models\ChatMaster;
use Illuminate\Http\Request;

class ChatMasterController extends Controller
{
    public function index(Request $request)
    {
        $chats = ChatMaster::where('user1_id', $request->user()->id)
                            ->where('user2_id', $request->id)
                            ->get();
        if(!$chats)
        {
            $chats = ChatMaster::where('user2_id', $request->user()->id)
                                ->where('user1_id', $request->id)
                                ->get();
        }
        if(!$chats)
        {
            $chats = ChatMaster::create([
                'user1_id'=>$request->user()->id,
                'user2_id'=>$request->id
            ]);
        }
        return response()->json([
            "message"=>"Chat Founded /  Added",
            "data"=>ChatMaster::find($chats)->with('chats')
        ],200);
    }
}
