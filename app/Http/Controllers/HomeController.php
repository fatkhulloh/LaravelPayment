<?php

namespace App\Http\Controllers;

use App\Models\Currency;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        $cur = Currency::get();
        return view('pages.beranda')->with([
            'currencies' => $cur,
        ]);
    }
}
