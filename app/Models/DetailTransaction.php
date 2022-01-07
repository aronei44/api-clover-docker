<?php

namespace App\Models;

use App\Models\Product;
use App\Models\Transaction;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class DetailTransaction extends Model
{
    use HasFactory;
    protected $guarded = ['id'];
    public function transaction(){
        return $this->belongsTo(Transaction::class);
    }
}
