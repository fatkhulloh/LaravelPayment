<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
class RegisterController extends Controller
{
    public function show()
    {
        if (Auth::check()) {
            // Kalau sudah login, arahkan ke beranda
            return redirect('/')->with('message', 'Anda sudah login.');
        }
           return view('pages.register');
    }
    public function register(Request $req)
    {
         // Jika sudah login, langsung alihkan ke beranda
        if (Auth::check()) {
            return redirect('/')->with('message', 'Anda sudah login.');
        }
        // Validasi input
        $req->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        // Simpan user baru
        User::create([
            'name' => $req->name,
            'email' => $req->email,
            'password' => Hash::make($req->password),
        ]);

        // Redirect setelah berhasil
        return redirect('/login')->with('message', 'Registrasi berhasil! Silakan login.');
    }
}
