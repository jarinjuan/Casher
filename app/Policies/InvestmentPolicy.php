<?php

namespace App\Policies;

use App\Models\Investment;
use App\Models\User;
use App\Models\Team;
use Illuminate\Support\Facades\DB;

class InvestmentPolicy
{
    public function view(User $user, Investment $investment): bool
    {
        return $investment->team_id === $user->current_team_id;
    }

    public function update(User $user, Investment $investment): bool
    {
        if ($investment->team_id !== $user->current_team_id) {
            return false;
        }

        $team = Team::find($investment->team_id, '*');
        if ($team && $team->user_id === $user->id) {
            return true;
        }

        return DB::table('team_user')
            ->where('team_id', $investment->team_id)
            ->where('user_id', $user->id)
            ->where('role', 'editor')
            ->exists();
    }

    public function delete(User $user, Investment $investment): bool
    {
        if ($investment->team_id !== $user->current_team_id) {
            return false;
        }

        $team = Team::find($investment->team_id, '*');
        if ($team && $team->user_id === $user->id) {
            return true;
        }

        return DB::table('team_user')
            ->where('team_id', $investment->team_id)
            ->where('user_id', $user->id)
            ->where('role', 'editor')
            ->exists();
    }
}
