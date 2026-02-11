<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Investment extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'team_id',
        'type',
        'symbol',
        'name',
        'external_id',
        'quantity',
        'average_price',
        'currency',
    ];

    protected $casts = [
        'quantity' => 'float',
        'average_price' => 'float',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }

    public function prices(): HasMany
    {
        return $this->hasMany(InvestmentPrice::class);
    }

    public function latestPrice(): HasOne
    {
        return $this->hasOne(InvestmentPrice::class)->latest('recorded_at');
    }
}
