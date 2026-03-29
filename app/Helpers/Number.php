<?php

namespace App\Helpers;

class Number
{
    /**
     * Naformátuje částku do jednotného formátu aplikace:
     * např. 1 234 567.89 (mezera pro tisíce, tečka pro desetinná místa)
     */
    public static function format($amount, $decimals = 2)
    {
        if ($amount === null) {
            return '';
        }
        
        /** @var string $decimalSeparator Výchozí tečka pro jednotný formát */
        $decimalSeparator = '.';
        /** @var string $thousandsSeparator Výchozí mezera pro oddělení tisíců */
        $thousandsSeparator = ' ';

        return number_format((float)$amount, $decimals, $decimalSeparator, $thousandsSeparator);
    }
}
