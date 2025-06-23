<?php

namespace App\Http\Controllers;

use App\Models\Game;
use App\Models\GameMove;
use App\Models\GameResult;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class GameMoveController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function store(Request $request, Game $game)
    {
        $request->validate([
            'guessed_word' => 'required|string|size:5|alpha',
        ]);

        $user = Auth::user();

        // Check if user can make a move
        if (!$game->canPlayerMakeMove($user)) {
            return response()->json([
                'success' => false,
                'message' => 'You cannot make a move at this time.',
            ], 403);
        }

        $guessedWord = strtoupper($request->guessed_word);
        $targetWord = strtoupper($game->word->word);

        // Calculate the result for each letter
        $result = $this->calculateWordResult($guessedWord, $targetWord);

        // Get the next attempt number for this player
        $attemptNumber = $game->getPlayerAttempts($user) + 1;

        // Create the move
        $move = GameMove::create([
            'game_id' => $game->id,
            'player_id' => $user->id,
            'guessed_word' => $guessedWord,
            'result' => $result,
            'attempt_number' => $attemptNumber,
        ]);

        // Check if the word was guessed correctly
        $isCorrect = $guessedWord === $targetWord;

        // Check if the game should end
        $shouldEndGame = $this->shouldEndGame($game, $user, $isCorrect, $attemptNumber);

        if ($shouldEndGame) {
            $this->endGame($game, $user, $isCorrect);
        }

        // Prepare response data
        $responseData = [
            'success' => true,
            'move' => $move,
            'result' => $result,
            'is_correct' => $isCorrect,
            'attempt_number' => $attemptNumber,
            'game_ended' => $shouldEndGame,
        ];

        if ($shouldEndGame) {
            $game->refresh();
            $responseData['game_result'] = $game->result;
            $responseData['winner'] = $game->winner?->name;
        } else {
            $responseData['next_player'] = $game->getCurrentPlayerTurn()?->name;
        }

        return response()->json($responseData);
    }

    private function calculateWordResult(string $guessedWord, string $targetWord): array
    {
        $result = [];
        $targetLetters = str_split($targetWord);
        $guessedLetters = str_split($guessedWord);

        // First pass: find exact matches
        $usedTargetPositions = [];
        for ($i = 0; $i < 5; $i++) {
            if ($guessedLetters[$i] === $targetLetters[$i]) {
                $result[$i] = 'correct';
                $usedTargetPositions[] = $i;
            } else {
                $result[$i] = 'incorrect';
            }
        }

        // Second pass: find letters in wrong positions
        for ($i = 0; $i < 5; $i++) {
            if ($result[$i] === 'incorrect') {
                for ($j = 0; $j < 5; $j++) {
                    if (!in_array($j, $usedTargetPositions) &&
                        $guessedLetters[$i] === $targetLetters[$j]) {
                        $result[$i] = 'wrong_position';
                        $usedTargetPositions[] = $j;
                        break;
                    }
                }
            }
        }

        return $result;
    }

    private function shouldEndGame(Game $game, $currentPlayer, bool $isCorrect, int $attemptNumber): bool
    {
        // Game ends if word is guessed correctly
        if ($isCorrect) {
            return true;
        }

        // Game ends if both players have used all their attempts
        $player1Attempts = $game->getPlayerAttempts($game->player1);
        $player2Attempts = $game->getPlayerAttempts($game->player2);

        // Update attempts count for current player
        if ($currentPlayer->id === $game->player1_id) {
            $player1Attempts = $attemptNumber;
        } else {
            $player2Attempts = $attemptNumber;
        }

        return $player1Attempts >= $game->max_attempts && $player2Attempts >= $game->max_attempts;
    }

    private function endGame(Game $game, $currentPlayer, bool $isCorrect): void
    {
        DB::transaction(function () use ($game, $currentPlayer, $isCorrect) {
            $game->refresh();

            $winner = null;
            $result = null;

            if ($isCorrect) {
                // Current player won by guessing the word
                $winner = $currentPlayer;
                $result = $currentPlayer->id === $game->player1_id ? 'player1_wins' : 'player2_wins';
            } else {
                // No one guessed the word, check who used fewer attempts
                $player1Attempts = $game->getPlayerAttempts($game->player1);
                $player2Attempts = $game->getPlayerAttempts($game->player2);

                if ($player1Attempts < $player2Attempts) {
                    $winner = $game->player1;
                    $result = 'player1_wins';
                } elseif ($player2Attempts < $player1Attempts) {
                    $winner = $game->player2;
                    $result = 'player2_wins';
                } else {
                    // It's a draw
                    $result = 'draw';
                }
            }

            // Update game
            $game->update([
                'status' => 'completed',
                'winner_id' => $winner?->id,
                'result' => $result,
                'completed_at' => now(),
            ]);

            // Create game result record
            GameResult::create([
                'game_id' => $game->id,
                'winner_id' => $winner?->id,
                'loser_id' => $winner ? ($winner->id === $game->player1_id ? $game->player2_id : $game->player1_id) : null,
                'result_type' => $result === 'draw' ? 'draw' : ($winner ? 'win' : 'loss'),
                'attempts_used' => $winner ? $game->getPlayerAttempts($winner) : null,
                'winning_word' => $isCorrect ? $game->word->word : null,
                'completed_at' => now(),
            ]);

            // Update user statistics
            $this->updatePlayerStats($game, $result);
        });
    }

    private function updatePlayerStats(Game $game, string $result): void
    {
        $player1 = $game->player1;
        $player2 = $game->player2;

        switch ($result) {
            case 'player1_wins':
                $player1->increment('games_won');
                $player2->increment('games_lost');
                break;
            case 'player2_wins':
                $player1->increment('games_lost');
                $player2->increment('games_won');
                break;
            case 'draw':
                $player1->increment('games_drawn');
                $player2->increment('games_drawn');
                break;
        }
    }

    public function getMoves(Game $game)
    {
        $user = Auth::user();

        if (!$game->isPlayerInGame($user)) {
            abort(403, 'You are not part of this game.');
        }

        $moves = $game->moves()
            ->with('player:id,name,username')
            ->orderBy('created_at', 'asc')
            ->get();

        return response()->json([
            'success' => true,
            'moves' => $moves,
            'current_player' => $game->getCurrentPlayerTurn(),
            'game_status' => $game->status,
        ]);
    }
}
