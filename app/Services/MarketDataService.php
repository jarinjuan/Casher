<?php

namespace App\Services;

use App\Models\Investment;
use Illuminate\Support\Facades\Http;

class MarketDataService
{
    public function searchStocks(string $query): array
    {
        $apiKey = config('services.stocks.key');
        if (! $apiKey || trim($query) === '') {
            return [];
        }

        $response = Http::timeout(10)->get('https://api.twelvedata.com/symbol_search', [
            'symbol' => $query,
            'apikey' => $apiKey,
        ]);

        if (! $response->ok()) {
            return [];
        }

        $data = $response->json();
        $items = $data['data'] ?? [];

        return collect($items)
            ->take(10)
            ->map(fn ($item) => [
                'symbol' => $item['symbol'] ?? null,
                'name' => $item['instrument_name'] ?? ($item['name'] ?? null),
                'exchange' => $item['exchange'] ?? null,
                'country' => $item['country'] ?? null,
            ])
            ->filter(fn ($item) => $item['symbol'])
            ->values()
            ->all();
    }

    public function searchCrypto(string $query): array
    {
        if (trim($query) === '') {
            return [];
        }

        $response = Http::timeout(10)->get('https://api.coingecko.com/api/v3/search', [
            'query' => $query,
        ]);

        if (! $response->ok()) {
            return [];
        }

        $data = $response->json();
        $items = $data['coins'] ?? [];

        return collect($items)
            ->take(10)
            ->map(fn ($item) => [
                'symbol' => isset($item['symbol']) ? strtoupper($item['symbol']) : null,
                'name' => $item['name'] ?? null,
                'external_id' => $item['id'] ?? null,
            ])
            ->filter(fn ($item) => $item['symbol'] && $item['external_id'])
            ->values()
            ->all();
    }

    public function getPrice(Investment $investment): ?array
    {
        if ($investment->type === 'crypto') {
            return $this->getCryptoPrice($investment);
        }

        if ($investment->type === 'stock') {
            return $this->getStockPrice($investment);
        }

        return null;
    }

    protected function getCryptoPrice(Investment $investment): ?array
    {
        $id = $investment->external_id ?: $this->cryptoIdFromSymbol($investment->symbol);
        if (! $id) {
            return null;
        }

        $response = Http::timeout(10)->get('https://api.coingecko.com/api/v3/simple/price', [
            'ids' => $id,
            'vs_currencies' => 'usd',
        ]);

        if (! $response->ok()) {
            return null;
        }

        $data = $response->json();
        if (! isset($data[$id]['usd'])) {
            return null;
        }

        return [
            'price' => (float) $data[$id]['usd'],
            'currency' => 'USD',
            'source' => 'coingecko',
        ];
    }

    protected function getStockPrice(Investment $investment): ?array
    {
        $provider = config('services.stocks.provider', 'twelvedata');
        $apiKey = config('services.stocks.key');

        if (! $apiKey) {
            return null;
        }

        if ($provider === 'twelvedata') {
            $response = Http::timeout(10)->get('https://api.twelvedata.com/price', [
                'symbol' => $investment->symbol,
                'apikey' => $apiKey,
            ]);

            if (! $response->ok()) {
                return null;
            }

            $data = $response->json();
            if (! isset($data['price'])) {
                return null;
            }

            return [
                'price' => (float) $data['price'],
                'currency' => 'USD',
                'source' => 'twelvedata',
            ];
        }

        return null;
    }

    public function cryptoIdFromSymbol(string $symbol): ?string
    {
        $map = [
            'BTC' => 'bitcoin',
            'ETH' => 'ethereum',
            'SOL' => 'solana',
            'BNB' => 'binancecoin',
            'ADA' => 'cardano',
            'XRP' => 'ripple',
            'DOT' => 'polkadot',
            'DOGE' => 'dogecoin',
            'AVAX' => 'avalanche-2',
            'MATIC' => 'matic-network',
            'USDT' => 'tether',
            'USDC' => 'usd-coin',
            'LTC' => 'litecoin',
            'LINK' => 'chainlink',
            'UNI' => 'uniswap',
            'ATOM' => 'cosmos',
            'XLM' => 'stellar',
            'ALGO' => 'algorand',
            'VET' => 'vechain',
            'ICP' => 'internet-computer',
            'FIL' => 'filecoin',
            'TRX' => 'tron',
            'ETC' => 'ethereum-classic',
            'NEAR' => 'near',
            'APT' => 'aptos',
            'ARB' => 'arbitrum',
            'OP' => 'optimism',
            'SUI' => 'sui',
        ];

        $symbol = strtoupper($symbol);

        return $map[$symbol] ?? null;
    }
}
