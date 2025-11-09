<?php

namespace App\Resolvers;

use App\Models\PaymentPlatform as ModelsPaymentPlatform;
use App\PaymentPlatform;

class PaymentPlatformResolver
{
    protected $paymentPlatforms;

    public function __construct()
    {
        $this->paymentPlatforms = ModelsPaymentPlatform::all();
    }

    public function resolveService($paymentPlatformId)
    {
        $name = strtolower($this->paymentPlatforms->firstWhere('id', $paymentPlatformId)->name);
        // dd($name);
        $service = config("services.{$name}.class");

        if ($service) {
            return resolve($service);
        }

        throw new \Exception('The selected platform is not in the configuration');

    }
}
