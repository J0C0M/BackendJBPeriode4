<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('games', function (Blueprint $table) {
            $table->id();
            $table->foreignId('player1_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('player2_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('word_id')->constrained('words')->onDelete('cascade');
            $table->enum('status', ['pending', 'in_progress', 'completed', 'cancelled'])->default('pending');
            $table->enum('game_type', ['random', 'friend'])->default('random');
            $table->foreignId('winner_id')->nullable()->constrained('users')->onDelete('set null');
            $table->enum('result', ['player1_wins', 'player2_wins', 'draw'])->nullable();
            $table->integer('max_attempts')->default(6);
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->json('game_state')->nullable(); // Store current game state
            $table->timestamps();

            $table->index(['player1_id', 'player2_id']);
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('games');
    }
};
