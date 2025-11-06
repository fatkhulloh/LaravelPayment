<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\PaymentController;
use Illuminate\Support\Facades\Route;

// Route::get('/', function () {
//     return view('welcome');
// });
// Route::get('/', function () {
//     return view('pages.beranda');
// });

Route::get('/', [HomeController::class, 'index']);
Route::view('/login', 'pages.login');
Route::view('/register', 'pages.register');

Route::post('/pay', [PaymentController::class, 'pay']);
Route::post('/approval', [PaymentController::class, 'approval']);
Route::post('/canceled', [PaymentController::class, 'canceled']);
