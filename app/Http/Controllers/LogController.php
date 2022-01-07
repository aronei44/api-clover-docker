<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Arr;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\MailController;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\PhoneVerifiedController;
use Illuminate\Support\Facades\Mail;
use App\Mail\SendMail;
use Illuminate\Support\Carbon;


// use Illuminate\Foundation\Auth\User;

class LogController extends Controller
{
    public function logout(Request $request){

        try {
            $tokens = DB::select("DELETE FROM personal_access_tokens WHERE tokenable_id = " .$request->user()->id);
            return response()->json([
                'message'=>'Success Deleting Token'
            ],200);
        } catch (\Throwable $th) {
            return response()->json([
                'message'=>'Unable to Deleting Token'
            ],401);
        }
    }
    public function login(Request $request){
        if($request->username){
            $user = User::where('username',$request->username)->first();
        }else if($request->email){
            $user = User::where('email',$request->email)->first();
        }else if($request->handphone){
            $user = User::where('handphone',$request->handphone)->first();
        }
        if(!$user || !Hash::check($request->password, $user->password)){
            return response()->json([
                'message'=>'Unauthorized'
            ],401);
        }
        $token = $user->createToken('log-token')->plainTextToken;
        return response()->json([
            'message'=>'Success',
            'data'=>User::with(['role','store'])->find($user->id),
            'token'=>$token
        ],200);
    }

    public function register(Request $request)
    {
        $message =
        [
            'username.required' => 'Kolom username tidak boleh kosong !',
            'username.unique' => 'Username sudah ada, mohon cari username lain !',
            'username.min' => 'Minimal 8 Karakter !',
            'username.max' => 'Maximal 45 karakter !',
            'email.email' => 'Format email salah, mohon cek ulang !',
            'email.unique' => 'Email sudah digunakan, mohon gunakan email lain !',
            'handphone.unique' => 'No Telepon sudah digunakan, mohon gunakan nomor lain !',
            'handphone.min' => 'Minimal 11 Angka !',
            'handphone.max' => 'Maximal 15 Angka !',
            'password.required' => 'Kolom password tidak boleh kosong !',
            'password.min' => 'Minimal 8 karakter !'
        ];

        $validator = Validator::make($request->all(), [
            'username' => 'required|unique:users|max:45|min:8',
            'email' => 'email|unique:users',
            'handphone' => 'unique:users|max:15|min:11',
            'password' => 'required|min:8'
        ], $message);

        if ($validator->fails()) {
            return response()->json([
                'message'=>$validator->errors()
            ],401);
        } else {
            if ($request->email) {
                $name = explode(' ',$request->fullname);
                $firstname = $name[0];
                unset($name[0]);
                $lastname = implode(' ',$name);
                try {
                    $arr = [0,1,2,3,4,5,6,7,8,9];
                    $arr = implode('',Arr::random($arr, 6));
                    $user = User::create([
                        'username' => $request->username,
                        'email' => $request->email,
                        'password' => Hash::make($request->password),
                        'email_otp' => $arr,
                        'fullname' => $request->fullname,
                        'firstname'=> $firstname,
                        'lastname'=>$lastname,
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
                        'message'=>$validator->errors()
                    ],401);
                }
            } elseif ($request->handphone) {
                try {
                    $name = explode(' ',$request->fullname);
                    $firstname = $name[0];
                    unset($name[0]);
                    $lastname = implode(' ',$name);
                    $arr = [0,1,2,3,4,5,6,7,8,9];
                    $arr = implode('',Arr::random($arr, 6));
                    $user = User::create([
                        'fullname' => $request->fullname,
                        'firstname'=> $firstname,
                        'lastname'=>$lastname,
                        'username' => $request->username,
                        'handphone' => $request->handphone,
                        'password' => Hash::make($request->password),
                        'phone_otp'=>$arr
                    ]);
                    try {
                        PhoneVerifiedController::auth($user);
                    } catch (\Throwable $th) {

                    }
                    Role::create([
                        'user_id' => $user->id,
                        'role' => "User"
                    ]);
                    $token = $user->createToken('log-token')->plainTextToken;

                    return response()->json([
                        'message'=>'Success',
                        'data'=>User::with('role')->find($user->id),
                        'token'=>$token
                    ],201);
                } catch (\Throwable $th) {
                     return response()->json([
                        'message'=>$validator->errors()
                    ],401);
                }
            }else{
                 return response()->json([
                    'message'=>$validator->errors()
                ],401);
            }
        }
    }

    public function mobileRegister(Request $request)
    {
         $message =
        [
            'username.required' => 'Kolom username tidak boleh kosong !',
            'username.unique' => 'Username sudah ada, mohon cari username lain !',
            'username.min' => 'Minimal 8 Karakter !',
            'username.max' => 'Maximal 45 karakter !',
            'email.email' => 'Format email salah, mohon cek ulang !',
            'email.unique' => 'Email sudah digunakan, mohon gunakan email lain !',
            'handphone.unique' => 'No Telepon sudah digunakan, mohon gunakan nomor lain !',
            'handphone.min' => 'Minimal 11 Angka !',
            'handphone.max' => 'Maximal 15 Angka !',
            'password.required' => 'Kolom password tidak boleh kosong !',
            'password.min' => 'Minimal 8 karakter !'
        ];

        $validator = Validator::make($request->all(), [
            'username' => 'required|unique:users|max:45|min:8',
            'email' => 'email|unique:users',
            'handphone' => 'unique:users|max:15|min:11',
            'password' => 'required|min:8'
        ], $message);

        if ($validator->fails()) {
            return response()->json([
                'message'=>$validator->errors()
            ],401);
        } else {
            if ($request->email) {
                $name = explode(' ',$request->fullname);
                $firstname = $name[0];
                unset($name[0]);
                $lastname = implode(' ',$name);
                try {
                    $arr = [0,1,2,3,4,5,6,7,8,9];
                    $arr = implode('',Arr::random($arr, 6));
                    $user = User::create([
                        'username' => $request->username,
                        'email' => $request->email,
                        'password' => Hash::make($request->password),
                        'email_otp' => $arr,
                        'fullname' => $request->fullname,
                        'firstname'=> $firstname,
                        'lastname'=>$lastname,
                    ]);
                    Role::create([
                        'user_id' => $user->id,
                        'role' => "User"
                    ]);
                    MailController::mobileSendEmail($user);
                    $token = $user->createToken('log-token')->plainTextToken;

                    return response()->json([
                        'message'=>'Success',
                        'data'=>User::with('role')->find($user->id),
                        'token'=>$token
                    ],201);
                } catch (\Throwable $th) {
                     return response()->json([
                        'message'=>$validator->errors()
                    ],401);
                }
            } elseif ($request->handphone) {
                try {
                    $name = explode(' ',$request->fullname);
                    $firstname = $name[0];
                    unset($name[0]);
                    $lastname = implode(' ',$name);
                    $arr = [0,1,2,3,4,5,6,7,8,9];
                    $arr = implode('',Arr::random($arr, 6));
                    $user = User::create([
                        'fullname' => $request->fullname,
                        'firstname'=> $firstname,
                        'lastname'=>$lastname,
                        'username' => $request->username,
                        'handphone' => $request->handphone,
                        'password' => Hash::make($request->password),
                        'phone_otp'=>$arr
                    ]);
                    try {
                        PhoneVerifiedController::auth($user);
                    } catch (\Throwable $th) {

                    }
                    Role::create([
                        'user_id' => $user->id,
                        'role' => "User"
                    ]);
                    $token = $user->createToken('log-token')->plainTextToken;

                    return response()->json([
                        'message'=>'Success',
                        'data'=>User::with('role')->find($user->id),
                        'token'=>$token
                    ],201);
                } catch (\Throwable $th) {
                     return response()->json([
                        'message'=>$validator->errors()
                    ],401);
                }
            }else{
                 return response()->json([
                    'message'=>$validator->errors()
                ],401);
            }
        }
    }

    public function update_password(Request $request){
        $user = $request->user();
        if(Hash::check($request->password, $user->password)){
            if($request->password != $request->new_password ){
                $user->update([
                    'password'=>Hash::make($request->new_password)
                ]);
                MailController::change_password($user);
                return response()->json([
                    'message'=>"Password Sukses Diganti"
                ],200);
            }else{
                return response()->json([
                    'message'=>"Password Tidak Boleh Sama Dengan Yang Sebelumnya"
                ],401);
            }

        }else{
            return response()->json([
                'message'=>"Password Salah"
            ],401);
        }
    }
    public function forgot_password(Request $request){
        $user = User::where('email',$request->email)->first();
        if($user){
            $arr = [0,1,2,3,4,5,6,7,8,9];
            $arr = implode('',Arr::random($arr, 6));
            MailController::forgot_password($user,$arr);
            $user->update([
                'email_otp'=>$arr
            ]);
            return response()->json([
                'message'=>"Kode OTP dikirim",
                'user'=>User::find($user->id)
            ],200);
        }else{
            return response()->json([
                'message'=>"Email Tidak Ditemukan"
            ],404);
        }
    }
    public function verif_email_otp(Request $request,$id){
        $user = User::find($id);
        $dateCarbon = Carbon::create($user->updated_at);
        $dateExpire =  $dateCarbon->addMinute(5)->format("d F Y H:i");
        $dateNow = Carbon::now()->format("d F Y H:i");
        if ($user->email_otp == $request->otp) {
            if ($dateNow > $dateExpire) {
                return response()->json([
                    'message' => 'OTP Code already expired',
                    'valid' => false
                ]);
            } else {
                return response()->json([
                    'message'=>'OTP match',
                    'valid'=>true
                ],200);
            }
        } else {
            return response()->json([
                'message'=>"OTP didn't match",
                'valid'=>false
            ]);
        }

        // if($user->email_otp == $request->otp){
        //     return response()->json([
        //         'message'=>'OTP match',
        //         'valid'=>true
        //     ],200);
        // }
        // return response()->json([
        //     'message'=>"OTP didn't match",
        //     'valid'=>false
        // ],403);
    }
    public function confirm_password(Request $request,$id){
        // return $request->user();
        $user = User::find($id);
        try {
            $user->update([
                'password'=>Hash::make($request->password)
            ]);
            MailController::change_password($user);
            return response()->json([
                'message'=>"Password Sukses Diganti"
            ],200);
        } catch (\Throwable $th) {
            return response()->json([
                'message'=>"Unauthorized"
            ],403);
        }

    }
    public function verif_handphone(Request $request){
        $user = User::where('handphone',$request->handphone)->first();
        if($user){
            if($user->phone_otp == $request->phone_otp){
                $user->update([
                    'phone_verified_at'=>date('Y-m-d H:i:s')
                ]);
                return response()->json([
                    'message'=>"Success. Phone Verified"
                ],200);
            }else{
                return response()->json([
                    'message'=>"Failed. Wrong OTP"
                ],400);
            }
        }else{
            return response()->json([
                'message'=>"Failed. Wrong Handphone"
            ],403);
        }
    }
    public function update_account(Request $request){
        $user = User::findOrFail($request->user()->id);
        $message = [
            'email.unique' => 'Email tidak boleh sama',
            'handphone.unique' => 'Nomor handphone tidak boleh sama'
        ];

        $validator = Validator::make($request->all(), [
            'email' => 'unique:users|nullable',
            'handphone' => 'unique:users|nullable'
        ], $message);

        if ($validator->fails()) {
            return response()->json([
                'message' => $validator->errors(),
            ]);
        } else {
            try {
                $arr = [0,1,2,3,4,5,6,7,8,9];
                $arr = implode('',Arr::random($arr, 6));
                if ($request->email != null) {
                    if ($user->email != $request->email) {
                         $user->update([
                            'email'=>$request->email,
                            'email_otp'=>$arr,
                            'email_verified_at' => null
                        ]);
                        $tokens = DB::select("DELETE FROM personal_access_tokens WHERE tokenable_id = " .$request->user()->id);$tokens = DB::select("DELETE FROM personal_access_tokens WHERE tokenable_id = " .$request->user()->id);
                        MailController::auth($user);
                    }
                }

                if ($request->handphone != null) {
                    if ($user->handphone != $request->hanphone) {
                        $user->update([
                            'handphone'=>$request->handphone,
                            'phone_otp'=>$arr,
                            'phone_verified_at' => null
                        ]);
                        $tokens = DB::select("DELETE FROM personal_access_tokens WHERE tokenable_id = " .$request->user()->id);
                        PhoneVerifiedController::auth($user);
                    }
                }

                return response()->json([
                    'message'=>'account updated',
                    'data'=>$user
                ],200);
            } catch (\Throwable $th) {
                return response()->json([
                    'message'=>'account failed to update'
                ],400);
            }
        }
    }
    public function roles(Request $request){
        try {
            $role = Role::where('user_id',$request->user()->id)->first();
            return response()->json([
                "message"=>"Success",
                "data"=>$role
            ],200);
        } catch (\Throwable $th) {
            return response()->json([
                "message"=>"Unauthorized",
            ],401);
        }
    }

    public function resendEmail(Request $request)
    {
        try {
            $user = User::find($request->user()->id);
            $arr = [0,1,2,3,4,5,6,7,8,9];
            $arr = implode('',Arr::random($arr, 6));

            $user->update([
                'email_otp' => $arr,
            ]);

            $details = [
                'username'=>$user['username'],
                'email'=>$user['email'],
                'link'=>env('APP_URL').'/'.$user['email'].'/'.$user['username'].'/'.$user['id'].'/'.$user['email_otp'],
            ];
            Mail::to($user['email'])->send(new SendMail($details,'welcome'));
            return $details;
        } catch (\Throwable $th) {
            return $th;
            return response()->json([
                'message' => 'Failed to send email'
            ]);
        }
    }

    public function resendOtpEmail(Request $request)
    {
        try {
            $user = User::where($request->email);
            $arr = [0,1,2,3,4,5,6,7,8,9];
            $arr = implode('',Arr::random($arr, 6));

            $user->update([
                'email_otp' => $arr,
            ]);

            MailController::forgot_password($user,$arr);

            return response()->json([
                'message' => 'Success',
                'data' => $user
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'Failed to send OTP code'
            ]);
        }
    }
}
