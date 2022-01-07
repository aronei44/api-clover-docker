<?php

namespace App\Models;

use App\Models\ChatMaster;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Chat extends Model
{
    use HasFactory;
    public function chat_master()
    {
        return $this->belongsTo(ChatMaster::class);
    }
}
