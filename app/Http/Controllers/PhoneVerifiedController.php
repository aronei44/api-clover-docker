<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Nexmo\Laravel\Facade\Nexmo;


class PhoneVerifiedController extends Controller
{
    public static function auth($user){
        Nexmo::message()->send([
            'to'=>$user->handphone,
            'from'=>'Clover',
            'text'=>'Kode OTP anda '. $user->phone_otp .'. silahkan tambahkan untuk verifikasi no hp anda'
        ]);
    }
}
