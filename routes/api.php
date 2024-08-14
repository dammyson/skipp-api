<?php

use App\Http\Controllers\CartController;
use App\Http\Controllers\ProductController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
  
use App\Http\Controllers\RegisterController;

 
  
Route::post('register', [RegisterController::class, 'register']);
Route::post('login', [RegisterController::class, 'login']);
     

Route::prefix('products')->middleware(['auth:api'])->group(function () {
    Route::get('/', [ProductController::class,'getProductsByStoreOrCategory'])->name('get.product');
    Route::post('/', [ProductController::class, 'store']);
});


Route::middleware(['auth:api'])->group(function () {
    Route::post('/cart/add', [CartController::class, 'addToCart']);
    Route::post('/checkout', [CartController::class, 'checkout']);
});



