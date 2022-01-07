<?php

namespace App\Models;

use App\Models\Cart;
use App\Models\Role;
use App\Models\Store;
use App\Models\Product;
use App\Models\ChatMaster;
use App\Models\ProfileKYC;
use App\Models\Transaction;
use App\Models\ProfilePhoto;
use App\Models\ReviewProduct;

use App\Models\ReviewStandardKyc;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $guarded = ['id'];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function role()
    {
        return $this->hasOne(Role::class);
    }

    public function profile_photo()
    {
        return $this->hasOne(ProfilePhoto::class);
    }

    public function profile_review()
    {
        return $this->hasOne(ReviewStandardKyc::class);
    }

    public function profile_k_y_c()
    {
        return $this->hasOne(ProfileKYC::class);
    }

    public function transactions(){
        return $this->hasMany(Transaction::class);
    }

    public function carts(){
        return $this->hasMany(Cart::class);
    }
    public function chat_masters(){
        return $this->hasMany(ChatMaster::class);
    }
    public function store()
    {
        return $this->hasOne(Store::class);
    }
    public function review_products(){
        return $this->hasMany(ReviewProduct::class);
    }
}
