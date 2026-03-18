<?php

namespace App\Enums;

class CurrencyList
{
    /**
     * All supported currency codes.
     */
    public const CODES = [
        'CZK', 'EUR', 'USD', 'GBP', 'JPY', 'CHF',
        'PLN', 'SEK', 'NOK', 'DKK', 'HUF',
        'CAD', 'AUD', 'NZD', 'CNY',
        'BTC', 'ETH',
    ];

    /**
     * Laravel validation "in:" rule string.
     */
    public static function validationRule(): string
    {
        return 'in:' . implode(',', self::CODES);
    }
}
