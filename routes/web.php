<?php

use Illuminate\Support\Facades\Route;

Route::get('/{amount}', function ($amount) {
    return view('welcome', ['amount' => $amount]);
})->name('payment');

