<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Game extends Model
{
    use HasFactory;

    protected $fillable = [
        'player1_id',
        'player2_id',
        'word_id',
        'status',
        'game_type',
        'winner_id',
        'result',
        'max_attempts',
        'started_at',
        'completed_at',
        'game_state',
    ];

    protected function casts(): array
    {
        return [
            'started_at' => 'datetime',
            'completed_at' => 'datetime',
            'game_state' => 'array',
        ];
    }

    // Relationships
    public function player1(): BelongsTo
    {
        return $this->belongsTo(User::class, 'player1_id');
    }

    public function player2(): BelongsTo
    {
        return $this->belongsTo(User::class, 'player2_id');
    }

    public function winner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'winner_id');
    }

    public function word(): BelongsTo
    {
        return $this->belongsTo(Word::class);
    }

    public function moves(): HasMany
    {
        return $this->hasMany(GameMove::class);
    }

    public function result(): HasOne
    {
        return $this->hasOne(GameResult::class);
    }

    public function comments()
    {
        return $this->morphMany(Comment::class, 'commentable');
    }

    // Helper methods
    public function getOpponent(User $user): ?User
    {
        if ($this->player1_id === $user->id) {
            return $this->player2;
        } elseif ($this->player2_id === $user->id) {
            return $this->player1;
        }
        return null;
    }

    public function isPlayerInGame(User $user): bool
    {
        return $this->player1_id === $user->id || $this->player2_id === $user->id;
    }

    public function getCurrentPlayerTurn(): ?User
    {
        if ($this->status !== 'in_progress') {
            return null;
        }

        $player1Moves = $this->moves()->where('player_id', $this->player1_id)->count();
        $player2Moves = $this->moves()->where('player_id', $this->player2_id)->count();

        // Player 1 goes first, then alternating
        return $player1Moves <= $player2Moves ? $this->player1 : $this->player2;
    }

    public function getPlayerAttempts(User $user): int
    {
        return $this->moves()->where('player_id', $user->id)->count();
    }

    public function isGameOver(): bool
    {
        return in_array($this->status, ['completed', 'cancelled']);
    }

    public function canPlayerMakeMove(User $user): bool
    {
        if (!$this->isPlayerInGame($user)) {
            return false;
        }

        if ($this->status !== 'in_progress') {
            return false;
        }

        $currentPlayer = $this->getCurrentPlayerTurn();
        if (!$currentPlayer || $currentPlayer->id !== $user->id) {
            return false;
        }

        return $this->getPlayerAttempts($user) < $this->max_attempts;
    }

    public function hasPlayerWon(User $user): bool
    {
        return $this->moves()
            ->where('player_id', $user->id)
            ->where('guessed_word', $this->word->word)
            ->exists();
    }

    public function getDurationAttribute(): ?int
    {
        if (!$this->started_at || !$this->completed_at) {
            return null;
        }

        return $this->completed_at->diffInSeconds($this->started_at);
    }
}
