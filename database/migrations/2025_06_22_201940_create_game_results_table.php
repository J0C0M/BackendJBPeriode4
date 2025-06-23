<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('game_results', function (Blueprint $table) {
            $table->id();
            $table->foreignId('game_id')->constrained('games')->onDelete('cascade');
            $table->foreignId('winner_id')->nullable()->constrained('users')->onDelete('cascade');
            $table->foreignId('loser_id')->nullable()->constrained('users')->onDelete('cascade');
            $table->enum('result_type', ['win', 'loss', 'draw']);
            $table->integer('attempts_used')->nullable();
            $table->string('winning_word', 5)->nullable();
            $table->timestamp('completed_at');
            $table->timestamps();

            $table->index(['winner_id', 'completed_at']);
            $table->index(['loser_id', 'completed_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('game_results');
    }
};
