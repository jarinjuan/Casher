<?php

namespace App\Services;

use App\Models\Transaction;
use Illuminate\Support\Facades\DB;

class ExpenseForecastService
{
    /**
     * 
     * @param int $userId
     * @param int $teamId
     * @param int $months
     * @param string|null $categoryId
     * @return float
     */
    public function forecastMonthly(int $userId, int $teamId, int $months = 6, $categoryId = null): float
    {
        $query = Transaction::query()
            ->where('user_id', $userId)
            ->where('team_id', $teamId)
            ->where('type', 'expense')
            ->where('created_at', '>=', now()->subMonths($months)->startOfMonth());
        if ($categoryId) {
            $query->where('category_id', $categoryId);
        }
        $expenses = $query->get();
        if ($expenses->isEmpty()) {
            return 0;
        }
        
        $team = \App\Models\Team::find($teamId);
        $byMonth = $expenses->groupBy(function($t) {
            return $t->created_at->format('Y-m');
        });
        $monthlySums = $byMonth->map(function($group) use ($team) {
            $sum = 0.0;
            foreach ($group as $expense) {
                $amount = $expense->amount;
                if ($team && $expense->currency !== $team->default_currency) {
                    try {
                        $amount = $team->convertToDefaultCurrency($amount, $expense->currency, $expense->created_at);
                    } catch (\Exception $e) {}
                }
                $sum += $amount;
            }
            return $sum;
        });
        return $monthlySums->avg() ?? 0.0;
    }
}
