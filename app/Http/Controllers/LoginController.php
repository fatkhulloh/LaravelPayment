<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class LoginController extends Controller
{
    // public function __construct()
    // {
    //     $this->middleware('guest')->except('logout');
    // }
    public function show()
    {
        if (Auth::check()) {
            // Kalau sudah login, arahkan ke beranda
            return redirect('/')->with('message', 'Anda sudah login.');
        }
        return view('pages.login');
    }
    public function login(Request $req)
    {
         // Jika sudah login, langsung alihkan ke beranda
        if (Auth::check()) {
            return redirect('/')->with('message', 'Anda sudah login.');
        }
        // Validasi input
        $req->validate([
            'email' => 'required|email',
            'password' => 'required|string|min:8',
        ]);

        // Coba login menggunakan Auth
        if (Auth::attempt(['email' => $req->email, 'password' => $req->password])) {
            $req->session()->regenerate(); // keamanan sesi

            return redirect('/')->with('message', 'Login berhasil!');
        }

        // Jika gagal login
        return back()->withErrors([
            'email' => 'Email atau password salah.',
        ])->onlyInput('email');
    }

    public function logout(Request $req)
    {
        Auth::logout();
        $req->session()->invalidate();
        $req->session()->regenerateToken();

        return redirect('/')->with('message', 'Logout berhasil!');
    }
}
