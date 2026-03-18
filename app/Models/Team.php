<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Team extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'user_id',
        'invite_code',
        'default_currency',
    ];

    /**
     * Get currency symbol for the team's default currency
     */
    public function getCurrencySymbol(): string
    {
        return match($this->default_currency) {
            'CZK' => 'CZK',
            'EUR' => '€',
            'USD' => '$',
            'GBP' => '£',
            'JPY' => '¥',
            default => $this->default_currency,
        };
    }

    /**
     * Convert amount to team's default currency
     */
    public function convertToDefaultCurrency(float $amount, string $fromCurrency, $date = null): float
    {
        if ($fromCurrency === $this->default_currency) {
            return $amount;
        }

        try {
            $converter = app(\App\Services\CurrencyConverter::class);
            return $converter->convert($amount, $fromCurrency, $this->default_currency, $date);
        } catch (\Exception $e) {
            logger()->warning("Currency conversion failed: {$e->getMessage()}");
            return $amount;
        }
    }

    public function owner()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function users()
    {
        return $this->belongsToMany(User::class)->withPivot('role');
    }

    public function investments()
    {
        return $this->hasMany(Investment::class);
    }
}