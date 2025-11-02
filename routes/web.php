<?php

use App\Http\Controllers\HomeController;
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
