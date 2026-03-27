<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'team_id',
        'name',
        'color',
        'monthly_budget',
        'budget_currency',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function team()
    {
        return $this->belongsTo(Team::class);
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }


    public function getMonthlySpent($year = null, $month = null)
    {
        if (!$year) $year = now()->year;
        if (!$month) $month = now()->month;

        $transactions = $this->transactions()
            ->where('type', 'expense')
            ->whereYear('created_at', $year)
            ->whereMonth('created_at', $month)
            ->get();

        $converter = app(\App\Services\CurrencyConverter::class);
        $total = 0.0;

        foreach ($transactions as $transaction) {
            $amount = $transaction->amount;
            if ($transaction->currency !== $this->budget_currency) {
                try {
                    $amount = $converter->convert($amount, $transaction->currency, $this->budget_currency, $transaction->created_at);
                } catch (\Exception $e) {
                    $amount = 0;
                }
            }
            $total += $amount;
        }

        return $total;
    }

    public function getMonthlyBudgetPercentage($year = null, $month = null)
    {
        if (!$this->monthly_budget) return 0;
        $spent = $this->getMonthlySpent($year, $month);
        return min(100, ($spent / $this->monthly_budget) * 100);
    }
}
