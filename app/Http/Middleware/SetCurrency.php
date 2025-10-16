<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetCurrency
{
    public function handle(Request $request, Closure $next): Response
    {
        // Check if currency is set in request
        if ($request->has('currency')) {
            $currency = strtoupper($request->get('currency'));
            $currencies = config('currencies.currencies');
            
            if (isset($currencies[$currency])) {
                session(['currency' => $currency]);
            }
        }
        
        // Set default currency if not set
        if (!session()->has('currency')) {
            session(['currency' => config('currencies.default')]);
        }

        return $next($request);
    }
}
