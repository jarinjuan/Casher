<?php

namespace App\Services;

use App\Models\ExchangeRate;
use Carbon\Carbon;

class CurrencyConverter
{
    protected string $base = 'EUR';

    /**
     * Převede částku z jedné měny do druhé podle uložených kurzů.
     * Pokud pro dané datum kurz chybí, použije poslední dostupný.
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
            // Volání Artisan už proběhlo, pokud $from nebylo base.
            // Pokud je $from base, je potřeba provést stažení kurzů tady.
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
