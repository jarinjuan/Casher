<?php

namespace App\Services;

use App\Models\Investment;
use App\Models\InvestmentPrice;
use Illuminate\Support\Collection;
use Illuminate\Support\Carbon;

class PortfolioAnalyticsService
{
    public function buildSeries(Collection $investments, Carbon $start, string $bucket = 'day', ?\App\Models\Team $team = null): array
    {
        if ($investments->isEmpty()) {
            return ['labels' => [], 'values' => []];
        }

        $ids = $investments->pluck('id');
        $prices = InvestmentPrice::whereIn('investment_id', $ids)
            ->where('recorded_at', '>=', $start)
            ->orderBy('recorded_at')
            ->get();

        if ($prices->isEmpty()) {
            return ['labels' => [], 'values' => []];
        }

        $quantities = $investments->pluck('quantity', 'id');
        $labels = [];
        $values = [];
        $latest = [];
        $currentKey = null;

        $converter = null;
        if ($team) {
            $converter = app(\App\Services\CurrencyConverter::class);
        }
        $latestRateCache = [];

        foreach ($prices as $price) {
            $key = $bucket === 'month'
                ? $price->recorded_at->format('Y-m')
                : $price->recorded_at->toDateString();

            if ($currentKey !== null && $key !== $currentKey) {
                $labels[] = $currentKey;
                $values[] = $this->portfolioValue($latest, $quantities, $team, $converter, $latestRateCache);
            }

            $currentKey = $key;
            $latest[$price->investment_id] = [
                'price' => (float) $price->price,
                'currency' => $price->currency,
            ];
        }

        if ($currentKey !== null) {
            $labels[] = $currentKey;
            $values[] = $this->portfolioValue($latest, $quantities, $team, $converter, $latestRateCache);
        }

        return ['labels' => $labels, 'values' => $values];
    }

    public function changePercentFromSeries(array $series): float
    {
        $values = $series['values'] ?? [];
        if (count($values) < 2) {
            return 0.0;
        }

        $first = $values[0];
        $last = $values[count($values) - 1];

        if ($first == 0) {
            return 0.0;
        }

        return (($last - $first) / $first) * 100;
    }

    public function lastStepChangePercent(array $series): float
    {
        $values = $series['values'] ?? [];
        if (count($values) < 2) {
            return 0.0;
        }

        $last = $values[count($values) - 1];
        $prev = $values[count($values) - 2];

        if ($prev == 0) {
            return 0.0;
        }

        return (($last - $prev) / $prev) * 100;
    }

    protected function portfolioValue(array $latest, Collection $quantities, ?\App\Models\Team $team = null, $converter = null, &$latestRateCache = []): float
    {
        $total = 0.0;

        foreach ($quantities as $id => $qty) {
            if (isset($latest[$id])) {
                $info = $latest[$id];
                $val = $info['price'] * (float) $qty;
                
                if ($team && !empty($info['currency']) && $info['currency'] !== $team->default_currency) {
                    $currency = $info['currency'];
                    if (!isset($latestRateCache[$currency])) {
                        try {
                            $latestRateCache[$currency] = $converter->convert(1, $currency, $team->default_currency);
                        } catch (\Exception $e) {
                            $latestRateCache[$currency] = 1;
                        }
                    }
                    $val *= $latestRateCache[$currency];
                }
                
                $total += $val;
            }
        }

        return round($total, 2);
    }
}
