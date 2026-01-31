<?php

namespace App\Services;

use App\Traits\ConsumesExternalServices;
use GuzzleHttp\Client;
use Illuminate\Http\Request;

class PayPalService
{
    use ConsumesExternalServices;

    protected $baseUri;
    protected $clientId;
    protected $clientSecret;
    protected $accessToken;

    protected $plans;

    public function __construct()
    {
        $this->baseUri = config('services.paypal.base_uri');
        $this->clientId = config('services.paypal.client_id');
        $this->clientSecret = config('services.paypal.client_secret');
        $this->plans = config('services.paypal.plans');
    }

    // Hanya generate token sekali dan simpan di properti
    public function resolveAccessToken()
    {
        if ($this->accessToken) {
            return $this->accessToken;
        }

        $client = new Client(['base_uri' => $this->baseUri]);
        $response = $client->request('POST', '/v1/oauth2/token', [
            'headers' => [
                'Authorization' => 'Basic ' . base64_encode("{$this->clientId}:{$this->clientSecret}"),
                'Content-Type'  => 'application/x-www-form-urlencoded',
            ],
            'form_params' => [
                'grant_type' => 'client_credentials',
            ],
        ]);

        $body = json_decode($response->getBody()->getContents(), true);
        $this->accessToken = $body['access_token'];

        return $this->accessToken;
    }

    // Tambahkan header Authorization secara otomatis
    public function resolveAuthorization(&$queryParams, &$formParams, &$headers)
    {
        $headers['Authorization'] = 'Bearer ' . $this->resolveAccessToken();
    }

    public function decodeResponse($response)
    {
        return json_decode($response);
    }
    public function resolveFactor($currency)
    {
        $zeroDecimalCurrency = ['jpy'];
        if(in_array(strtoupper($currency), $zeroDecimalCurrency))
        {
            return 1;
        }
        return 100;
    }

    // Membuat order
    public function createOrder($value, $currency = 'USD')
    {
        $factor = $this->resolveFactor($currency);
        return $this->makeRequest(
            'POST',
            '/v2/checkout/orders',
            [],
            [
                "intent" => "CAPTURE",
                "purchase_units" => [
                    [
                        "amount" => [
                            "currency_code" => strtoupper($currency),
                            // "value" => round($value * $factor = $this->resolveFactor($currency)) / $factor,
                            "value" => round($value * $factor) / $factor,
                        ]
                    ]
                ],
                "application_context" => [
                    "brand_name" => config('app.name'),
                    "shipping_preference" => "NO_SHIPPING",
                    "user_action" => "PAY_NOW",
                    "return_url" => "http://localhost:8000/approval",
                    "cancel_url" => "http://localhost:8000/canceled",
                ]
            ],
            ['Content-Type' => 'application/json'],
            true
        );
    }

    // public function captureOrder($orderId)
    public function captureOrder($approvalId)
    {
        // return $this->makeRequest(
        //         'POST',
        //         "/v2/checkout/orders/{$orderId}/capture",
        //         [],
        //         '{}', // <── penting! dikirim string JSON kosong
        //         [
        //             'Content-Type' => 'application/json',
        //         ],
        //         true
        //     );


    // dd($result); // tampilkan hasil respons PayPal
         return $this->makeRequest(
            'POST',
            "/v2/checkout/orders/{$approvalId}/capture",
            [],
            [],
            [
                'Content-Type' => 'application/json'
            ],
        );
    }
    public function handlePayment(Request $req)
    {
        // dd($req);
        // $order = $this->createOrder($req->value, $req->currency);
        // // $orderLink = collect($order->link);
        // // $approve = $orderLink->where('rel', 'approve')->first;
        // // dd($order);
        // $orderLinks = collect($order['links']);
        // $approve = $orderLinks->firstWhere('rel', 'approve');
        // return redirect($approve->href);


        $order = $this->createOrder($req->value, $req->currency);

        // Kumpulkan semua link dari respons PayPal
        $orderLinks = collect($order->links);

        // Ambil link untuk "approve"
        $approve = $orderLinks->where('rel', 'approve')->first();
        session()->put('approvalId', $order->id);
        // Redirect ke halaman approval PayPal
        return redirect($approve->href);
    }


    //Subscription
    public function createSubscription($planSlug, $name, $email)
    {
        return $this->makeRequest(
            'POST',
            '/v1/billing/subscriptions',
            [],
            [
                'plan_id' => $this->plans[$planSlug],
                'subscriber' => [
                    'name' => [
                        'given_name' => $name,
                    ],
                    'email_address' => $email
                ],
                'application_context' => [
                    'brand_name' => config('app.name'),
                    'shipping_preference' => 'NO_SHIPPING',
                    'user_action' => 'SUBSCRIBE_NOW',
                    'return_url' => route('subscribe.approval', ['plan' => $planSlug]),
                    'cancel_url' => route('subscribe.cancelled'),
                ]
            ],
            [],
            $isJsonRequest = true,
        );
    }
    public function handleSubscription(request $request)
    {
        // dd($this->plans);
        // dd($request);
        $subscription = $this->createSubscription(
            $request->plan,
            $request->user()->name,
            $request->user()->email,
        );

        $subscriptionLinks = collect($subscription->links);

        $approve = $subscriptionLinks->where('rel', 'approve')->first();

        session()->put('subscriptionId', $subscription->id);

        return redirect($approve->href);
    }

    public function validateSubscription(Request $request)
    {
        if (session()->has('subscriptionId')) {
            $subscriptionId = session()->get('subscriptionId');

            session()->forget('subscriptionId');

            return $request->subscription_id == $subscriptionId;
        }

        return false;
    }

}
