<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;

class ChartController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request): View
    {
        $user = $request->user();
        $team = clone $user->currentTeam;
        $teamId = $team->id;

        // 1. Expenses by category (Main Pie)
        $categories = $team->categories()->orderBy('name')->get();
        $expenseLabels = [];
        $expenseData = [];
        $expenseColors = [];

        foreach ($categories as $cat) {
            $expenses = \App\Models\Transaction::where('team_id', '=', $teamId)
                ->where('type', '=', 'expense')
                ->where('category_id', '=', $cat->id)
                ->get();
            
            $sum = 0.0;
            foreach ($expenses as $expense) {
                $amount = $expense->amount;
                if ($expense->currency !== $team->default_currency) {
                    try {
                        $amount = $team->convertToDefaultCurrency($amount, $expense->currency, $expense->created_at);
                    } catch (\Exception $e) {}
                }
                $sum += $amount;
            }
            if ($sum <= 0) continue;
            $expenseLabels[] = $cat->name;
            $expenseData[] = (float) $sum;
            $expenseColors[] = $cat->color ?? '#fbbf24';
        }

        $uncatExpTransactions = \App\Models\Transaction::where('team_id', '=', $teamId)
            ->where('type', '=', 'expense')
            ->whereNull('category_id')
            ->get();
        $uncatSum = 0.0;
        foreach ($uncatExpTransactions as $expense) {
            $amount = $expense->amount;
            if ($expense->currency !== $team->default_currency) {
                try {
                    $amount = $team->convertToDefaultCurrency($amount, $expense->currency, $expense->created_at);
                } catch (\Exception $e) {}
            }
            $uncatSum += $amount;
        }
        
        if ($uncatSum > 0) {
            $expenseLabels[] = __('Uncategorized');
            $expenseData[] = (float) $uncatSum;
            $expenseColors[] = '#d1d5db';
        }

        // 2. Trend Chart (12 Months)
        $trendLabels = [];
        $trendIncome = [];
        $trendExpense = [];

        for ($i = 11; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $trendLabels[] = $date->format('m/Y');

            $income = \App\Models\Transaction::where('team_id', '=', $teamId)
                ->where('type', '=', 'income')
                ->whereYear('created_at', '=', $date->year)
                ->whereMonth('created_at', '=', $date->month)
                ->sum('amount');

            $expense = \App\Models\Transaction::where('team_id', '=', $teamId)
                ->where('type', '=', 'expense')
                ->whereYear('created_at', '=', $date->year)
                ->whereMonth('created_at', '=', $date->month)
                ->sum('amount');

            $trendIncome[] = (float) $income;
            $trendExpense[] = (float) $expense;
        }

        // 3. Monthly Bar Chart (Last 6 Months)
        $last6Labels = [];
        $last6Income = [];
        $last6Expense = [];

        for ($i = 5; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $last6Labels[] = $date->format('m/Y');

            $income = \App\Models\Transaction::where('team_id', '=', $teamId)
                ->where('type', '=', 'income')
                ->whereYear('created_at', '=', $date->year)
                ->whereMonth('created_at', '=', $date->month)
                ->sum('amount');

            $expense = \App\Models\Transaction::where('team_id', '=', $teamId)
                ->where('type', '=', 'expense')
                ->whereYear('created_at', '=', $date->year)
                ->whereMonth('created_at', '=', $date->month)
                ->sum('amount');

            $last6Income[] = (float) $income;
            $last6Expense[] = (float) $expense;
        }

        // 4. Income sources by category (doughnut)
        $incomeTransactions = \App\Models\Transaction::where('team_id', '=', $teamId)
            ->where('type', '=', 'income')
            ->get();

        $incomeMap = []; // category_id => total
        foreach ($incomeTransactions as $tx) {
            $catId = $tx->category_id ?? 0;
            $amount = $tx->amount;
            if ($tx->currency !== $team->default_currency) {
                try {
                    $amount = $team->convertToDefaultCurrency($amount, $tx->currency, $tx->created_at);
                } catch (\Exception $e) {}
            }
            $incomeMap[$catId] = ($incomeMap[$catId] ?? 0) + $amount;
        }
        arsort($incomeMap);

        $allCategories = \App\Models\Category::where('team_id', '=', $teamId)->get()->keyBy('id');

        $incomeSourceLabels = [];
        $incomeSourceData = [];
        $incomeSourceColors = [];

        foreach ($incomeMap as $catId => $total) {
            $cat = $allCategories->get($catId);
            $incomeSourceLabels[] = $cat?->name ?? __('Uncategorized');
            $incomeSourceData[] = (float) $total;
            $incomeSourceColors[] = $cat?->color ?? '#fbbf24';
        }

        // 5. Top spending categories (bar)
        $expenseTransactionsForTop = \App\Models\Transaction::where('team_id', '=', $teamId)
            ->where('type', '=', 'expense')
            ->get();

        $expenseTopMap = []; // category_id => total
        foreach ($expenseTransactionsForTop as $tx) {
            $catId = $tx->category_id ?? 0;
            $amount = $tx->amount;
            if ($tx->currency !== $team->default_currency) {
                try {
                    $amount = $team->convertToDefaultCurrency($amount, $tx->currency, $tx->created_at);
                } catch (\Exception $e) {}
            }
            $expenseTopMap[$catId] = ($expenseTopMap[$catId] ?? 0) + $amount;
        }
        arsort($expenseTopMap);
        $topExpenseSubset = array_slice($expenseTopMap, 0, 5, true);

        $topCategoryLabels = [];
        $topCategoryData = [];
        $topCategoryColors = [];

        foreach ($topExpenseSubset as $catId => $total) {
            $cat = $allCategories->get($catId);
            $topCategoryLabels[] = $cat?->name ?? __('Uncategorized');
            $topCategoryData[] = (float) $total;
            $topCategoryColors[] = $cat?->color ?? '#8b5cf6';
        }

        return view('charts.index', [
            'expenseLabels' => $expenseLabels,
            'expenseData' => $expenseData,
            'expenseColors' => $expenseColors,
            'trendLabels' => $trendLabels,
            'trendIncome' => $trendIncome,
            'trendExpense' => $trendExpense,
            'last6Labels' => $last6Labels,
            'last6Income' => $last6Income,
            'last6Expense' => $last6Expense,
            'incomeSourceLabels' => $incomeSourceLabels,
            'incomeSourceData' => $incomeSourceData,
            'incomeSourceColors' => $incomeSourceColors,
            'topCategoryLabels' => $topCategoryLabels,
            'topCategoryData' => $topCategoryData,
            'topCategoryColors' => $topCategoryColors,
        ]);
    }
}
