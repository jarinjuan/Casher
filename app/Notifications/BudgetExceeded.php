<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\DatabaseMessage;
use App\Models\Budget;

class BudgetExceeded extends Notification
{
    use Queueable;

    protected Budget $budget;
    protected float $spent;

    public function __construct(Budget $budget, float $spent)
    {
        $this->budget = $budget;
        $this->spent = $spent;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toDatabase($notifiable)
    {
        return [
            'budget_id' => $this->budget->id,
            'category_id' => $this->budget->category_id,
            'limit' => (float) $this->budget->amount,
            'spent' => (float) $this->spent,
            'period' => $this->budget->period,
            'currency' => $this->budget->currency,
            'message' => 'Rozpočet byl překročen',
        ];
    }
}
