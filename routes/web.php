<?php

use App\Models\User;
use Illuminate\Support\Carbon;
use App\Events\MessageNotification;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\CartController;
use App\Http\Controllers\MailController;
use App\Http\Controllers\GoogleAuthController;
use App\Http\Controllers\TransactionController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/{email}/{username}/{id}/{unique_code}', function ($email, $username, $id, $unique_code){
    $user = User::findOrFail($id);
    $dateCarbon = Carbon::create($user->updated_at);
    $dateExpire =  $dateCarbon->addMinute(5)->format("d F Y H:i");
    $dateNow = Carbon::now()->format("d F Y H:i");
    // return $user;
    if ($user->email == $email && $user->username == $username) {
        if ($user->email_otp == $unique_code) {
            if ($dateNow > $dateExpire) {
                return "Link already expired";
            } else {
                $user->update([
                    'email_verified_at' => date('Y-m-d H:i:s')
                ]);
                return "Akun kamu berhasil di verifikasi";
            }
        } else {
            return "Link salah";
        }
    } else {
        return "Akun gagal di verifikasi";
    }

    // if ($user->email == $email && $user->username == $username && $user->email_otp == $unique_code) {
    //     $user->update([
    //         'email_verified_at' => date('Y-m-d H:i:s')
    //     ]);
    //     return redirect('http://localhost:3000');
    // }
    // // return env('SANCTUM_STATEFUL_DOMAINS');
    // return 'akun gagal diverifikasi';
});

Route::get('/auth', [GoogleAuthController::class,'auth']);

Route::get('/auth/redirect', [GoogleAuthController::class,'redirect']);

Route::get('test', function() {
    Storage::disk('google')->put('test3.txt', 'Hello World');
    $url = Storage::disk('google')->url('test3.txt');
    return $url;
});

Route::get('/template', function () {
    return view('mail.Layouts.mainEmail');
});

Route::get('/success',[TransactionController::class,'updatePembayaran']);
