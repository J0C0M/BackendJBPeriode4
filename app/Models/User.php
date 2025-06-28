<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'username',
        'email',
        'password',
        'avatar',
        'last_login_at',
        'games_won',
        'games_lost',
        'games_drawn',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'last_login_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    // Game relationships
    public function gamesAsPlayer1(): HasMany
    {
        return $this->hasMany(Game::class, 'player1_id');
    }

    public function gamesAsPlayer2(): HasMany
    {
        return $this->hasMany(Game::class, 'player2_id');
    }

    public function games(): HasMany
    {
        return $this->hasMany(Game::class, 'player1_id')
            ->orWhere('player2_id', $this->id);
    }

    public function wonGames(): HasMany
    {
        return $this->hasMany(Game::class, 'winner_id');
    }

    public function gameMoves(): HasMany
    {
        return $this->hasMany(GameMove::class, 'player_id');
    }

    public function gameResults(): HasMany
    {
        return $this->hasMany(GameResult::class, 'winner_id');
    }

    // Friendship relationships
    public function sentFriendRequests(): HasMany
    {
        return $this->hasMany(Friendship::class, 'requester_id');
    }

    public function receivedFriendRequests(): HasMany
    {
        return $this->hasMany(Friendship::class, 'addressee_id');
    }

    /**
     * Get all accepted friends (many-to-many)
     */
    public function friends()
    {
        $friendIds = \App\Models\Friendship::where(function($q) {
            $q->where('requester_id', $this->id)
              ->orWhere('addressee_id', $this->id);
        })->where('status', 'accepted')
          ->get()
          ->map(function($friendship) {
              return $friendship->requester_id == $this->id ? $friendship->addressee_id : $friendship->requester_id;
          });
        
        return self::whereIn('id', $friendIds);
    }

    /**
     * Check if the user is friends with another user
     */
    public function isFriendWith(User $user): bool
    {
        return $this->friends()->where('id', $user->id)->exists();
    }

    // Settings and comments
    public function settings(): HasOne
    {
        return $this->hasOne(UserSetting::class);
    }

    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class);
    }

    // Helper methods
    public function getTotalGamesAttribute(): int
    {
        return $this->games_won + $this->games_lost + $this->games_drawn;
    }

    public function getWinRateAttribute(): float
    {
        $total = $this->getTotalGamesAttribute();
        return $total > 0 ? ($this->games_won / $total) * 100 : 0;
    }

    public function hasPendingFriendRequestFrom(User $user): bool
    {
        return $this->receivedFriendRequests()
            ->where('requester_id', $user->id)
            ->where('status', 'pending')
            ->exists();
    }

    /**
     * Check if the user has sent a friend request to another user
     */
    public function hasSentFriendRequestTo(User $user): bool
    {
        return \App\Models\Friendship::where('requester_id', $this->id)
            ->where('addressee_id', $user->id)
            ->where('status', 'pending')
            ->exists();
    }

    public function hasReceivedFriendRequestFrom(User $user): bool
    {
        return $this->hasPendingFriendRequestFrom($user);
    }

    /**
     * Get the user's completed game history
     */
    public function getGameHistory()
    {
        return \App\Models\Game::where(function($query) {
            $query->where('player1_id', $this->id)
                ->orWhere('player2_id', $this->id);
        })->where('status', 'completed')
          ->orderBy('completed_at', 'desc');
    }
}
