<?php

use Illuminate\Support\Facades\Route;

// Route::get('/', function () {
//     return view('welcome');
// });
Route::get('/', function () {
    return view('pages.beranda');
});

Route::view('/login', 'pages.login');
Route::view('/register', 'pages.register');
