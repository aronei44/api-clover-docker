<?php

namespace App\Http\Controllers;

use App\Models\Chat;
use App\Models\ChatMaster;
use Illuminate\Http\Request;
use App\Events\MessageNotification;

class ChatController extends Controller
{
    public function store(Request $request,$id)
    {
        $chats = ChatMaster::find($id);

        if($request->user()->id == $chats->user1_id || $request->user()->id == $chats->user2_id)
        {
            try {
                Chat::create([
                    "chat_id"=>$id,
                    "message"=>$request->message
                ]);
                new MessageNotification($chat);
                return response()->json([
                    "message"=>"message added successfully",
                    "data"=>ChatMaster::find($id)->with('chats')
                ],200);
            } catch (\Throwable $th) {
                return response()->json([
                    "message"=>"message failed to add"
                ],400);
            }
        }
        else
        {
            return response()->json([
                "message"=>"Something wrong"
            ],401);
        }
    }
}
