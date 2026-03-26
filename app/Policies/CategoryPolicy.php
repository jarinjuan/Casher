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
        return $category->team_id === (int) $user->current_team_id;
    }

    public function create(User $user): bool
    {
        return $user->canEdit((int) $user->current_team_id);
    }

    public function update(User $user, Category $category): bool
    {
        return $category->team_id === (int) $user->current_team_id && $user->canEdit($category->team_id);
    }

    public function delete(User $user, Category $category): bool
    {
        return $category->team_id === (int) $user->current_team_id && $user->canEdit($category->team_id);
    }
}
