<?php

namespace App\Console\Commands;

use App\Models\Investment;
use App\Models\InvestmentPrice;
use App\Services\MarketDataService;
use Illuminate\Console\Command;

class RefreshInvestmentPrices extends Command
{
    protected $signature = 'investments:refresh';
    protected $description = 'Refresh investment prices from external APIs';

    public function handle(MarketDataService $marketData): int
    {
        $investments = Investment::all();
        $updated = 0;

        foreach ($investments as $investment) {
            $price = $marketData->getPrice($investment);
            if (! $price) {
                continue;
            }

            InvestmentPrice::create([
                'investment_id' => $investment->id,
                'price' => $price['price'],
                'currency' => $price['currency'],
                'recorded_at' => now(),
                'source' => $price['source'],
            ]);
            $updated++;
        }

        $this->info("Updated {$updated} prices.");

        return Command::SUCCESS;
    }
}
