<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_settings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->boolean('email_notifications')->default(true);
            $table->boolean('friend_requests_notifications')->default(true);
            $table->boolean('game_invitations_notifications')->default(true);
            $table->enum('privacy_level', ['public', 'friends_only', 'private'])->default('public');
            $table->boolean('show_statistics')->default(true);
            $table->enum('theme', ['light', 'dark'])->default('light');
            $table->timestamps();

            $table->unique('user_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_settings');
    }
};
