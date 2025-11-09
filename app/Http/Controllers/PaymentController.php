<?php

namespace App\Http\Controllers;

use App\Models\Currency;
use App\Models\PaymentPlatform;
use App\Resolvers\PaymentPlatformResolver;
use App\Services\PayPalService;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    protected $paymentPlatformResolver;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(PaymentPlatformResolver $paymentPlatformResolver)
    {
        // $this->middleware('auth');

        $this->paymentPlatformResolver = $paymentPlatformResolver;
    }
    public function pay(Request $req)
    {
        $rules = [
            'value' => ['required', 'numeric', 'min:5'],
            'currency' => ['required', 'exists:currencies,iso'],
            'payment_platform' => ['required', 'exists:payment_platforms,id'],
        ];
        $req->validate($rules);

        // dd($req);
        // $pay = PaymentPlatform::all();
        // $cur = Currency::all();
        // $paymentPlatform = resolve(PaymentPlatform::class);
        $paymentPlatform = $this->paymentPlatformResolver
            ->resolveService($req->payment_platform);

        return $paymentPlatform->handlePayment($req);

        // return $req->all();
    }
    public function approval(Request $request, PayPalService $paypal)
    {
        $orderId = $request->get('token');

        // if (!$token) {
        if (!$orderId) {
            return redirect('/')->with('error', 'Token tidak ditemukan');
        }
        $result = $paypal->captureOrder($orderId);

        // dd($result); // untuk lihat hasilnya
        // // return view('pages.approval');
        // // Jika sukses
        if (isset($result->status) && $result->status === 'COMPLETED') {
            // kirim data hasil capture ke halaman approval
            return view('pages.approval', ['result' => $result]);
        }

        // Jika gagal
        return view('pages.approval', [
            'result' => $result,
            'error'  => 'Transaksi gagal diselesaikan',
        ]);
    }
    public function canceled()
    {

    }

}
