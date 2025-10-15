<?php

return [
    'api_url' => env('PAYMENT_API_URL', 'https://api.stripe.com/v1'),
    'api_key' => env('PAYMENT_API_KEY'),
    'webhook_secret' => env('PAYMENT_WEBHOOK_SECRET'),
    'currency' => env('PAYMENT_CURRENCY', 'tnd'),
];