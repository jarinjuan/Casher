<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use App\Models\ExchangeRate;
use Carbon\Carbon;

class FetchEcbRates extends Command
{
    protected $signature = 'fx:fetch-ecb';
    protected $description = 'Fetch daily exchange rates from ECB and store in database';

    public function handle()
    {
        $this->info('Fetching ECB rates...');

        $url = 'https://www.ecb.europa.eu/stats/eurofxref/eurofxref-daily.xml';

        $res = Http::get($url);
        if (! $res->ok()) {
            $this->error('Failed to fetch ECB rates');
            return 1;
        }

        $body = $res->body();

        $dom = new \DOMDocument();
        libxml_use_internal_errors(true);
        if (! $dom->loadXML($body)) {
            $this->error('Invalid XML');
            return 1;
        }

        $xpath = new \DOMXPath($dom);
        $timeNodes = $xpath->query("//*[local-name()='Cube' and @time]");
        if ($timeNodes->length === 0) {
            $this->error('Could not find Cube/time node');
            return 1;
        }

        $time = $timeNodes->item(0)->getAttribute('time');
        $date = Carbon::parse($time)->toDateString();
        ExchangeRate::updateOrCreate(
            ['date' => $date, 'currency' => 'EUR'],
            ['base' => 'EUR', 'rate' => 1.0]
        );

        // find child Cube nodes that have currency/rate
        $rateNodes = $xpath->query("//*[local-name()='Cube' and @currency and @rate]");
        foreach ($rateNodes as $node) {
            $currency = $node->getAttribute('currency');
            $rate = (float) $node->getAttribute('rate');
            ExchangeRate::updateOrCreate(
                ['date' => $date, 'currency' => $currency],
                ['base' => 'EUR', 'rate' => $rate]
            );
        }

        $this->info('ECB rates stored for '.$date);
        return 0;
    }
}
