<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'current_team_id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
    
    /**
     * User has many transactions.
     */
    public function transactions()
    {
        return $this->hasMany(\App\Models\Transaction::class);
    }

    public function categories()
    {
        return $this->hasMany(\App\Models\Category::class);
    }

    public function budgets()
    {
        return $this->hasMany(\App\Models\Budget::class);
    }

    public function investments()
    {
        return $this->hasMany(\App\Models\Investment::class);
    }

    public function ownedTeams()
    {
        return $this->hasMany(Team::class);
    }

    public function teams()
    {
        return $this->belongsToMany(Team::class)->withPivot('role');
    }

    public function currentTeam()
    {
        return $this->belongsTo(Team::class, 'current_team_id');
    }

    /**
     * Check if user has a specific role in a specific team.
     */
    public function hasRole(string $role, int $teamId): bool
    {
        if ($this->ownedTeams()->where('id', $teamId)->exists()) {
            return true;
        }

        return $this->teams()
            ->where('team_id', $teamId)
            ->where('role', $role)
            ->exists();
    }

    /**
     * Check if user can edit in a specific team (Owner or Editor).
     */
    public function canEdit(int $teamId): bool
    {
        if ($this->ownedTeams()->where('id', $teamId)->exists()) {
            return true;
        }

        return $this->teams()
            ->where('team_id', $teamId)
            ->where('role', 'editor')
            ->exists();
    }

}
