<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InvestmentPrice extends Model
{
    use HasFactory;

    protected $fillable = [
        'investment_id',
        'price',
        'currency',
        'recorded_at',
        'source',
    ];

    protected $casts = [
        'price' => 'float',
        'recorded_at' => 'datetime',
    ];

    public function investment(): BelongsTo
    {
        return $this->belongsTo(Investment::class);
    }
}
