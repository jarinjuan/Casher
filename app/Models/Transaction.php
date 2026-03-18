<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'team_id',
        'title',
        'amount',
        'type',
        'note',
        'category_id',
        'currency',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    protected static function booted()
    {
        static::created(function (Transaction $transaction) {
            if ($transaction->type !== 'expense') {
                return;
            }

            $user = $transaction->user;
            if (! $user) {
                return;
            }

            $budgets = Budget::where('user_id', $user->id)
                ->where(function ($q) use ($transaction) {
                    $q->where('category_id', $transaction->category_id)
                      ->orWhereNull('category_id');
                })->get();

            foreach ($budgets as $budget) {
                $periodStart = now();
                if ($budget->period === 'monthly') {
                    $periodStart = now()->startOfMonth();
                } else {
                    $periodStart = now()->startOfYear();
                }

                $expenses = Transaction::where('user_id', $user->id)
                    ->where('type', 'expense')
                    ->where('category_id', $budget->category_id)
                    ->where('created_at', '>=', $periodStart)
                    ->get();
                    
                $converter = app(\App\Services\CurrencyConverter::class);
                $spent = 0.0;
                foreach ($expenses as $expense) {
                    $amount = $expense->amount;
                    if ($expense->currency !== $budget->currency) {
                        try {
                            $amount = $converter->convert($amount, $expense->currency, $budget->currency, $expense->created_at);
                        } catch (\Exception $e) {
                            // keep original
                        }
                    }
                    $spent += $amount;
                }

                if ($budget->isExceeded($spent)) {
                    $user->notify(new \App\Notifications\BudgetExceeded($budget, $spent));
                }
            }
        });
    }
}
