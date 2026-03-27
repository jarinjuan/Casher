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
        
        // Try to fetch once if rate is missing
        if ($from !== $this->base) {
            $rFrom = ExchangeRate::latestRate($from, $date);
            if (! $rFrom) {
                \Illuminate\Support\Facades\Artisan::call('fx:fetch-ecb');
                $rFrom = ExchangeRate::latestRate($from, $date);
            }
            
            if (! $rFrom) {
                throw new \RuntimeException("Rate for {$from} not found even after refresh");
            }
            $amount = $amount / (float) $rFrom->rate;
        }

        if ($to === $this->base) {
            return (float) $amount;
        }

        $rTo = ExchangeRate::latestRate($to, $date);
        if (! $rTo) {
            // We already tried calling Artisan above if $from was different from base, 
            // but if $from was base, we might need to call it here.
            if ($from === $this->base) {
                \Illuminate\Support\Facades\Artisan::call('fx:fetch-ecb');
                $rTo = ExchangeRate::latestRate($to, $date);
            }
        }

        if (! $rTo) {
            throw new \RuntimeException("Rate for {$to} not found even after refresh");
        }

        return (float) ($amount * (float) $rTo->rate);
    }
}
