<?php

namespace App\Services;

class CurrencyService
{
    /**
     * Convert amount from one currency to another
     */
    public function convert($amount, $fromCurrency = 'USD', $toCurrency = 'USD')
    {
        if ($fromCurrency === $toCurrency) {
            return $amount;
        }

        $currencies = config('currencies.currencies');
        
        if (!isset($currencies[$fromCurrency]) || !isset($currencies[$toCurrency])) {
            return $amount;
        }

        // Convert to USD first (base currency)
        $amountInUSD = $amount / $currencies[$fromCurrency]['rate'];
        
        // Then convert to target currency
        $convertedAmount = $amountInUSD * $currencies[$toCurrency]['rate'];
        
        return round($convertedAmount, 2);
    }

    /**
     * Get all supported currencies
     */
    public function getSupportedCurrencies()
    {
        return config('currencies.currencies');
    }

    /**
     * Get currency details
     */
    public function getCurrency($code)
    {
        $currencies = config('currencies.currencies');
        return $currencies[$code] ?? null;
    }

    /**
     * Format amount with currency symbol
     */
    public function format($amount, $currency = 'USD')
    {
        $currencyData = $this->getCurrency($currency);
        
        if (!$currencyData) {
            return number_format($amount, 2);
        }

        $symbol = $currencyData['symbol'];
        $formattedAmount = number_format($amount, 2);

        // For some currencies, symbol goes after the amount
        if (in_array($currency, ['TND', 'MAD'])) {
            return $formattedAmount . ' ' . $symbol;
        }

        return $symbol . $formattedAmount;
    }

    /**
     * Get user's currency based on session or default
     */
    public function getUserCurrency()
    {
        return session('currency', config('currencies.default'));
    }

    /**
     * Set user's preferred currency
     */
    public function setUserCurrency($currency)
    {
        $currencies = config('currencies.currencies');
        
        if (isset($currencies[$currency])) {
            session(['currency' => $currency]);
            return true;
        }
        
        return false;
    }

    /**
     * Get price in user's currency (simple conversion without regional pricing)
     */
    public function getLocalizedPrice($basePrice, $baseCurrency = 'USD', $userCurrency = null)
    {
        $userCurrency = $userCurrency ?? $this->getUserCurrency();
        
        // Convert to user's currency
        $convertedPrice = $this->convert($basePrice, $baseCurrency, $userCurrency);
        
        return $convertedPrice;
    }
}
