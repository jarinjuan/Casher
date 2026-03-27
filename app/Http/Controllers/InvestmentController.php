<?php

namespace App\Http\Controllers;

use App\Http\Requests\InvestmentRequest;
use App\Models\Investment;
use App\Models\InvestmentPrice;
use App\Services\MarketDataService;
use App\Services\PortfolioAnalyticsService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;

class InvestmentController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request, PortfolioAnalyticsService $analytics, MarketDataService $marketData): View
    {
        $team = $request->user()->currentTeam;
        $teamId = $team->id ?? null;
        $defaultCurrency = $team->default_currency;
        $currencySymbol = $team->getCurrencySymbol();

        $investments = Investment::where('team_id', $teamId)
            ->with('latestPrice')
            ->orderByDesc('created_at')
            ->get();

        $totalValue = 0.0;
        $totalCost = 0.0;

        foreach ($investments as $investment) {
            $latestPrice = $investment->latestPrice?->price;
            $latestPriceCurrency = $investment->latestPrice?->currency ?? 'USD';
            
            if ($latestPrice) {
                $valueInOriginal = $latestPrice * $investment->quantity;
                $valueInDefault = $team->convertToDefaultCurrency($valueInOriginal, $latestPriceCurrency);
                $totalValue += $valueInDefault;
            }
            
            $costInOriginal = $investment->average_price * $investment->quantity;
            $costInDefault = $team->convertToDefaultCurrency($costInOriginal, $investment->currency);
            $totalCost += $costInDefault;
        }

        $profit = $totalValue - $totalCost;
        $profitPct = $totalCost > 0 ? ($profit / $totalCost) * 100 : 0;

        $dailySeries = $analytics->buildSeries($investments, now()->subDays(30), 'day', $team);
        $monthlySeries = $analytics->buildSeries($investments, now()->subMonths(12), 'month', $team);

        $dailyChangePct = $analytics->lastStepChangePercent($dailySeries);
        $monthlyChangePct = $analytics->changePercentFromSeries($monthlySeries);

        return view('investments.index', [
            'investments' => $investments,
            'totalValue' => $totalValue,
            'totalCost' => $totalCost,
            'profit' => $profit,
            'profitPct' => $profitPct,
            'dailySeries' => $dailySeries,
            'monthlySeries' => $monthlySeries,
            'dailyChangePct' => $dailyChangePct,
            'monthlyChangePct' => $monthlyChangePct,
            'defaultCurrency' => $defaultCurrency,
            'currencySymbol' => $currencySymbol,
            'team' => $team,
        ]);
    }

    public function store(InvestmentRequest $request): RedirectResponse
    {
        $team = $request->user()->currentTeam;
        if (!$team) {
            return redirect()->route('dashboard')->with('error', __('No workspace selected'));
        }

        $this->authorize('create', Investment::class);

        $data = $request->validated();
        $data['user_id'] = $request->user()->id;
        $data['team_id'] = $team->id;
        $data['symbol'] = strtoupper($data['symbol']);
        if (isset($data['currency'])) {
            $data['currency'] = strtoupper($data['currency']);
        }

        $marketData = app(MarketDataService::class);

        if ($data['type'] === 'crypto' && empty($data['external_id'] ?? null)) {
            $externalId = $marketData->cryptoIdFromSymbol($data['symbol']);
            if ($externalId) {
                $data['external_id'] = $externalId;
            }
        }

        $tmp = new Investment($data);
        $price = $marketData->getPrice($tmp);
        if ($price) {
            $data['average_price'] = $price['price'];
            $data['currency'] = strtoupper($price['currency']);
        } else {
            return back()->withErrors(['symbol' => __('Symbol not supported.')])->withInput();
        }

        if ($data['buy_mode'] === 'amount') {
            $amountInDefault = (float) $data['amount'];
            
            $converter = app(\App\Services\CurrencyConverter::class);
            $team = $request->user()->currentTeam;
            try {
                $amountInTargetCurrency = $converter->convert($amountInDefault, $team->default_currency, $data['currency']);
            } catch (\Exception $e) {
                return back()->withErrors(['amount' => __('Currency conversion error. Please check the current exchange rates.')])->withInput();
            }

            $data['quantity'] = $amountInTargetCurrency / (float) $data['average_price'];
        }

        unset($data['buy_mode']);
        unset($data['amount']);

        $existing = Investment::where('team_id', $data['team_id'])
            ->where('type', $data['type'])
            ->where('symbol', $data['symbol'])
            ->first();

        if ($existing) {
            $oldQty = (float) $existing->quantity;
            $newQty = (float) $data['quantity'];
            $oldAvg = (float) $existing->average_price;
            $newAvg = (float) $data['average_price'];
            
            if ($existing->currency !== $data['currency']) {
                try {
                    $converter = app(\App\Services\CurrencyConverter::class);
                    $oldAvg = $converter->convert($oldAvg, $existing->currency, $data['currency']);
                } catch (\Exception $e) {
                    return back()->withErrors(['currency' => __('Failed to convert currencies for merging investments. Please check the API or enter a compatible currency.')])->withInput();
                }
            }
            
            $totalQty = $oldQty + $newQty;

            $weightedAvg = $totalQty > 0
                ? (($oldQty * $oldAvg) + ($newQty * $newAvg)) / $totalQty
                : $newAvg;

            $existing->update([
                'quantity' => $totalQty,
                'average_price' => $weightedAvg,
                'name' => $data['name'] ?: $existing->name,
                'external_id' => ($data['external_id'] ?? null) ?: $existing->external_id,
                'currency' => $data['currency'],
            ]);

            return back()->with('success', __('Investment updated (merged with existing).'));
        }

        Investment::create($data);

        return back()->with('success', __('Investment added.'));
    }

    public function edit(Request $request, Investment $investment): View
    {
        $this->authorize('update', $investment);

        return view('investments.edit', [
            'investment' => $investment,
        ]);
    }

    public function update(InvestmentRequest $request, Investment $investment): RedirectResponse
    {
        $this->authorize('update', $investment);

        $data = $request->validated();
        $data['symbol'] = strtoupper($data['symbol']);
        $data['currency'] = strtoupper($data['currency']);

        if (! isset($data['average_price']) || $data['average_price'] === null || $data['average_price'] === '') {
            unset($data['average_price']);
        }

        $investment->update($data);

        return back()->with('success', __('Investment updated.'));
    }

    public function search(Request $request, MarketDataService $marketData): JsonResponse
    {
        $query = (string) $request->query('q', '');
        $type = (string) $request->query('type', 'stock');
        if (mb_strlen($query) < 1) {
            return response()->json([]);
        }

        if ($type === 'crypto') {
            return response()->json($marketData->searchCrypto($query));
        }

        return response()->json($marketData->searchStocks($query));
    }

    public function price(Request $request, MarketDataService $marketData, \App\Services\CurrencyConverter $converter): JsonResponse
    {
        $type = $request->query('type');
        $symbol = $request->query('symbol');
        $externalId = $request->query('external_id');
        
        if (!$symbol) {
            return response()->json(['error' => 'Missing symbol'], 400);
        }

        $tmp = new Investment([
            'type' => $type,
            'symbol' => strtoupper($symbol),
            'external_id' => $externalId,
        ]);
        
        $priceData = $marketData->getPrice($tmp);
        if (!$priceData) {
            return response()->json(['error' => 'Price not found'], 404);
        }
        
        $team = $request->user()->currentTeam;
        $priceInDefault = $priceData['price'];
        try {
           $priceInDefault = $converter->convert($priceData['price'], $priceData['currency'], $team->default_currency);
        } catch (\Exception $e) {
            // fallback to original if conversion fails
        }
        
        return response()->json([
            'price' => $priceData['price'],
            'currency' => $priceData['currency'],
            'price_in_default' => $priceInDefault,
            'default_currency' => $team->default_currency,
        ]);
    }

    public function destroy(Request $request, Investment $investment): RedirectResponse
    {
        $this->authorize('delete', $investment);
        $investment->delete();

        return back()->with('success', __('Investment deleted.'));
    }

    public function refresh(Request $request, MarketDataService $marketData): RedirectResponse
    {
        $this->authorize('create', Investment::class);
        $teamId = $request->user()->currentTeam->id ?? null;
        $investments = Investment::where('team_id', $teamId)->with('latestPrice')->get();

        $updated = 0;
        foreach ($investments as $investment) {
            $latest = $investment->latestPrice;
            if ($latest && $latest->recorded_at && $latest->recorded_at->gt(now()->subMinutes(5))) {
                continue;
            }

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

        if ($updated === 0) {
            return back()->withErrors(['refresh' => __('Prices are already up to date (last updated less than 5 minutes ago).')]);
        }

        return back()->with('success', __('Prices refreshed.'));
    }

    public function livePrices(Request $request, MarketDataService $marketData): JsonResponse
    {
        $team = $request->user()->currentTeam;
        $teamId = $team->id ?? null;

        $investments = Investment::where('team_id', $teamId)
            ->with('latestPrice')
            ->get();

        $refreshed = false;
        foreach ($investments as $investment) {
            $latest = $investment->latestPrice;
            if ($latest && $latest->recorded_at && $latest->recorded_at->gt(now()->subMinutes(15))) {
                continue;
            }
            $price = $marketData->getPrice($investment);
            if (! $price) {
                continue;
            }
            InvestmentPrice::create([
                'investment_id' => $investment->id,
                'price'         => $price['price'],
                'currency'      => $price['currency'],
                'recorded_at'   => now(),
                'source'        => $price['source'],
            ]);
            $refreshed = true;
        }

        if ($refreshed) {
            $investments = Investment::where('team_id', $teamId)
                ->with('latestPrice')
                ->get();
        }

        $totalValue  = 0.0;
        $totalCost   = 0.0;
        $items       = [];

        foreach ($investments as $investment) {
            $lastPrice         = $investment->latestPrice?->price;
            $lastPriceCurrency = $investment->latestPrice?->currency ?? 'USD';
            $value             = $lastPrice ? $lastPrice * $investment->quantity : null;
            $valueInDefault    = $value ? $team->convertToDefaultCurrency($value, $lastPriceCurrency) : null;

            $costInOriginal    = $investment->average_price * $investment->quantity;
            $costInDefault     = $team->convertToDefaultCurrency($costInOriginal, $investment->currency);

            $plInDefault       = $valueInDefault !== null ? $valueInDefault - $costInDefault : null;
            $plPct             = $costInDefault > 0 && $valueInDefault !== null
                                    ? (($valueInDefault - $costInDefault) / $costInDefault) * 100 : null;

            if ($valueInDefault) {
                $totalValue += $valueInDefault;
            }
            $totalCost += $costInDefault;

            $items[] = [
                'id' => $investment->id,
                'symbol' => $investment->symbol,
                'last_price' => $lastPrice,
                'last_price_currency'=> $lastPriceCurrency,
                'value_raw' => $value,
                'value_in_default' => $valueInDefault,
                'pl_in_default' => $plInDefault,
                'pl_pct' => $plPct,
                'recorded_at' => $investment->latestPrice?->recorded_at?->toISOString(),
            ];
        }

        $profit    = $totalValue - $totalCost;
        $profitPct = $totalCost > 0 ? ($profit / $totalCost) * 100 : 0;

        return response()->json([
            'investments'      => $items,
            'total_value'      => $totalValue,
            'total_cost'       => $totalCost,
            'profit'           => $profit,
            'profit_pct'       => $profitPct,
            'currency_symbol'  => $team->getCurrencySymbol(),
            'default_currency' => $team->default_currency,
            'updated_at'       => now()->toISOString(),
        ]);
    }

}
