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
        
        $byMonth = $expenses->groupBy(function($t) {
            return $t->created_at->format('Y-m');
        });
        $monthlySums = $byMonth->map(function($group) {
            return $group->sum('amount');
        });
        return $monthlySums->avg();
    }
}
