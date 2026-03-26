<?php

namespace App\Http\Controllers;

use App\Models\Investment;
use App\Models\Transaction;
use App\Services\CurrencyConverter;
use App\Services\ExpenseForecastService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __construct(
        private CurrencyConverter $converter,
        private ExpenseForecastService $forecastService
    ) {}

    public function index(Request $request): View
    {
        $user = $request->user();
        $team = $user->currentTeam;
        if (!$team) {
            return view('workspace.join');
        }
        $teamId = $team->id;
        $defaultCurrency = $team->default_currency;
        $currencySymbol = $team->getCurrencySymbol();

        $month = now()->month;
        $year = now()->year;

        $cashBalance = $this->calculateCashBalance($teamId, $team, $defaultCurrency);
        $investmentPortfolioValue = $this->calculateInvestmentPortfolioValue($teamId, $team);

        $totalBalance = $cashBalance + $investmentPortfolioValue;

        $monthlyTransactions = Transaction::where('team_id', $teamId)
            ->where('type', 'expense')
            ->whereYear('created_at', $year)
            ->whereMonth('created_at', $month)
            ->get();
        
        $monthlyExpenses = $this->sumTransactionsInDefaultCurrency($monthlyTransactions, $team);

        $monthlyIncomeTransactions = Transaction::where('team_id', $teamId)
            ->where('type', 'income')
            ->whereYear('created_at', $year)
            ->whereMonth('created_at', $month)
            ->get();
        
        $monthlyIncome = $this->sumTransactionsInDefaultCurrency($monthlyIncomeTransactions, $team);

        $lastMonthTransactions = Transaction::where('team_id', $teamId)
            ->where('type', 'expense')
            ->whereYear('created_at', now()->subMonth()->year)
            ->whereMonth('created_at', now()->subMonth()->month)
            ->get();
        
        $lastMonthExpenses = $this->sumTransactionsInDefaultCurrency($lastMonthTransactions, $team);
        $expenseTrend = $lastMonthExpenses > 0 ? (($monthlyExpenses - $lastMonthExpenses) / $lastMonthExpenses * 100) : 0;

        $lastMonthIncomeTransactions = Transaction::where('team_id', $teamId)
            ->where('type', 'income')
            ->whereYear('created_at', now()->subMonth()->year)
            ->whereMonth('created_at', now()->subMonth()->month)
            ->get();
        
        $lastMonthIncome = $this->sumTransactionsInDefaultCurrency($lastMonthIncomeTransactions, $team);
        $incomeTrend = $lastMonthIncome > 0 ? (($monthlyIncome - $lastMonthIncome) / $lastMonthIncome * 100) : 0;

        $forecast = $this->forecastService->forecastMonthly($user->id, $teamId, 6);

        $months = [];
        $expenseData = [];
        $incomeData = [];

        for ($i = 5; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $months[] = $date->format('M Y');

            $expenses = Transaction::where('team_id', $teamId)
                ->where('type', 'expense')
                ->whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month)
                ->get();
            
            $income = Transaction::where('team_id', $teamId)
                ->where('type', 'income')
                ->whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month)
                ->get();

            $expenseData[] = $this->sumTransactionsInDefaultCurrency($expenses, $team);
            $incomeData[] = $this->sumTransactionsInDefaultCurrency($income, $team);
        }

        $chartDatasets = [
            [
                'label' => __('Expenses'),
                'data' => $expenseData,
                'backgroundColor' => '#3b82f6',
                'borderColor' => '#1e40af',
                'borderWidth' => 1
            ],
            [
                'label' => __('Income'),
                'data' => $incomeData,
                'backgroundColor' => '#eab308',
                'borderColor' => '#ca8a04',
                'borderWidth' => 1
            ]
        ];

        $categories = $team->categories()->where('monthly_budget', '>', 0)->get();

        $recentTransactions = Transaction::where('team_id', $teamId)
            ->with(['category'])
            ->latest()
            ->take(5)
            ->get();

        return view('dashboard', compact(
            'totalBalance',
            'cashBalance',
            'investmentPortfolioValue',
            'monthlyExpenses',
            'monthlyIncome',
            'expenseTrend',
            'incomeTrend',
            'forecast',
            'months',
            'chartDatasets',
            'categories',
            'defaultCurrency',
            'currencySymbol',
            'recentTransactions',
            'team'
        ));
    }

    public function liveBalance(Request $request): JsonResponse
    {
        $team = $request->user()->currentTeam;
        $teamId = $team->id;
        $defaultCurrency = $team->default_currency;

        $cashBalance = $this->calculateCashBalance($teamId, $team, $defaultCurrency);
        $investmentPortfolioValue = $this->calculateInvestmentPortfolioValue($teamId, $team);
        $totalBalance = $cashBalance + $investmentPortfolioValue;

        return response()->json([
            'total_balance' => $totalBalance,
            'cash_balance' => $cashBalance,
            'investment_portfolio_value' => $investmentPortfolioValue,
            'currency_symbol' => $team->getCurrencySymbol(),
            'default_currency' => $defaultCurrency,
            'updated_at' => now()->toISOString(),
        ]);
    }

    private function calculateCashBalance(int $teamId, $team, string $defaultCurrency): float
    {
        $allTransactions = Transaction::where('team_id', $teamId)->get();

        $cashBalance = 0.0;
        foreach ($allTransactions as $transaction) {
            $amount = $transaction->amount;
            if ($transaction->currency !== $defaultCurrency) {
                try {
                    $amount = $team->convertToDefaultCurrency($amount, $transaction->currency, $transaction->created_at);
                } catch (\Exception $e) {
                    $amount = 0;
                }
            }
            $cashBalance += $transaction->type === 'income' ? $amount : -$amount;
        }

        return $cashBalance;
    }

    private function calculateInvestmentPortfolioValue(int $teamId, $team): float
    {
        $investmentPortfolioValue = 0.0;
        $investments = Investment::where('team_id', $teamId)
            ->with('latestPrice')
            ->get();

        foreach ($investments as $investment) {
            $latestPrice = $investment->latestPrice?->price;
            if (! $latestPrice) {
                continue;
            }

            $valueInPriceCurrency = $latestPrice * $investment->quantity;
            $priceCurrency = $investment->latestPrice?->currency ?? 'USD';

            try {
                $investmentPortfolioValue += $team->convertToDefaultCurrency($valueInPriceCurrency, $priceCurrency);
            } catch (\Exception $e) {
            }
        }

        return $investmentPortfolioValue;
    }

    /**
     * Sum transactions in default currency
     */
    private function sumTransactionsInDefaultCurrency($transactions, $team): float
    {
        $sum = 0;
        foreach ($transactions as $transaction) {
            $amount = $transaction->amount;
            if ($transaction->currency !== $team->default_currency) {
                try {
                    $amount = $team->convertToDefaultCurrency($amount, $transaction->currency, $transaction->created_at);
                } catch (\Exception $e) {
                    $amount = 0;
                }
            }
            $sum += $amount;
        }
        return $sum;
    }
}
