<?php

namespace App\Http\Controllers;

use App\Services\CurrencyService;
use Illuminate\Http\Request;

class CurrencyController extends Controller
{
    protected $currencyService;

    public function __construct(CurrencyService $currencyService)
    {
        $this->currencyService = $currencyService;
    }

    public function changeCurrency(Request $request)
    {
        $currency = $request->input('currency');
        
        if ($this->currencyService->setUserCurrency($currency)) {
            return response()->json([
                'success' => true,
                'message' => 'Currency changed successfully',
                'currency' => $currency
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Invalid currency'
        ], 400);
    }
}
