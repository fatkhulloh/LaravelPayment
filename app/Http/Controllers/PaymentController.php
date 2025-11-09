<?php

namespace App\Http\Controllers;

use App\Models\Currency;
use App\Models\PaymentPlatform;
use App\Services\PayPalService;
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
    public function approval(Request $request, PayPalService $paypal)
    {
         $orderId = $request->get('token');
        $result = $paypal->captureOrder($orderId);

        dd($result); // untuk lihat hasilnya
        // // return view('pages.approval');
        //  $token = $request->query('token'); // ambil token dari URL

        // if (!$token) {
        //     return redirect('/')->with('error', 'Token tidak ditemukan');
        // }

        // $paypal = new PayPalService();
        // $result = $paypal->captureOrder($token);

        // // Jika sukses
        // if (isset($result->status) && $result->status === 'COMPLETED') {
        //     // kirim data hasil capture ke halaman approval
        //     return view('pages.approval', ['result' => $result]);
        // }

        // // Jika gagal
        // return view('pages.approval', [
        //     'result' => $result,
        //     'error'  => 'Transaksi gagal diselesaikan',
        // ]);
    }
    public function canceled()
    {

    }

}
