<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Company Information
    |--------------------------------------------------------------------------
    |
    | These values are used in the invoice PDF generation
    |
    */

    'company' => [
        'name' => env('INVOICE_COMPANY_NAME', 'Bookshare Platform'),
        'address' => env('INVOICE_COMPANY_ADDRESS', 'La petite ariana'),
        'city' => env('INVOICE_COMPANY_CITY', 'Ariana, La petite ariana'),
        'email' => env('INVOICE_COMPANY_EMAIL', 'contact@bookshare.com'),
        'phone' => env('INVOICE_COMPANY_PHONE', '+216 29135995'),
        'website' => env('INVOICE_COMPANY_WEBSITE', 'www.bookshare.com'),
        'logo' => env('INVOICE_COMPANY_LOGO', '📚 Bookshare'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Tax Configuration
    |--------------------------------------------------------------------------
    |
    | Configure tax rate and settings
    |
    */

    'tax' => [
        'enabled' => env('INVOICE_TAX_ENABLED', false),
        'rate' => env('INVOICE_TAX_RATE', 0.00), // 0%
        'label' => env('INVOICE_TAX_LABEL', 'Tax'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Invoice Settings
    |--------------------------------------------------------------------------
    |
    | General invoice configuration
    |
    */

    'prefix' => env('INVOICE_PREFIX', 'INV'),
    'due_days' => env('INVOICE_DUE_DAYS', 0), // Due immediately by default
    'notes' => env('INVOICE_NOTES', 'Thank you for your subscription!'),
    
    /*
    |--------------------------------------------------------------------------
    | PDF Settings
    |--------------------------------------------------------------------------
    |
    | PDF generation settings
    |
    */

    'pdf' => [
        'format' => 'A4',
        'orientation' => 'portrait',
    ],
];
