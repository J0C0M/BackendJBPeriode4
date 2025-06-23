<?php

// GameMove Model
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GameMove extends Model
{
    use HasFactory;

    protected $fillable = [
        'game_id',
        'player_id',
        'guessed_word',
        'result',
        'attempt_number',
    ];

    protected function casts(): array
    {
        return [
            'result' => 'array',
        ];
    }

    public function game(): BelongsTo
    {
        return $this->belongsTo(Game::class);
    }

    public function player(): BelongsTo
    {
        return $this->belongsTo(User::class, 'player_id');
    }
}
