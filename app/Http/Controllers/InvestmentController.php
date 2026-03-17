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
                'price' => $price['price'],
                'currency' => $price['currency'],
                'recorded_at' => now(),
                'source' => $price['source'],
            ]);
        }

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
                // Convert to default currency
                $valueInDefault = $team->convertToDefaultCurrency($valueInOriginal, $latestPriceCurrency);
                $totalValue += $valueInDefault;
            }
            
            $costInOriginal = $investment->average_price * $investment->quantity;
            // Convert cost to default currency (using investment currency)
            $costInDefault = $team->convertToDefaultCurrency($costInOriginal, $investment->currency);
            $totalCost += $costInDefault;
        }

        $profit = $totalValue - $totalCost;
        $profitPct = $totalCost > 0 ? ($profit / $totalCost) * 100 : 0;

        $dailySeries = $analytics->buildSeries($investments, now()->subDays(30), 'day');
        $monthlySeries = $analytics->buildSeries($investments, now()->subMonths(12), 'month');

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
        $data = $request->validated();
        $data['user_id'] = $request->user()->id;
        $data['team_id'] = $request->user()->currentTeam->id ?? null;
        $data['symbol'] = strtoupper($data['symbol']);
        $data['currency'] = strtoupper($data['currency']);

        $marketData = app(MarketDataService::class);

        // Pro crypto: pokud nebyl zadán external_id, pokus se ho najít podle symbolu
        if ($data['type'] === 'crypto' && empty($data['external_id'])) {
            $externalId = $marketData->cryptoIdFromSymbol($data['symbol']);
            if ($externalId) {
                $data['external_id'] = $externalId;
            }
        }

        // Vždy doměřit aktuální cenu z API pro tuto akci/krypto k výpočtu
        $tmp = new Investment($data);
        $price = $marketData->getPrice($tmp);
        if ($price) {
            $data['average_price'] = $price['price'];
        } else {
            return back()->withErrors(['average_price' => 'Nepodařilo se stáhnout aktuální cenu pro vložený symbol. Zkontrolujte prosím symbol a zkuste to znovu.']);
        }

        // Pokud uživatel nakupuje za "Total Amount", spočítáme výsledné množství
        if ($data['buy_mode'] === 'amount') {
            $data['quantity'] = (float) $data['amount'] / (float) $data['average_price'];
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
            $totalQty = $oldQty + $newQty;

            $weightedAvg = $totalQty > 0
                ? (($oldQty * $oldAvg) + ($newQty * $newAvg)) / $totalQty
                : $newAvg;

            $existing->update([
                'quantity' => $totalQty,
                'average_price' => $weightedAvg,
                'name' => $data['name'] ?: $existing->name,
                'external_id' => $data['external_id'] ?: $existing->external_id,
                'currency' => $data['currency'],
            ]);

            return back()->with('success', 'Investice byla aktualizována (sloučena s existující).');
        }

        Investment::create($data);

        return back()->with('success', 'Investice byla přidána.');
    }

    public function edit(Request $request, Investment $investment): View
    {
        $this->authorizeInvestment($request, $investment);

        return view('investments.edit', [
            'investment' => $investment,
        ]);
    }

    public function update(InvestmentRequest $request, Investment $investment): RedirectResponse
    {
        $this->authorizeInvestment($request, $investment);

        $data = $request->validated();
        $data['symbol'] = strtoupper($data['symbol']);
        $data['currency'] = strtoupper($data['currency']);

        if (! isset($data['average_price']) || $data['average_price'] === null || $data['average_price'] === '') {
            unset($data['average_price']);
        }

        $investment->update($data);

        return back()->with('success', 'Investment updated.');
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

    public function destroy(Request $request, Investment $investment): RedirectResponse
    {
        $this->authorizeInvestment($request, $investment);
        $investment->delete();

        return back()->with('success', 'Investment deleted.');
    }

    public function refresh(Request $request, MarketDataService $marketData): RedirectResponse
    {
        $teamId = $request->user()->currentTeam->id ?? null;
        $investments = Investment::where('team_id', $teamId)->with('latestPrice')->get();

        $updated = 0;
        foreach ($investments as $investment) {
            $latest = $investment->latestPrice;
            if ($latest && $latest->recorded_at && $latest->recorded_at->gt(now()->subMinutes(5))) {
                continue; // Zamezení spamování ručního refresh tlačítka (5 minutový cooldown)
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

        return back()->with('success', $updated > 0 ? 'Prices refreshed.' : 'No prices updated. Check API settings.');
    }

    public function livePrices(Request $request, MarketDataService $marketData): JsonResponse
    {
        $team = $request->user()->currentTeam;
        $teamId = $team->id ?? null;

        $investments = Investment::where('team_id', $teamId)
            ->with('latestPrice')
            ->get();

        // Refresh prices that are stale (older than 15 minutes to save API requests)
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
            $pl                = $lastPrice ? ($lastPrice - $investment->average_price) * $investment->quantity : null;
            $plPct             = $investment->average_price > 0 && $lastPrice
                                    ? (($lastPrice - $investment->average_price) / $investment->average_price) * 100
                                    : null;
            $valueInDefault    = $value ? $team->convertToDefaultCurrency($value, $lastPriceCurrency) : null;
            $plInDefault       = $pl    ? $team->convertToDefaultCurrency($pl,    $lastPriceCurrency) : null;

            if ($valueInDefault) {
                $totalValue += $valueInDefault;
            }
            $costInDefault = $team->convertToDefaultCurrency(
                $investment->average_price * $investment->quantity,
                $investment->currency
            );
            $totalCost += $costInDefault;

            $items[] = [
                'id'                 => $investment->id,
                'symbol'             => $investment->symbol,
                'last_price'         => $lastPrice,
                'last_price_currency'=> $lastPriceCurrency,
                'value_raw'          => $value,
                'value_in_default'   => $valueInDefault,
                'pl_in_default'      => $plInDefault,
                'pl_pct'             => $plPct,
                'recorded_at'        => $investment->latestPrice?->recorded_at?->toISOString(),
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

    protected function authorizeInvestment(Request $request, Investment $investment): void
    {
        if ($investment->team_id !== ($request->user()->currentTeam->id ?? null)) {
            abort(403);
        }
    }
}
