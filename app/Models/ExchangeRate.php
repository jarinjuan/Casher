<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExchangeRate extends Model
{
    use HasFactory;

    protected $fillable = ['date','base','currency','rate'];

    protected $casts = [
        'date' => 'date',
        'rate' => 'decimal:8',
    ];

    public static function latestRate(string $currency, $date = null)
    {
        $q = static::where('currency', $currency);
        if ($date) $q->where('date', $date);
        return $q->orderByDesc('date')->first();
    }
}
