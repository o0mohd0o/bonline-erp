<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class CurrencyHelper
{
    /**
     * Convert amount using your currency conversion API
     */
    public static function convertAmount($amount, $fromCurrency, $toCurrency)
    {
        if ($fromCurrency === $toCurrency) {
            return $amount;
        }

        $rate = self::getExchangeRate($fromCurrency, $toCurrency);
        return round($amount * $rate, 2);
    }

    /**
     * Get exchange rate from your API (replace with your actual API endpoint)
     */
    public static function getExchangeRate($fromCurrency, $toCurrency)
    {
        if ($fromCurrency === $toCurrency) {
            return 1.00;
        }

        $cacheKey = "exchange_rate_{$fromCurrency}_{$toCurrency}";
        
        return Cache::remember($cacheKey, 3600, function() use ($fromCurrency, $toCurrency) {
            try {
                // TODO: Replace this URL with your actual currency conversion API
                // $response = Http::get('YOUR_API_ENDPOINT', [
                //     'from' => $fromCurrency,
                //     'to' => $toCurrency,
                //     'amount' => 1
                // ]);
                
                // For now, using static rates - replace with your API call
                $staticRates = [
                    'USD' => ['SAR' => 3.75, 'EGP' => 31.00, 'AUD' => 1.50],
                    'SAR' => ['USD' => 0.2667, 'EGP' => 8.27, 'AUD' => 0.40],
                    'EGP' => ['USD' => 0.0323, 'SAR' => 0.121, 'AUD' => 0.048],
                    'AUD' => ['USD' => 0.67, 'SAR' => 2.50, 'EGP' => 20.67]
                ];

                return $staticRates[$fromCurrency][$toCurrency] ?? 1.00;
                
            } catch (\Exception $e) {
                Log::error("Currency conversion failed: " . $e->getMessage());
                return 1.00; // Fallback rate
            }
        });
    }

    /**
     * Get currency symbol
     */
    public static function getCurrencySymbol($currency)
    {
        $symbols = [
            'USD' => '$',
            'SAR' => 'ر.س',
            'EGP' => 'ج.م',
            'AUD' => 'A$'
        ];

        return $symbols[$currency] ?? $currency;
    }

    /**
     * Get supported currencies
     */
    public static function getSupportedCurrencies()
    {
        return ['USD', 'SAR', 'EGP', 'AUD'];
    }
}
