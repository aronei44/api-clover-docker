<?php

namespace App\Http\Controllers;

use App\Models\Photo;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return response()->json([
            'message'=>'Success',
            'data'=>Product::with(['photos','category','sub_category','store'])->paginate(10)
        ], 200);

    }

    public function indexMobile()
    {
        return response()->json([
            'message'=>'Success',
            'data'=>Product::with(['photos','category','sub_category','store'])->get()
        ], 200);

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
            'product_name.required' => 'Kolom Nama Produk tidak boleh kosong !',
            'product_name.max' => 'Maximal 45 karakter !',
            'product_price.required' => 'Kolom Harga Produk tidak boleh kosong !',
            'product_price.numeric' => 'Kolom Hanya diisi angka',
            'product_description.required' => 'Kolom Deskripsi Tidak Boleh Kosong',
            'product_discount.required' => 'Kolom Diskon harus diisi. 0-100',
            'product_discount.numeric' => 'Kolom Diskon harus diisi angka',
            'product_stock.required'=> 'Stock Product tidak boleh kosong',
            'product_category_id.required' => 'Kolom category harus diisi',
            'product_category_id.numeric' => 'Kolom category harus diisi angka',
            'product_sub_category_id.required' => 'Kolom sub category harus diisi.',
            'product_sub_category_id.numeric' => 'Kolom sub category harus diisi angka',
        ];

        $validator = Validator::make($request->all(), [
            'product_name' => 'required|max:45',
            'product_price' => 'required|numeric',
            'product_description' => 'required',
            'product_stock' => 'required',
            'product_discount'=>'required|numeric',
            'product_category_id'=>'required|numeric',
            'product_sub_category_id'=>'required|numeric',
        ], $message);
        if ($validator->fails()) {
            return response()->json([
                'message'=>$validator->errors()
            ],401);
        } else {
            try {
                $product = Product::create([
                    'product_name' => $request->input('product_name'),
                    'product_price' => $request->input('product_price'),
                    'product_description' => $request->input('product_description'),
                    'product_stock' => $request->input('product_stock'),
                    'product_discount'=>$request->input('product_discount'),
                    'product_slug' => Str::of($request->input('product_name'))->slug('-')."-".Str::random(20),
                    'store_id'=>$request->user()->store->id,
                    'category_id'=>$request->input('product_category_id'),
                    'sub_category_id'=>$request->input('product_sub_category_id'),
                ]);

                $this->add_image($request, $product->id);
                return response()->json([
                    'message'=>'Success menambah product',
                    'data'=>$product
                ],200);
            } catch (\Throwable $th) {
                return $th;
                return response()->json([
                    'message'=>'failed to add product'
                ],400);
            }

        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function show(Product $product)
    {
        return response()->json([
            'message'=>'Product Details',
            'data'=>Product::with(['category','sub_category','photos','store'])->find($product->id)
        ],200);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function edit(Product $product)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Product $product)
    {
        // $photoProduct = Photo::where('product_id', $product)->first();
        $message =
        [
            'product_name.required' => 'Kolom Nama Produk tidak boleh kosong !',
            'product_name.max' => 'Maximal 45 karakter !',
            'product_price.required' => 'Kolom Harga Produk tidak boleh kosong !',
            'product_price.numeric' => 'Kolom Hanya diisi angka',
            'product_description.required' => 'Kolom Deskripsi Tidak Boleh Kosong',
            'product_discount.required' => 'Kolom Diskon harus diisi. 0-100',
            'product_discount.numeric' => 'Kolom Diskon harus diisi angka'
        ];

        $validator = Validator::make($request->all(), [
            'product_name' => 'required|max:45',
            'product_price' => 'required|numeric',
            'product_description' => 'required',
            'product_discount'=>'required|numeric',
            'product_stock' => 'required|numeric'
        ], $message);
        if ($validator->fails()) {
            return $validator->errors();
        } else {
            try {
                $product->update([
                    "product_name" => $request->product_name,
                    "product_price" => $request->product_price,
                    "product_description" => $request->product_description,
                    "product_stock" => $request->product_stock,
                    "product_discount" => $request->product_discount
                ]);

                // $this->update_image($request, $photoProduct);
                return response()->json([
                    'message'=>'Success',
                    'data'=>$product
                ],200);
            } catch (\Throwable $th) {
                return response()->json([
                    'message'=>'Failed'
                ],400);
            }

        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function destroy(Product $product)
    {
        try {
            foreach($product->photos as $photo){
                $url = $photo->path_to_product_image;
                $url = explode('?',$url);
                $url = explode('&',$url[1]);
                $url = explode('=',$url[0]);
                // echo $url[1];
                Storage::disk('google')->delete($url[1]);
                $photo->delete();
            }
            $product->delete();

            return response()->json([
                'message'=>'Success'
            ],200);
        } catch (\Throwable $th) {
            return response()->json([
                'message'=>'Failed'
            ],400);
        }

    }

    public function destroy_image(Photo $photo)
    {
        try {
            $url = $photo->path_to_product_image;
            $url = explode('?',$url);
            $url = explode('&',$url[1]);
            $url = explode('=',$url[0]);
            // echo $url[1];
            Storage::disk('google')->delete($url[1]);
            $photo->delete();
            return response()->json([
                'message'=>'Success'
            ],200);
        } catch (\Throwable $th) {
             return response()->json([
                'message'=>'Failed'
            ],400);
        }
    }

    function search(Request $request)
    {
        try {
            $products = Product::where('product_name', "like", "%".$request->keyword."%")->get();
            return response()->json([
                'message'=>'Data Ditemukan',
                'data'=> $products
            ],200);
        } catch (\Throwable $th) {
            return response()->json([
                'message'=>'Data Tidak Ditemukan'
            ],404);
        }
    }

    public static function add_image(Request $request, $id){
        // return $request->files();
        $message =
        [
            'product_image.required' => 'Kolom Image tidak boleh kosong !',
            'product_image.mimes' => 'Format Image harus JPEG, JPG, PNG, HEIC'
        ];

        $validator = Validator::make($request->all(), [
            'product_image' => 'required|mimes:jpeg,jpg,png,heic'
        ], $message);
        if ($validator->fails()) {
            return $validator->errors();
        } else {
            try {
                $image = $request->product_image->store('','google');
                $url = Storage::disk('google')->url($image);

                // return $url;
                $photo = Photo::create([
                    'product_image_path' => $url,
                    'product_image_name' => $image,
                    'product_id' => $id
                ]);
                return response()->json([
                    'message'=>'Foto Ditambahkan'
                ],200);
            } catch (\Throwable $th) {
                return $th;
                return response()->json([
                    'message'=>'Foto Gagal Ditambahkan'
                ],400);
            }
        }

    }

    public function update_image(Request $request, Photo $photo)
    {
        $message =
        [
            'product_image.required' => 'Kolom image tidak boleh kosong',
            'product_image.mimes' => 'format yang diizinkan: Jpg, Png, Jpeg'
        ];

        $validator = Validator::make($request->all(), [
            'product_image' => 'required|mimes:jpg,png,jpeg'
        ], $message);
        if ($validator->fails()) {
            return $validator->errors();
        } else {
            try {
                if ($request->product_image) {
                    // File::disk('google')->delete($photo->path_to_product_image);
                    // // $request->image->move(public_path('img'),$request->file('image')->getClientOriginalName());
                    // Storage::disk('google')->put($request->image->getClientOriginalName(), $request->image);
                    // $url = Storage::disk('google')->url($request->image->getClientOriginalName());
                    $image = $request->product_image->store('','google');
                    $url = Storage::disk('google')->url($image);
                    $photo->update([
                        'product_image_path' => $url,
                        'product_image_name' => $image,
                    ]);
                    return response()->json([
                        'message'=>'Success',
                        'data'=>$photo
                    ],200);
                }

            } catch (\Throwable $th) {
                return response()->json([
                    'message'=>'Failed'
                ],400);
            }

        }
    }
}
