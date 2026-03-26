<?php

namespace App\Policies;

use App\Models\Transaction;
use App\Models\User;
use App\Models\Team;
use Illuminate\Support\Facades\DB;

class TransactionPolicy
{
    public function view(User $user, Transaction $transaction): bool
    {
        return $transaction->team_id === (int) $user->current_team_id;
    }

    public function create(User $user): bool
    {
        return $user->canEdit((int) $user->current_team_id);
    }

    public function update(User $user, Transaction $transaction): bool
    {
        return $transaction->team_id === (int) $user->current_team_id && $user->canEdit($transaction->team_id);
    }

    public function delete(User $user, Transaction $transaction): bool
    {
        return $transaction->team_id === (int) $user->current_team_id && $user->canEdit($transaction->team_id);
    }
}
