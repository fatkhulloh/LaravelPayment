<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\RegisterController;
use Illuminate\Support\Facades\Route;

// Route::get('/', function () {
//     return view('welcome');
// });
// Route::get('/', function () {
//     return view('pages.beranda');
// });

Route::get('/', [HomeController::class, 'index']);

//Register
Route::view('/register', 'pages.register');
Route::get('/register', [RegisterController::class, 'show'])->name('register');

Route::post('/register', [RegisterController::class, 'register'])->name('register.post');

//Login
// Route::view('/login', 'pages.login');
Route::get('/login', [LoginController::class, 'show'])->name('login');

Route::post('/login', [LoginController::class, 'login'])->name('login.post');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

Route::post('/pay', [PaymentController::class, 'pay']);
Route::post('/approval', [PaymentController::class, 'approval']);
Route::post('/canceled', [PaymentController::class, 'canceled']);
