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

    public function __construct()
    {
        $this->baseUri = config('services.paypal.base_uri');
        $this->clientId = config('services.paypal.client_id');
        $this->clientSecret = config('services.paypal.client_secret');
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

    // Membuat order
    public function createOrder($value, $currency = 'USD')
    {
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
                            "value" => $value
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

        // Redirect ke halaman approval PayPal
        return redirect($approve->href);
    }

}
