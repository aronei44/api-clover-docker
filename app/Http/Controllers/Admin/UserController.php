<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\ProfileKYC;
use Google\Service\Analytics\Profile;

class UserController extends Controller
{
    public function index(){
        return response()->json([
            'message'=>'success',
            'data'=>User::all()
        ],200);
    }

    public function kycApproved($id)
    {
        // $user = User::find($id);
        $profileKyc = ProfileKYC::where('user_id', $id)->first();

        try {
            $profileKyc->update([
                'is_approved' => true
            ]);

            return response()->json([
                'message' => 'Success'
            ]);
        } catch (\Throwable $th) {
            return $th;
            return response()->json([
                'message' => 'Failed'
            ]);
        }
    }
}
