<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Role;
use App\Models\Store;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
class StoreController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {

        return response()->json([
            'message'=>'success',
            'data'=>Store::with(['products','user'])->where('user_id',$request->user()->id)->first()
        ],200);
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
        $message =
        [
            'store_name.max' => 'Kolom Nama Toko Maximal 45 Karakter !',
            'store_description.required' => 'Kolom Deskripsi Harus diisi !',
            'store_province.required' => 'Kolom Provinsi harus di isi !',
            'store_city.required' => 'Kolom Kota Harus di isi !',
            'store_postal_code.required' => 'Kolom kode pos harus di isi !',
            'store_complete_address.required' => 'Kolom alamat lengkap harus di isi !',
            'store_description.min' => 'Kolom Deskripsi minimal 20 karakter !'
        ];

        $validator = Validator::make($request->all(), [
            'store_name' => 'required|max:45',
            'store_description' => 'required|min:20',
            'store_image_profile' => 'required',
            'store_province' => 'required',
            'store_city' => 'required',
            'store_postal_code' => 'required',
            'store_complete_address' => 'required',
        ], $message);
        if ($validator->fails()) {
            return response()->json([
                'message'=>$validator->errors()
            ],401);
        } else {
            try {
                // Storage::disk('google')->put($request->store_image_profile->getClientOriginalName(), $request->store_image_profile);
                // $url = Storage::disk('google')->url($request->store_image_profile->getClientOriginalName());
                // $request->store_image_profile->move(public_path('img/store_file'),$request->store_image_profile->getClientOriginalName());
                $image = $request->store_image_profile->store('','google');
                $url = Storage::disk('google')->url($image);
                $store = Store::create([
                    'store_name' => $request->input('store_name'),
                    'store_description' => $request->input('store_description'),
                    'store_image_profile' => $url,
                    'store_image_name' => $image,
                    'user_id'=>$request->user()->id,
                    'store_province' => $request->store_province,
                    'store_city' => $request->store_city,
                    'store_postal_code' => $request->store_postal_code,
                    'store_complete_address' => $request->store_complete_address
                ]);
                $user = Role::firstWhere('user_id',$request->user()->id)->update([
                    'role'=>'Seller'
                ]);

                // ProfileKYCController::addReview($request);

                return response()->json([
                    'message'=>'Success membuat toko ',
                    'data'=>$store
                ],200);
            } catch (\Throwable $th) {
                // return $th;
                return response()->json([
                    'message'=>'failed untuk membuat toko'
                ],400);
            }

        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Store  $store
     * @return \Illuminate\Http\Response
     */
    public function show(Store $store)
    {
        return response()->json([
            'message' => 'Store Detail',
            'data' => $store
        ], 200);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Store  $store
     * @return \Illuminate\Http\Response
     */
    public function edit(Store $store)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Store  $store
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Store $store)
    {
        $message =
        [
            'store_name.max' => 'Kolom Nama Toko Maximal 45 Karakter !',
            'store_name.required' => 'Kolom Nama Toko tidak boleh kosong !',
            'store_description.required' => 'Kolom Deskripsi Harus diisi !',
            'store_description.min' => 'Kolom Deskripsi Harus diisi !',
            'store_province.required' => 'Kolom Provinsi harus di isi !',
            'store_city.required' => 'Kolom Kota Harus di isi !',
            'store_postal_code.required' => 'Kolom kode pos harus di isi !',
            'store_complete_address.required' => 'Kolom alamat lengkap harus di isi !'
        ];

        $validator = Validator::make($request->all(), [
            'store_name' => 'required|max:45',
            'store_description' => 'required|min:20',
            'store_province' => 'required',
            'store_city' => 'required',
            'store_postal_code' => 'required|size:6',
            'store_complete_address' => 'required',
        ], $message);
        if ($validator->fails()) {
            return $validator->errors();
        } else {
            try {
                if ($request->store_image_profile) {
                    $url = $store->store_image_profile;
                    $url = explode('?',$url);
                    $url = explode('&',$url[1]);
                    $url = explode('=',$url[0]);
                    // echo $url[1];
                    Storage::disk('google')->delete($url[1]);
                    $image = $request->store_image_profile->store('','google');
                    $urlStore = Storage::disk('google')->url($image);
                    // File::disk('google')->delete($store->store_image_profile);
                    // Storage::disk('google')->put($request->store_image_profile->getClientOriginalName(), $request->store_image_profile);
                    // $url = Storage::disk('google')->url($request->store_image_profile->getClientOriginalName());
                    // $request->store_image_profile->move(public_path('img/store_file'),$request->store_image_profile->getClientOriginalName());
                    $store->update([
                        'store_name' => $request->store_name,
                        'store_description' => $request->store_description,
                        'store_image_profile' =>$urlStore,
                        'store_image_name' => $image,
                        'store_province' => $request->store_province,
                        'store_city' => $request->store_city,
                        'store_postal_code' => $request->store_postal_code,
                        'store_complete_address' => $request->store_complete_address
                    ]);
                } else {
                    $store->update([
                        'store_name' => $request->store_name,
                        'store_description' => $request->store_descriptsion,
                        'store_province' => $request->store_province,
                        'store_city' => $request->store_city,
                        'store_postal_code' => $request->store_postal_code,
                        'store_complete_address' => $request->store_complete_address
                    ]);
                }


                return response()->json([
                    'message'=>'Success mengubah toko ',
                    'data'=>$store
                ],200);
            } catch (\Throwable $th) {
                // return $th;
                return response()->json([
                    'data'=>$store
                ],200);
            } catch (\Throwable $th) {
                // return $th;
            }
        }
    }
    public function destroy(Store $store)
    {
        try {
            $url = $store->store_image_profile;
            $url = explode('?',$url);
            $url = explode('&',$url[1]);
            $url = explode('=',$url[0]);
            // echo $url[1];
            Storage::disk('google')->delete($url[1]);
            $store->delete();

            return response()->json([
                'message'=>'Success'
            ],200);
        } catch (\Throwable $th) {
            return response()->json([
                'message'=>'Failed'
            ],400);
        }
    }

    public function totalIncome()
    {
        $countIncome = Transaction::where('status_pembayaran', 'sudah membayar')->sum('total_price');
        // $countTransaction = count($transaction->total_price);

        return response()->json([
            'total_income' => $countIncome
        ]);
    }

    public function totalProduct(Request $request)
    {
        $store = Store::firstWhere('user_id', $request->user()->id);
        $countProduct = Product::where('store_id', $store->id)->count();

        return response()->json([
            'total_product' => $countProduct
        ]);
    }
}
