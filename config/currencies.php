<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Default Currency
    |--------------------------------------------------------------------------
    */
    'default' => env('DEFAULT_CURRENCY', 'USD'),

    /*
    |--------------------------------------------------------------------------
    | Supported Currencies
    |--------------------------------------------------------------------------
    */
    'currencies' => [
        'USD' => [
            'name' => 'US Dollar',
            'symbol' => '$',
            'code' => 'USD',
            'rate' => 1.00, // Base currency
            'flag' => '🇺🇸',
        ],
        'EUR' => [
            'name' => 'Euro',
            'symbol' => '€',
            'code' => 'EUR',
            'rate' => 0.92, // 1 USD = 0.92 EUR
            'flag' => '🇪🇺',
        ],
        'TND' => [
            'name' => 'Tunisian Dinar',
            'symbol' => 'د.ت',
            'code' => 'TND',
            'rate' => 3.10, // 1 USD = 3.10 TND
            'flag' => '🇹🇳',
        ],
        'GBP' => [
            'name' => 'British Pound',
            'symbol' => '£',
            'code' => 'GBP',
            'rate' => 0.79, // 1 USD = 0.79 GBP
            'flag' => '🇬🇧',
        ],
        'MAD' => [
            'name' => 'Moroccan Dirham',
            'symbol' => 'د.م.',
            'code' => 'MAD',
            'rate' => 10.00, // 1 USD = 10.00 MAD
            'flag' => '🇲🇦',
        ],
    ],

];
