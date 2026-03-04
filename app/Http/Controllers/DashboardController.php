<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Services\CurrencyConverter;
use App\Services\ExpenseForecastService;
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
        $teamId = $team->id;
        $defaultCurrency = $team->default_currency;
        $currencySymbol = $team->getCurrencySymbol();

        $month = now()->month;
        $year = now()->year;

        // Get all transactions
        $allTransactions = Transaction::where('team_id', $teamId)->get();
        
        // Calculate total balance in default currency
        $totalBalance = 0;
        foreach ($allTransactions as $transaction) {
            $amount = $transaction->amount;
            if ($transaction->currency !== $defaultCurrency) {
                try {
                    $amount = $team->convertToDefaultCurrency($amount, $transaction->currency, $transaction->created_at);
                } catch (\Exception $e) {
                    // Keep original amount if conversion fails
                }
            }
            $totalBalance += $transaction->type === 'income' ? $amount : -$amount;
        }

        // Monthly expenses
        $monthlyTransactions = Transaction::where('team_id', $teamId)
            ->where('type', 'expense')
            ->whereYear('created_at', $year)
            ->whereMonth('created_at', $month)
            ->get();
        
        $monthlyExpenses = $this->sumTransactionsInDefaultCurrency($monthlyTransactions, $team);

        // Monthly income
        $monthlyIncomeTransactions = Transaction::where('team_id', $teamId)
            ->where('type', 'income')
            ->whereYear('created_at', $year)
            ->whereMonth('created_at', $month)
            ->get();
        
        $monthlyIncome = $this->sumTransactionsInDefaultCurrency($monthlyIncomeTransactions, $team);

        // Last month expenses for trend
        $lastMonthTransactions = Transaction::where('team_id', $teamId)
            ->where('type', 'expense')
            ->whereYear('created_at', now()->subMonth()->year)
            ->whereMonth('created_at', now()->subMonth()->month)
            ->get();
        
        $lastMonthExpenses = $this->sumTransactionsInDefaultCurrency($lastMonthTransactions, $team);
        $expenseTrend = $lastMonthExpenses > 0 ? (($monthlyExpenses - $lastMonthExpenses) / $lastMonthExpenses * 100) : 0;

        // Last month income for trend
        $lastMonthIncomeTransactions = Transaction::where('team_id', $teamId)
            ->where('type', 'income')
            ->whereYear('created_at', now()->subMonth()->year)
            ->whereMonth('created_at', now()->subMonth()->month)
            ->get();
        
        $lastMonthIncome = $this->sumTransactionsInDefaultCurrency($lastMonthIncomeTransactions, $team);
        $incomeTrend = $lastMonthIncome > 0 ? (($monthlyIncome - $lastMonthIncome) / $lastMonthIncome * 100) : 0;

        // Forecast
        $forecast = $this->forecastService->forecastMonthly($user->id, $teamId, 6);

        // Chart data for last 6 months
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
                'label' => 'Expenses',
                'data' => $expenseData,
                'backgroundColor' => '#3b82f6',
                'borderColor' => '#1e40af',
                'borderWidth' => 1
            ],
            [
                'label' => 'Income',
                'data' => $incomeData,
                'backgroundColor' => '#eab308',
                'borderColor' => '#ca8a04',
                'borderWidth' => 1
            ]
        ];

        // Categories with budgets
        $categories = $user->categories()->where('monthly_budget', '>', 0)->get();

        return view('dashboard', compact(
            'totalBalance',
            'monthlyExpenses',
            'monthlyIncome',
            'expenseTrend',
            'incomeTrend',
            'forecast',
            'months',
            'chartDatasets',
            'categories',
            'defaultCurrency',
            'currencySymbol'
        ));
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
                    // Keep original amount if conversion fails
                    logger()->warning("Failed to convert {$transaction->currency} to {$team->default_currency}");
                }
            }
            $sum += $amount;
        }
        return $sum;
    }
}
