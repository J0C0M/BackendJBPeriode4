<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Game;
use App\Models\GameMove;
use App\Models\GameResult;
use App\Models\User;
use App\Models\Word;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class GameApiController extends Controller
{
    /**
     * Get current game state for a specific game
     */
    public function getGameState(Game $game): JsonResponse
    {
        if (!$game->isPlayerInGame(Auth::user())) {
            return response()->json(['error' => 'You are not a player in this game'], 403);
        }

        $moves = $game->moves()->with('player')->orderBy('created_at')->get();
        $currentPlayer = $game->getCurrentPlayerTurn();

        return response()->json([
            'game' => [
                'id' => $game->id,
                'status' => $game->status,
                'game_type' => $game->game_type,
                'max_attempts' => $game->max_attempts,
                'result' => $game->result,
                'started_at' => $game->started_at,
                'completed_at' => $game->completed_at,
                'word_length' => $game->word ? strlen($game->word->word) : 5,
                'winner_id' => $game->winner_id,
                'player1' => [
                    'id' => $game->player1->id,
                    'name' => $game->player1->name,
                    'username' => $game->player1->username,
                    'avatar' => $game->player1->avatar,
                    'attempts' => $game->getPlayerAttempts($game->player1)
                ],
                'player2' => [
                    'id' => $game->player2->id,
                    'name' => $game->player2->name,
                    'username' => $game->player2->username,
                    'avatar' => $game->player2->avatar,
                    'attempts' => $game->getPlayerAttempts($game->player2)
                ],
                'current_player_id' => $currentPlayer ? $currentPlayer->id : null,
                'can_current_user_move' => $game->canPlayerMakeMove(Auth::user())
            ],
            'moves' => $moves->map(function ($move) {
                return [
                    'id' => $move->id,
                    'player_id' => $move->player_id,
                    'player_name' => $move->player->name,
                    'guessed_word' => $move->guessed_word,
                    'result' => $move->result,
                    'attempt_number' => $move->attempt_number,
                    'created_at' => $move->created_at->toISOString()
                ];
            })
        ]);
    }

    /**
     * Make a move in the game
     */
    public function makeMove(Request $request, Game $game): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'guessed_word' => 'required|string|size:5|alpha'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => 'Invalid guess',
                'details' => $validator->errors()
            ], 422);
        }

        $user = Auth::user();
        $guessedWord = strtoupper($request->guessed_word);

        // Check if user can make a move
        if (!$game->canPlayerMakeMove($user)) {
            return response()->json(['error' => 'You cannot make a move in this game'], 403);
        }

        // Validate the guessed word exists in our dictionary
        $validWord = Word::where('word', $guessedWord)->where('is_active', true)->first();
        if (!$validWord) {
            return response()->json(['error' => 'Invalid word. Please enter a valid 5-letter word.'], 422);
        }

        DB::beginTransaction();

        try {
            $targetWord = $game->word->word;
            $attemptNumber = $game->getPlayerAttempts($user) + 1;

            // Calculate the result for each letter
            $result = $this->calculateWordResult($guessedWord, $targetWord);

            // Create the game move
            $gameMove = GameMove::create([
                'game_id' => $game->id,
                'player_id' => $user->id,
                'guessed_word' => $guessedWord,
                'result' => $result,
                'attempt_number' => $attemptNumber
            ]);

            // Check if the word was guessed correctly
            $isCorrectGuess = $guessedWord === $targetWord;
            $playerWon = $isCorrectGuess;

            // Check if game should end
            $gameEnded = false;
            $gameResult = null;

            if ($playerWon) {
                // Player won
                $game->update([
                    'status' => 'completed',
                    'winner_id' => $user->id,
                    'result' => $user->id === $game->player1_id ? 'player1_wins' : 'player2_wins',
                    'completed_at' => now()
                ]);

                // Update user statistics
                $user->increment('games_won');
                $opponent = $game->getOpponent($user);
                $opponent->increment('games_lost');

                // Create game result record
                $gameResult = GameResult::create([
                    'game_id' => $game->id,
                    'winner_id' => $user->id,
                    'loser_id' => $opponent->id,
                    'result_type' => 'win',
                    'attempts_used' => $attemptNumber,
                    'winning_word' => $guessedWord,
                    'completed_at' => now()
                ]);

                $gameEnded = true;
            } elseif ($attemptNumber >= $game->max_attempts) {
                // Check if both players have used all attempts
                $opponent = $game->getOpponent($user);
                $opponentAttempts = $game->getPlayerAttempts($opponent);

                if ($opponentAttempts >= $game->max_attempts) {
                    // Both players used all attempts - it's a draw
                    $game->update([
                        'status' => 'completed',
                        'result' => 'draw',
                        'completed_at' => now()
                    ]);

                    // Update user statistics
                    $user->increment('games_drawn');
                    $opponent->increment('games_drawn');

                    // Create game result records
                    GameResult::create([
                        'game_id' => $game->id,
                        'result_type' => 'draw',
                        'attempts_used' => $attemptNumber,
                        'completed_at' => now()
                    ]);

                    $gameEnded = true;
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'move' => [
                    'id' => $gameMove->id,
                    'guessed_word' => $guessedWord,
                    'result' => $result,
                    'attempt_number' => $attemptNumber,
                    'is_correct' => $isCorrectGuess
                ],
                'game_ended' => $gameEnded,
                'game_result' => $gameResult ? [
                    'result_type' => $gameResult->result_type,
                    'winner_id' => $gameResult->winner_id,
                    'attempts_used' => $gameResult->attempts_used
                ] : null,
                'target_word' => $gameEnded ? $targetWord : null,
                'next_player_id' => $gameEnded ? null : $game->fresh()->getCurrentPlayerTurn()?->id
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['error' => 'Failed to process move'], 500);
        }
    }

    /**
     * Get user's active games
     */
    public function getUserGames(): JsonResponse
    {
        $user = Auth::user();

        $games = Game::where(function ($query) use ($user) {
            $query->where('player1_id', $user->id)
                ->orWhere('player2_id', $user->id);
        })
            ->with(['player1', 'player2', 'winner'])
            ->orderBy('updated_at', 'desc')
            ->take(10)
            ->get();

        return response()->json([
            'games' => $games->map(function ($game) use ($user) {
                $opponent = $game->getOpponent($user);
                return [
                    'id' => $game->id,
                    'status' => $game->status,
                    'game_type' => $game->game_type,
                    'opponent' => [
                        'id' => $opponent->id,
                        'name' => $opponent->name,
                        'username' => $opponent->username,
                        'avatar' => $opponent->avatar
                    ],
                    'result' => $game->result,
                    'winner_id' => $game->winner_id,
                    'is_my_turn' => $game->getCurrentPlayerTurn()?->id === $user->id,
                    'my_attempts' => $game->getPlayerAttempts($user),
                    'opponent_attempts' => $game->getPlayerAttempts($opponent),
                    'max_attempts' => $game->max_attempts,
                    'started_at' => $game->started_at,
                    'updated_at' => $game->updated_at
                ];
            })
        ]);
    }

    /**
     * Accept a game invitation
     */
    public function acceptGame(Game $game): JsonResponse
    {
        $user = Auth::user();

        if (!$game->isPlayerInGame($user)) {
            return response()->json(['error' => 'You are not a player in this game'], 403);
        }

        if ($game->status !== 'pending') {
            return response()->json(['error' => 'Game is not in pending status'], 422);
        }

        $game->update([
            'status' => 'in_progress',
            'started_at' => now()
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Game started successfully',
            'game' => [
                'id' => $game->id,
                'status' => $game->status,
                'started_at' => $game->started_at
            ]
        ]);
    }

    /**
     * Decline/Cancel a game invitation
     */
    public function declineGame(Game $game): JsonResponse
    {
        $user = Auth::user();

        if (!$game->isPlayerInGame($user)) {
            return response()->json(['error' => 'You are not a player in this game'], 403);
        }

        if ($game->status !== 'pending') {
            return response()->json(['error' => 'Game is not in pending status'], 422);
        }

        $game->update([
            'status' => 'cancelled',
            'completed_at' => now()
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Game cancelled successfully'
        ]);
    }

    /**
     * Get game history for the current user
     */
    public function getGameHistory(Request $request): JsonResponse
    {
        $user = Auth::user();
        $page = $request->get('page', 1);
        $perPage = 10;

        $games = Game::where(function ($query) use ($user) {
            $query->where('player1_id', $user->id)
                ->orWhere('player2_id', $user->id);
        })
            ->where('status', 'completed')
            ->with(['player1', 'player2', 'winner', 'word'])
            ->orderBy('completed_at', 'desc')
            ->paginate($perPage, ['*'], 'page', $page);

        return response()->json([
            'games' => $games->items(),
            'pagination' => [
                'current_page' => $games->currentPage(),
                'last_page' => $games->lastPage(),
                'per_page' => $games->perPage(),
                'total' => $games->total()
            ]
        ]);
    }

    /**
     * Calculate the result for each letter in the guessed word
     */
    private function calculateWordResult(string $guessedWord, string $targetWord): array
    {
        $result = [];
        $targetLetters = str_split($targetWord);
        $guessedLetters = str_split($guessedWord);
        $usedTargetPositions = [];

        // First pass: mark correct letters (green)
        for ($i = 0; $i < 5; $i++) {
            if ($guessedLetters[$i] === $targetLetters[$i]) {
                $result[$i] = 'correct';
                $usedTargetPositions[$i] = true;
            } else {
                $result[$i] = 'incorrect';
            }
        }

        // Second pass: mark wrong position letters (yellow)
        for ($i = 0; $i < 5; $i++) {
            if ($result[$i] === 'incorrect') {
                for ($j = 0; $j < 5; $j++) {
                    if (!isset($usedTargetPositions[$j]) && $guessedLetters[$i] === $targetLetters[$j]) {
                        $result[$i] = 'wrong_position';
                        $usedTargetPositions[$j] = true;
                        break;
                    }
                }
            }
        }

        return $result;
    }
}
