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
        return $investment->team_id === (int) $user->current_team_id;
    }

    public function create(User $user): bool
    {
        return $user->canEdit((int) $user->current_team_id);
    }

    public function update(User $user, Investment $investment): bool
    {
        return $investment->team_id === (int) $user->current_team_id && $user->canEdit($investment->team_id);
    }

    public function delete(User $user, Investment $investment): bool
    {
        return $investment->team_id === (int) $user->current_team_id && $user->canEdit($investment->team_id);
    }
}
