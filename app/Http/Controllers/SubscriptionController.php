<?php

namespace App\Http\Controllers;

use App\Models\PaymentPlatform;
use App\Models\Plan;
use App\Resolvers\PaymentPlatformResolver;
use Illuminate\Http\Request;

class SubscriptionController extends Controller
{
    protected $paymentPlatformResolver;
    public function __construct(PaymentPlatformResolver $paymentPlatformResolver)
    {
        // $this->middleware(['auth', 'unsubcribed']);

        $this->paymentPlatformResolver = $paymentPlatformResolver;
    }
    public function show(Request $req)
    {
        // dd($req);
        $paymentPlatforms = PaymentPlatform::where('subscriptions_enabled', true)->get();
        // dd($paymentPlatforms);
        return view('pages.subscribe')->with([
            'plans' => Plan::all(),
            'paymentPlatforms' => $paymentPlatforms,
            // 'paymentPlatforms' => PaymentPlatform::all(),
        ]);
    }
    public function store(Request $request)
    {
        // dd($req);
          $rules = [
            'plan' => ['required', 'exists:plans,slug'],
            'payment_platform' => ['required', 'exists:payment_platforms,id'],
        ];

        $request->validate($rules);

        // $paymentPlatform = $this->paymentPlatformResolver->resolveService($request->payment_platform);
        $paymentPlatform = $this->paymentPlatformResolver
            ->resolveService($request->payment_platform);

        session()->put('subscriptionPlatformId', $request->payment_platform);

        // return $paymentPlatform->handlePayment($request);
        return $paymentPlatform->handleSubscription($request);
    }
    public function approval(Request $req)
    {
        dd($req);
    }
    public function cancelled(Request $req)
    {
        dd($req);
    }
}
