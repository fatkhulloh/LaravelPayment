<?php

namespace App\Http\Controllers;

use App\Models\Currency;
use App\Models\PaymentPlatform;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        // $pay = PaymentPlatform::get();
        // $cur = Currency::get();
        // $pay = PaymentPlatform::all();
        $pay = PaymentPlatform::where('subscriptions_enabled', true)->get();
        $cur = Currency::all();
        return view('pages.beranda')->with([
            'currencies' => $cur,
            'paymentPlatforms' => $pay,
        ]);
    }
}
