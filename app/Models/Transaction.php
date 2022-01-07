<?php

namespace App\Models;

use App\Models\User;
use App\Models\Product;
use App\Models\DetailTransaction;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Transaction extends Model
{
    use HasFactory;
    protected $guarded = ['id'];
    public function user(){
        return $this->belongsTo(User::class);
    }
    public function detail_transactions(){
        return $this->hasMany(DetailTransaction::class,'transaction_id');
    }
    public function product(){
        return $this->belongsTo(Product::class);
    }
}
