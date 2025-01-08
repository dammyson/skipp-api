<?php

use App\Http\Controllers\NotificationController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
  
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\OtpController;
use App\Http\Controllers\WalletController;
use App\Http\Controllers\StoreController;

Route::post('register', [RegisterController::class, 'register']);
Route::post('login', [RegisterController::class, 'login']);
Route::post('forgetpassword',  [OtpController::class,'generatePinForgetPassword']);
Route::post('verifycode',  [OtpController::class,'VerifyOTP']);
Route::post('resetPassword', [AuthController::class, 'resetPassword']);
     

Route::prefix('products')->middleware(['auth:api'])->group(function () {
    Route::get('/', [ProductController::class,'getProductsByStoreOrCategory'])->name('get.product');
    Route::post('/', [ProductController::class, 'store']);
});

Route::get('scan/{code}', [ProductController::class, 'scan'])->name('scan');

Route::middleware(['auth:api'])->group(function () {
    Route::post('/updateProfile',  [AuthController::class,'update']);
    Route::post('/changePassword',  [AuthController::class,'changePassword']); 
    Route::get('/logout',  [AuthController::class,'logout']); 
    Route::get('/profile',  [AuthController::class,'profile']); 
});


Route::middleware(['auth:api'])->group(function () {
    Route::post('/cart/add', [CartController::class, 'addToCart']);
    Route::get('cart', [CartController::class, 'listCart']);
    Route::get('cart/{id}', [CartController::class, 'cartDetails']);
    Route::post('/checkout', [CartController::class, 'checkout']);

    Route::post('/cart/update-quantity', [CartController::class, 'updateQuantity']);
    Route::delete('/cart/remove-item', [CartController::class, 'removeItem']);
});

Route::prefix('wallet')->middleware(['auth:api'])->group(function () {
    Route::get('/{ref}', [WalletController::class, 'verify'])->name('wallet.top_up');
    Route::get('/fluter/{ref}', [WalletController::class, 'fluterVerify']);
});

Route::prefix('stores')->middleware(['auth:api'])->group(function () {
    Route::get('/', [StoreController::class, 'index']);
    Route::post('/', [StoreController::class, 'store']);
    Route::get('/{id}', [StoreController::class, 'show']);
    Route::put('/{id}', [StoreController::class, 'update']);
    Route::delete('/{id}', [StoreController::class, 'destroy']);
    Route::patch('/{id}/status', [StoreController::class, 'updateStatus']); 
});

Route::prefix('order')->middleware(['auth:api'])->group(function () {
    Route::get('/history', [OrderController::class, 'orderHistory']);
});

Route::prefix('notifications')->middleware('auth:api')->group(function () {
    Route::get('/', [NotificationController::class, 'listUserNotifications'])->name('notifications.list');
    Route::get('/{id}/read', [NotificationController::class, 'markAsRead'])->name('notifications.markAsRead');
    Route::get('/read-all', [NotificationController::class, 'markAllAsRead']) ->name('notifications.markAllAsRead');
});


