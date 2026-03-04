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
            if ($latest && $latest->recorded_at && $latest->recorded_at->gt(now()->subMinutes(10))) {
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

        if (empty($data['average_price']) || $data['average_price'] <= 0) {
            $tmp = new Investment($data);
            $price = $marketData->getPrice($tmp);
            if ($price) {
                $data['average_price'] = $price['price'];
            } else {
                return back()->withErrors(['average_price' => 'Nepodařilo se stáhnout aktuální cenu. Zkuste to znovu nebo zadejte průměrnou cenu ručně.']);
            }
        }

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
        $investments = Investment::where('team_id', $teamId)->get();

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

        return back()->with('success', $updated > 0 ? 'Prices refreshed.' : 'No prices updated. Check API settings.');
    }

    protected function authorizeInvestment(Request $request, Investment $investment): void
    {
        if ($investment->team_id !== ($request->user()->currentTeam->id ?? null)) {
            abort(403);
        }
    }
}
