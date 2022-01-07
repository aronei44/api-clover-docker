<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\MailController;
use Laravel\Socialite\Facades\Socialite;

class GoogleAuthController extends Controller
{
    public function auth(){
        return Socialite::driver('google')->redirect();
    }
    public function redirect(){
        $user = Socialite::driver('google')->stateless()->user();
        $registered_user = User::where('google_id', $user->id)->first();
        if($registered_user){
            $token = $registered_user->createToken('log-token')->plainTextToken;
            return redirect(env("DOMAIN")."/auth/redirect/".$registered_user->google_id);
        }
        try {
            $is_user = User::where('email',$user->email)->first();

            if($is_user != Null){
                $is_user->update([
                    'google_id'=>$user->id
                ]);

            }else{
                $is_user = User::create([
                    'username' => $user->name,
                    'email' => $user->email,
                    'password' => Hash::make($user->name),
                    'google_id'=>$user->id
                ]);
                MailController::auth($user);
                Role::create([
                    'user_id' => $is_user->id,
                    'role' => "User"
                ]);
            }
            return redirect(env("DOMAIN")."/auth/redirect/".$is_user->google_id);

            $token = $is_user->createToken('log-token')->plainTextToken;

        } catch (\Throwable $th) {
            $saveUser = User::where('email',$user->email)->first();
            Role::create([
                'user_id' => $saveUser->id,
                'role' => "User"
            ]);
            return redirect(env("DOMAIN")."/auth/redirect/".$saveUser->google_id);

        }

    }
    public function getOauth($google_id){
        try {
            //code...
            $saveUser = User::firstWhere('google_id',$google_id);
            $token = $saveUser->createToken('log-token')->plainTextToken;
            return response()->json([
                'message'=>'Success catch',
                'data'=>User::with('role')->find($saveUser->id),
                'token'=>$token
            ],200);
        } catch (\Throwable $th) {
            //throw $th;
            return response()->json([
                'message'=>'Not Found'
            ],400);
        }
    }


    public function signGoogle(Request $request){
        $user = User::where('google_id',$request->id)->first();
        if($user){
            $token = $user->createToken('log-token')->plainTextToken;
            return response()->json([
                'message'=>'Success',
                'data'=>User::with('role')->find($user->id),
                'token'=>$token
            ],200);
        }else{
            $user = User::where('email',$request->email)->first();
            if($user){
                $user->update([
                    'google_id'=>$request->id
                ]);
                $token = $user->createToken('log-token')->plainTextToken;
                return response()->json([
                    'message'=>'Success',
                    'data'=>User::with('role')->find($user->id),
                    'token'=>$token
                ],200);
            }else{
                try {
                    $user = User::create([
                        'username' => $request->username,
                        'email' => $request->email,
                        'password' => Hash::make($request->username),
                        'google_id'=> $request->id
                    ]);
                    Role::create([
                        'user_id' => $user->id,
                        'role' => "User"
                    ]);
                    MailController::auth($user);
                    $token = $user->createToken('log-token')->plainTextToken;
                    return response()->json([
                        'message'=>'Success',
                        'data'=>User::with('role')->find($user->id),
                        'token'=>$token
                    ],201);
                } catch (\Throwable $th) {
                    return response()->json([
                        'message'=>'Failed'
                    ],401);
                }
            }
        }
    }
}
