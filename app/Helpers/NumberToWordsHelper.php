<?php

namespace App\Helpers;

use NumberToWords\NumberToWords;

class NumberToWordsHelper
{
    public static function convertToArabicWords($amount, $currency)
    {
        if (empty($amount)) {
            $amount = 0;
        }

        $numberToWords = new NumberToWords();
        $numberTransformer = $numberToWords->getNumberTransformer('ar'); // 'ar' for Arabic
        $amountInWords = $numberTransformer->toWords((int)$amount);

        // Determine the currency label in Arabic
        $currencyLabel = match ($currency) {
            'SAR' => 'ريال سعودي',
            'USD' => 'دولار أمريكي',
            'EGP' => 'جنيه مصري',
            'AUD' => 'دولار أسترالي',
            default => 'عملة غير معروفة', // Default if currency is unknown
        };

        return "فقط وقدرة $amountInWords $currencyLabel لا غير";
    }
}
