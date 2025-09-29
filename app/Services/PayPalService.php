<?php

namespace App\Services;

use PayPalCheckoutSdk\Core\PayPalHttpClient;
use PayPalCheckoutSdk\Core\SandboxEnvironment;

class PayPalService
{
    public function client()
    {
        $environment = new SandboxEnvironment(env('PAYPAL_CLIENT_ID'), env('PAYPAL_SECRET'));
        return new PayPalHttpClient($environment);
    }
}

