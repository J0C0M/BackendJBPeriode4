<?php

// UserSetting Model
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'email_notifications',
        'friend_requests_notifications',
        'game_invitations_notifications',
        'privacy_level',
        'show_statistics',
        'theme',
    ];

    protected function casts(): array
    {
        return [
            'email_notifications' => 'boolean',
            'friend_requests_notifications' => 'boolean',
            'game_invitations_notifications' => 'boolean',
            'show_statistics' => 'boolean',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
