<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Budget extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'category_id',
        'amount',
        'period',
        'currency',
        'starts_at',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'starts_at' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function isExceeded($spentAmount): bool
    {
        return $spentAmount > $this->amount;
    }
}
