<?php

use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LogController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\ChatMasterController;
use App\Http\Controllers\GoogleAuthController;
use App\Http\Controllers\ProfileKYCController;
use App\Http\Controllers\RajaOngkirController;

use App\Http\Controllers\Admin\StoreController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\ReviewProductController;
use App\Http\Controllers\Admin\CategoryController;


/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Raja ongkir
Route::get('/province',[RajaOngkirController::class,'get_province']);
Route::get('/city/{id}',[RajaOngkirController::class,'get_city']);
Route::get('/courier',[RajaOngkirController::class,'get_courier']);
Route::post('/cost',[RajaOngkirController::class,'get_cost']);
//
Route::get('/auth/{google_id}', [GoogleAuthController::class,'getOauth']);


// Product
Route::get('/products', [ProductController::class,'index']);
Route::get('/products/mobile', [ProductController::class,'indexMobile']);
Route::get('/products/{product:product_slug}',   [ProductController::class,'show']);
Route::get('/products/{product}/id',   [ProductController::class,'show']);
Route::post('/products/search',      [ProductController::class, 'search']);
Route::get('/categories', [CategoryController::class,'index']);
Route::get('/categories/{category}', [CategoryController::class,'show']);

Route::middleware('auth:sanctum')->group(function () {

    // Resend Email Verification
    Route::post('/resend-email', [LogController::class, 'resendEmail']);

    // cart
    Route::get('/carts',            [CartController::class,'index']);
    Route::post('/carts',           [CartController::class,'store']);
    Route::delete('/carts/{cart}',  [CartController::class,'destroy']);

    // Route for verified mail
    Route::middleware('is_verified')->group(function(){

        // add review product
        Route::post('/feedback', [ReviewProductController::class,'store']);
        // Profile KYC
        Route::post('/add-image-verification',          [ProfileKYCController::class, 'storeImageVerification']);
        Route::post('/register-partner',                [ProfileKYCController::class, 'store']);
        Route::put('/update-partner/{profileKYC}',      [ProfileKYCController::class, 'update']);
        Route::delete('/delete-partner/{profileKYC}',   [ProfileKYCController::class, 'destroy']);
        Route::post('/add-review', [ProfileKYCController::class, 'addReview']);
        // Route::get('/kyc',   [ProfileKYCController::class, 'index']);

        // Transaction
        Route::get('/transaction',          [TransactionController::class,'index']);
        Route::post('/transaction',          [TransactionController::class,'store']);
        Route::post('/transaction-dummy',          [TransactionController::class,'dummyStore']);
        Route::get('/transaction/{transaction}',          [TransactionController::class,'show']);

        Route::middleware(['is_admin'])->group(function(){
            // category
            Route::post('/admin/category',            [CategoryController::class,'store']);
            Route::post('/admin/category/{category}',            [CategoryController::class,'storeSub']);
            Route::put('/admin/sub_category/{sub_category}',  [CategoryController::class,'updateSub']);

            // user
            Route::get('/admin/users',        [UserController::class,'index']);
            Route::put('/admin/approve-partner/{id}', [UserController::class, 'kycApproved']);

            // store
            Route::get('/admin/stores',       [StoreController::class,'index']);
        });

        // Photo
        Route::delete('/product/image/{photo}',     [ProductController::class, 'destroy_image']);
        Route::post('/products/image/{product}',    [ProductController::class,'update_image']);
        Route::post('/add-image/{id}',              [ProductController::class,'add_image']);

        Route::middleware(['is_partner'])->group(function () {
            // Store
            Route::post('/store', [App\Http\Controllers\StoreController::class, 'store']);
            Route::post('/store/{store}', [App\Http\Controllers\StoreController::class, 'update']);
            Route::delete('/store-delete/{store}', [App\Http\Controllers\StoreController::class, 'destroy']);

            Route::middleware(['is_mitra'])->group(function(){

                // Dashboard Seller
                Route::get('/dashboard/seller/total-income', [App\Http\Controllers\StoreController::class, 'totalIncome']);
                Route::get('/dashboard/seller/total-product', [App\Http\Controllers\StoreController::class, 'totalProduct']);

                // Product
               Route::post('/products',                [ProductController::class,'store']);
               Route::put('/products/{product}',       [ProductController::class,'update']);
               Route::delete('/products/{product}',    [ProductController::class,'destroy']);

               // Transaction
               Route::put('/transaction/{transaction}/pesanan',          [TransactionController::class,'updatePesanan']);
            //    Route::put('/transaction/{transaction}/pembayaran',          [TransactionController::class,'updatePembayaran']);
            });


        });
    });

    // log
    Route::post('/logout',          [LogController::class,'logout']);
    Route::get('/roles',            [LogController::class,'roles']);
    Route::put('/update-account',   [LogController::class,'update_account']);
    Route::put('/update-password',  [LogController::class,'update_password']);

    // Chats
    Route::post('/chat',            [ChatMasterController::class, 'index']);
    Route::post('/chat/{id}',       [ChatController::class, 'store']);


});


Route::post('/login',                   [LogController::class,'login']);
Route::post('/register',                [LogController::class,'register']);
Route::post('/register/mobile',         [LogController::class,'mobileRegister']);
Route::post('/forgot-password',         [LogController::class,'forgot_password']);
Route::put('/confirm-otp/{id}',         [LogController::class,'verif_email_otp']);
Route::post('/confirm-password/{id}',   [LogController::class,'confirm_password']);

Route::post('/auth/google',             [GoogleAuthController::class,'signGoogle']);

Route::post('/resend-otp-email', [LogController::class, 'resendOtpEmail']);

