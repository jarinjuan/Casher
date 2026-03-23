<?php

namespace App\Helpers;

class Number
{
    /**
     * Format a given amount to the application's global standard:
     * e.g., 1 234 567,89 (space for thousands, comma for decimals)
     */
    public static function format($amount, $decimals = 2)
    {
        if ($amount === null) {
            return '';
        }
        
        return number_format((float)$amount, $decimals, '.', ' ');
    }
}
