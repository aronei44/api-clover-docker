<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\User;
use App\Models\ProfileKYC;
use App\Models\ProfilePhoto;
use App\Models\ReviewStandardKyc;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;


class ProfileKYCController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return 'test';
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // return $request->all();
        $message = [
            'kyc_name.required'=>'Kolom Nama Harus Diisi',
            'kyc_name.max'=>'Kolom Nama Harus maksimal 45 huruf',
            'kyc_ktp.required'=>'Kolom KTP harus diisi',
            'kyc_ktp.unique'=>'Nomor KTP Sudah teraftar, Harap menggunakan Nomor KTP yang belum terdaftar',
            'kyc_ktp.size'=>'Kolom KTP harus diisi 16 angka',
            'kyc_address.required'=>'Kolom Alamat Harus Diisi',
            'kyc_city.required'=>'Kolom Kota Harus Diisi',
            'kyc_postal_code.required'=>'Kolom Kode Pos Harus Diisi',
            'kyc_postal_code.numeric'=>'Kolom Kode Pos Harus Diisi Angka',
            'kyc_postal_code.size'=>'Kolom Kode Pos Harus Diisi 5 Angka',
            'kyc_place_of_birth.required'=>'Kolom tempat lahir harus diisi',
            'kyc_date_of_birth.required'=>'Kolom tanggal lahir harus diisi',
            'kyc_gender.required'=>'Kolom jenis kelamin harus diisi',
            'kyc_self_photo.required' => 'Foto diri harus di isi dengan sebenar-benarnya',
            'kyc_self_photo.mimes' => 'Format foto hanya boleh, JPEG, JPG, PNG, HEIC',
            'kyc_ktp_photo.required' => 'Foto KTP harus di isi dengan sebenar-benarnya',
            'kyc_ktp_photo.mimes' => 'Format foto hanya boleh, JPEG, JPG, PNG, HEIC',
            'kyc_province.required'=>'Kolom provinsi harus diisi',
            'kyc_province_id.required'=>'Kolom id provinsi harus diisi',
            'kyc_city_id.required'=>'Kolom id kota harus diisi',

        ];
        $validator = Validator::make($request->all(), [
            'kyc_name' => 'required|max:45',
            'kyc_ktp' => 'required|size:16|unique:profile_k_y_c_s,kyc_ktp',
            'kyc_address' => 'required',
            'kyc_city' => 'required',
            'kyc_postal_code'=>'required|size:5',
            'kyc_place_of_birth'=>'required',
            'kyc_date_of_birth'=>'required',
            'kyc_gender'=>'required',
            'kyc_self_photo' => 'required|mimes:jpeg,jpg,png,heic',
            'kyc_ktp_photo' => 'required|mimes:jpeg,jpg,png,heic',
            'kyc_province'=>'required',
            'kyc_province_id'=>'required',
            'kyc_city_id'=>'required'
        ], $message);
        if ($validator->fails()) {
            // return $validator->errors();
            return response()->json([
                'message'=>$validator->errors()
            ],400);
        }else{
            $date = explode('-',$request->kyc_date_of_birth);
            if($request->kyc_gender == "Perempuan"){
                $date[2] = (int)$date[2]+40;
            }
            $date = $date[2] . $date[1] . substr($date[0],2,2);
            $nik = substr($request->kyc_ktp,6,6);
            if($date != $nik){
                return response()->json([
                    'message'=>'ada kesalahan dalam nik atau tanggal lahir anda'
                ],400);
            }else{

                try {
                    $profile = ProfileKYC::create([
                        'kyc_name'=>$request->kyc_name,
                        'kyc_ktp'=>$request->kyc_ktp,
                        'kyc_address'=>$request->kyc_address,
                        'kyc_city'=>$request->kyc_city,
                        'kyc_postal_code'=>$request->kyc_postal_code,
                        'kyc_place_of_birth'=>$request->kyc_place_of_birth,
                        'kyc_date_of_birth'=>$request->kyc_date_of_birth,
                        'kyc_gender'=>$request->kyc_gender,
                        'user_id'=>$request->user()->id,
                        'kyc_province'=>$request->kyc_province,
                        'kyc_province_id'=>$request->kyc_province_id,
                        'kyc_city_id'=>$request->kyc_city_id
                    ]);

                    $this->storeImageVerification($request);

                    // $this->addReview($request);

                    return response()->json([
                        'message'=>'profile updated',
                        'data'=>$profile
                    ],201);
                } catch (\Throwable $th) {
                    return $th;
                    return response()->json([
                        'message'=>'profile failed to update'
                    ],400);
                }
            }
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\ProfileKYC  $profileKYC
     * @return \Illuminate\Http\Response
     */
    public function show(ProfileKYC $profileKYC)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\ProfileKYC  $profileKYC
     * @return \Illuminate\Http\Response
     */
    public function edit(ProfileKYC $profileKYC)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\ProfileKYC  $profileKYC
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, ProfileKYC $profileKYC)
    {
        $message = [
            'kyc_name.required'=>'Kolom Nama Harus Diisi',
            'kyc_name.max'=>'Kolom Nama Harus maksimal 45 huruf',
            'kyc_ktp.required'=>'Kolom KTP harus diisi',
            'kyc_ktp.unique'=>'Nomor KTP Sudah teraftar, Harap menggunakan Nomor KTP yang belum terdaftar',
            'kyc_ktp.size'=>'Kolom KTP harus diisi 16 angka',
            'kyc_address.required'=>'Kolom Alamat Harus Diisi',
            'kyc_city.required'=>'Kolom Kota Harus Diisi',
            'kyc_postal_code.required'=>'Kolom Kode Pos Harus Diisi',
            'kyc_postal_code.numeric'=>'Kolom Kode Pos Harus Diisi Angka',
            'kyc_postal_code.size'=>'Kolom Kode Pos Harus Diisi 5 Angka',
            'kyc_place_of_birth.required'=>'Kolom tempat lahir harus diisi',
            'kyc_date_of_birth.required'=>'Kolom tanggal lahir harus diisi',
            'kyc_gender.required'=>'Kolom jenis kelamin harus diisi',
            'kyc_province.required'=>'Kolom provinsi harus diisi',
            'kyc_province_id.required'=>'Kolom id provinsi harus diisi',
            'kyc_city_id.required'=>'Kolom id kota harus diisi',

        ];
        $validator = Validator::make($request->all(), [
            'kyc_name' => 'required|max:45',
            'kyc_ktp' => 'required|max:16|min:16',
            'kyc_address' => 'required',
            'kyc_city' => 'required',
            'kyc_postal_code'=>'required|max:5|min:5',
            'kyc_place_of_birth'=>'required',
            'kyc_date_of_birth'=>'required',
            'kyc_gender'=>'required',
            'kyc_province'=>'required',
            'kyc_province_id'=>'required',
            'kyc_city_id'=>'required'
        ], $message);
        if ($validator->fails()) {
            return response()->json([
                'message'=>$validator->errors()
            ],401);
        }else{
            try {
                $profileKYC->update([
                    'kyc_name'=>$request->kyc_name,
                    'kyc_ktp'=>$request->kyc_ktp,
                    'kyc_address'=>$request->kyc_address,
                    'kyc_city'=>$request->kyc_city,
                    'kyc_postal_code'=>$request->kyc_postal_code,
                    'kyc_place_of_birth'=>$request->kyc_place_of_birth,
                    'kyc_date_of_birth'=>$request->kyc_date_of_birth,
                    'kyc_gender'=>$request->kyc_gender,
                    'user_id'=>$request->user()->id,
                    'kyc_province'=>$request->kyc_province,
                    'kyc_province_id'=>$request->kyc_province_id,
                    'kyc_city_id'=>$request->kyc_city_id
                ]);
                return response()->json([
                    'message'=>'profile updated',
                    'data'=>$profileKYC
                ],201);
            } catch (\Throwable $th) {
                return response()->json([
                    'message'=>'profile failed to update'
                ],400);
            }
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\ProfileKYC  $profileKYC
     * @return \Illuminate\Http\Response
     */
    public function destroy(ProfileKYC $profileKYC)
    {
        try {
            $profileKYC->delete();
            return response()->json([
                'message'=>'profile berhasil dihapus'
            ],200);
        } catch (\Throwable $th) {
            return response()->json([
                'message'=>'profile gagal dihapus'
            ],400);
        }
    }

    public static function storeImageVerification(Request $request)
    {
        $message = [
            'kyc_self_photo.required' => 'Foto diri harus di isi dengan sebenar-benarnya',
            'kyc_self_photo.mimes' => 'Format foto hanya boleh, JPEG, JPG, PNG, HEIC',
            'kyc_ktp_photo.required' => 'Foto KTP harus di isi dengan sebenar-benarnya',
            'kyc_ktp_photo.mimes' => 'Format foto hanya boleh, JPEG, JPG, PNG, HEIC',
        ];

        $validator = Validator::make($request->all(), [
            'kyc_self_photo' => 'required|mimes:jpeg,jpg,png,heic',
            'kyc_ktp_photo' => 'required|mimes:jpeg,jpg,png,heic'
        ], $message);
        if ($validator->fails()) {
            return $validator->errors();
        } else {
            try {

                $image_self_photo = $request->kyc_self_photo->store('','google');
                $url_self_photo = Storage::disk('google')->url($image_self_photo);

                $image_ktp_photo = $request->kyc_ktp_photo->store('','google');
                $url_ktp_photo = Storage::disk('google')->url($image_ktp_photo);

                $kyc_photo_verification = ProfilePhoto::create([
                    'kyc_self_photo_path' => $url_self_photo,
                    'kyc_ktp_photo_path' => $url_ktp_photo,
                    'user_id' => $request->user()->id,
                    'kyc_self_photo_name' => $image_self_photo,
                    'kyc_self_ktp_name' => $image_ktp_photo
                ]);

            return response()->json([
                    'message' => 'Foto Verifikasi Berhasil ditambahkan'
                ], 200);
            } catch (\Throwable $th) {
                // return $th;
                return response()->json([
                    'message' => 'Foto Gagal ditambahkan'
                ]);
            }
        }
    }

    public static function addReview(Request $request)
    {
        $message = [
            'standard_store_category.required' => 'Pilih salah satu kategori !',
            'standard_store_min_product.required' => 'Harap setujui syarat minimum product !',
            'standard_store_min_quantity.required' => 'Harap setujui syarat minimun kuantiti product !',
            'standard_store_term_and_condition.required' => 'Harap setujui syarat dan kondisi !',
            'standard_store_product_photo.required' => 'Foto produk harus di isi !'
        ];

        $validator = Validator::make($request->all(), [
            'standard_store_category' => 'required',
            'standard_store_min_product' => 'required',
            'standard_store_min_quantity' => 'required',
            'standard_store_term_and_condition' => 'required',
            'standard_store_product_photo' => 'required|mimes:jpeg,jpg,png,heic',
        ]);

        if ($validator->fails()) {
            return $validator->errors();
        } else {
            try {
                $image_review_product = $request->standard_store_product_photo->store('','google');
                $url_review_product_photo = Storage::disk('google')->url($image_review_product);

                $kycReviewStandard = ReviewStandardKyc::create([
                    'standard_store_category' => $request->standard_store_category,
                    'standard_store_min_product' => $request->standard_store_min_product,
                    'standard_store_min_quantity' => $request->standard_store_min_quantity,
                    'standard_store_term_and_condition' => $request->standard_store_term_and_condition,
                    'standard_store_product_photo_path' => $url_review_product_photo,
                    'standard_store_product_photo_name' => $image_review_product,
                    'user_id' => $request->user()->id,
                ]);

                return response()->json([
                    'message' => 'Review data anda berhasil ditambahkan'
                ]);
            } catch (\Throwable $th) {
                //throw $th;
                return response()->json([
                    'message' => 'Review data gagal untuk di tambahkan'
                ]);
            }
        }
    }
}
