<?php

namespace App\Policies;

use App\Models\Category;
use App\Models\Team;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class CategoryPolicy
{
    public function view(User $user, Category $category): bool
    {
        return $user->teams->contains('id', $category->team_id);
    }

    public function update(User $user, Category $category): bool
    {
        if (!$user->teams->contains('id', $category->team_id)) {
            return false;
        }

        $team = Team::find($category->team_id);
        if ($team && $team->user_id === $user->id) {
            return true;
        }

        return DB::table('team_user')
            ->where('team_id', $category->team_id)
            ->where('user_id', $user->id)
            ->where('role', 'editor')
            ->exists();
    }

    public function delete(User $user, Category $category): bool
    {
        if (!$user->teams->contains('id', $category->team_id)) {
            return false;
        }

        $team = Team::find($category->team_id);
        if ($team && $team->user_id === $user->id) {
            return true;
        }

        return DB::table('team_user')
            ->where('team_id', $category->team_id)
            ->where('user_id', $user->id)
            ->where('role', 'editor')
            ->exists();
    }
}
