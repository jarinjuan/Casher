<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'color',
        'monthly_budget',
        'budget_currency',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    public function budgets()
    {
        return $this->hasMany(Budget::class);
    }

    public function getMonthlySpent($year = null, $month = null)
    {
        if (!$year) $year = now()->year;
        if (!$month) $month = now()->month;

        return $this->transactions()
            ->where('type', 'expense')
            ->whereYear('created_at', $year)
            ->whereMonth('created_at', $month)
            ->sum('amount');
    }

    public function getMonthlyBudgetPercentage($year = null, $month = null)
    {
        if (!$this->monthly_budget) return 0;
        $spent = $this->getMonthlySpent($year, $month);
        return min(100, ($spent / $this->monthly_budget) * 100);
    }
}
