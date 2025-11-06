<?php

namespace App\Http\Controllers;

use App\Models\Currency;
use App\Models\PaymentPlatform;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function pay(Request $req)
    {
        $rules = [
            'value' => ['required', 'numeric', 'min:5'],
            'currency' => ['required', 'exists:currencies,iso'],
            'payment_platform' => ['required', 'exists:payment_platforms,id'],
        ];
        $req->validate($rules);

        // $pay = PaymentPlatform::all();
        // $cur = Currency::all();

        return $req->all();
    }
    public function approval()
    {

    }
    public function canceled()
    {

    }
}
