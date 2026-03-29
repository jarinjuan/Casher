<?php

namespace App\Enums;

class CurrencyList
{
    /**
     * Seznam podporovaných kódů měn.
     */
    public const CODES = [
        'CZK', 'EUR', 'USD', 'GBP', 'JPY', 'CHF',
        'PLN', 'SEK', 'NOK', 'DKK', 'HUF',
        'CAD', 'AUD', 'NZD', 'CNY',
        'BTC', 'ETH',
    ];

    /**
     * Validační pravidlo Laravelu ve formátu "in:".
     */
    public static function validationRule(): string
    {
        return 'in:' . implode(',', self::CODES);
    }
}
