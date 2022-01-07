<?php

namespace App\Models;

use App\Models\Cart;
use App\Models\Photo;
use App\Models\Store;
use App\Models\Category;
use App\Models\ReviewProduct;
use App\Models\DetailTransaction;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Product extends Model
{
    use SoftDeletes;

    protected $guarded = ['id'];

    public function cart(){
        return $this->hasMany(Cart::class);
    }
    public function detail_transactions(){
        return $this->hasMany(DetailTransaction::class);
    }
    public function photos(){
        return $this->hasMany(Photo::class);
    }
    public function category(){
        return $this->belongsTo(Category::class);
    }
    public function sub_category(){
        return $this->belongsTo(SubCategory::class);
    }
    public function store(){
        return $this->belongsTo(Store::class);
    }
    public function review_products(){
        return $this->hasMany(ReviewProduct::class);
    }
}
