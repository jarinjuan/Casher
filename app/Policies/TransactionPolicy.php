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
        return $transaction->team_id === $user->current_team_id;
    }

    public function update(User $user, Transaction $transaction): bool
    {
        if ($transaction->team_id !== $user->current_team_id) {
            return false;
        }

        $team = Team::find($transaction->team_id);
        if ($team && $team->user_id === $user->id) {
            return true;
        }

        $teamUser = DB::table('team_user')
            ->where('team_id', $transaction->team_id)
            ->where('user_id', $user->id)
            ->first();

        return $teamUser && $teamUser->role === 'editor';
    }

    public function delete(User $user, Transaction $transaction): bool
    {
        if ($transaction->team_id !== $user->current_team_id) {
            return false;
        }

        $team = Team::find($transaction->team_id);
        if ($team && $team->user_id === $user->id) {
            return true;
        }

        $teamUser = DB::table('team_user')
            ->where('team_id', $transaction->team_id)
            ->where('user_id', $user->id)
            ->first();

        return $teamUser && $teamUser->role === 'editor';
    }
}
