<?php

namespace App\Services;

use App\Models\ExchangeRate;
use Carbon\Carbon;

class CurrencyConverter
{
    protected string $base = 'EUR';

    /**
     * Convert amount from one currency to another using stored rates.
     * If rates for given date are not available, uses latest.
     * @param float $amount
     * @param string $from
     * @param string $to
     * @param \Carbon\Carbon|string|null $date
     * @return float
     */
    public function convert(float $amount, string $from, string $to, $date = null): float
    {
        $from = strtoupper($from);
        $to = strtoupper($to);

        if ($from === $to) return $amount;

        $date = $date ? Carbon::parse($date)->toDateString() : null;

        // If base is EUR in DB
        // get rates as currency per EUR (e.g. USD = 1.1234 means 1 EUR = 1.1234 USD)

        // If converting from non-base, we convert to base first
        if ($from !== $this->base) {
            $rFrom = ExchangeRate::latestRate($from, $date);
            if (! $rFrom) {
                throw new \RuntimeException("Rate for {$from} not found");
            }
            // amount in base currency (EUR)
            $amount = $amount / (float) $rFrom->rate;
        }

        if ($to === $this->base) {
            return (float) $amount;
        }

        $rTo = ExchangeRate::latestRate($to, $date);
        if (! $rTo) {
            throw new \RuntimeException("Rate for {$to} not found");
        }

        return (float) ($amount * (float) $rTo->rate);
    }
}
