<?php

// Word Model
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Word extends Model
{
    use HasFactory;

    protected $fillable = [
        'word',
        'difficulty',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function games(): HasMany
    {
        return $this->hasMany(Game::class);
    }

    public static function getRandomWord(string $difficulty = null): ?Word
    {
        $query = static::where('is_active', true);

        if ($difficulty) {
            $query->where('difficulty', $difficulty);
        }

        return $query->inRandomOrder()->first();
    }
}

