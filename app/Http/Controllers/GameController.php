<?php

namespace App\Http\Controllers;

use App\Models\Game;
use App\Models\User;
use App\Models\Word;
use App\Models\GameResult;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class GameController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $user = Auth::user();

        $games = Game::where(function($query) use ($user) {
            $query->where('player1_id', $user->id)
                ->orWhere('player2_id', $user->id);
        })
            ->with(['player1', 'player2', 'winner', 'word'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('games.index', compact('games'));
    }

    public function create()
    {
        $user = Auth::user();
        $friends = $user->friends()->get();

        return view('games.create', compact('friends'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'game_type' => 'required|in:random,friend',
            'opponent_id' => 'required_if:game_type,friend|exists:users,id',
            'max_attempts' => 'nullable|integer|min:3|max:10',
        ]);

        $user = Auth::user();
        $maxAttempts = $request->max_attempts ?? 6;

        // Get random word
        $word = Word::getRandomWord();
        if (!$word) {
            return redirect()->back()->with('error', 'No words available for the game.');
        }

        if ($request->game_type === 'random') {
            $opponent = $this->findRandomOpponent($user);
            if (!$opponent) {
                return redirect()->back()->with('error', 'No available opponents found.');
            }
        } else {
            $opponent = User::findOrFail($request->opponent_id);

            // Check if they are friends
            if (!$user->isFriendWith($opponent)) {
                return redirect()->back()->with('error', 'You can only play with friends.');
            }
        }

        $game = Game::create([
            'player1_id' => $user->id,
            'player2_id' => $opponent->id,
            'word_id' => $word->id,
            'status' => 'pending',
            'game_type' => $request->game_type,
            'max_attempts' => $maxAttempts,
        ]);

        return redirect()->route('games.show', $game)
            ->with('success', 'Game created! Waiting for opponent to accept.');
    }

    public function show(Game $game)
    {
        $user = Auth::user();

        if (!$game->isPlayerInGame($user)) {
            abort(403, 'You are not part of this game.');
        }

        $game->load(['player1', 'player2', 'word', 'moves.player']);

        // Get moves for each player
        $player1Moves = $game->moves()->where('player_id', $game->player1_id)->orderBy('attempt_number')->get();
        $player2Moves = $game->moves()->where('player_id', $game->player2_id)->orderBy('attempt_number')->get();

        $currentPlayer = $game->getCurrentPlayerTurn();
        $opponent = $game->getOpponent($user);

        return view('games.show', compact(
            'game',
            'user',
            'opponent',
            'currentPlayer',
            'player1Moves',
            'player2Moves'
        ));
    }

    public function accept(Game $game)
    {
        $user = Auth::user();

        if ($game->player2_id !== $user->id) {
            abort(403, 'You cannot accept this game.');
        }

        if ($game->status !== 'pending') {
            return redirect()->route('games.show', $game)
                ->with('error', 'This game is no longer pending.');
        }

        $game->update([
            'status' => 'in_progress',
            'started_at' => now(),
        ]);

        return redirect()->route('games.show', $game)
            ->with('success', 'Game started! Good luck!');
    }

    public function decline(Game $game)
    {
        $user = Auth::user();

        if ($game->player2_id !== $user->id) {
            abort(403, 'You cannot decline this game.');
        }

        if ($game->status !== 'pending') {
            return redirect()->route('games.index')
                ->with('error', 'This game is no longer pending.');
        }

        $game->update(['status' => 'cancelled']);

        return redirect()->route('games.index')
            ->with('success', 'Game invitation declined.');
    }

    public function cancel(Game $game)
    {
        $user = Auth::user();

        if ($game->player1_id !== $user->id) {
            abort(403, 'You cannot cancel this game.');
        }

        if (!in_array($game->status, ['pending', 'in_progress'])) {
            return redirect()->route('games.show', $game)
                ->with('error', 'This game cannot be cancelled.');
        }

        $game->update(['status' => 'cancelled']);

        return redirect()->route('games.index')
            ->with('success', 'Game cancelled.');
    }

    private function findRandomOpponent(User $currentUser)
    {
        // Find user with least active games who is not the current user
        return User::where('id', '!=', $currentUser->id)
            ->withCount(['gamesAsPlayer1 as active_games_count' => function($query) {
                $query->where('status', 'in_progress');
            }])
            ->orderBy('active_games_count', 'asc')
            ->orderBy(DB::raw('RANDOM()')) // For SQLite
            ->first();
    }

    public function history()
    {
        $user = Auth::user();

        $completedGames = Game::where(function($query) use ($user) {
            $query->where('player1_id', $user->id)
                ->orWhere('player2_id', $user->id);
        })
            ->where('status', 'completed')
            ->with(['player1', 'player2', 'winner', 'word'])
            ->orderBy('completed_at', 'desc')
            ->paginate(15);

        return view('games.history', compact('completedGames'));
    }

    public function leaderboard()
    {
        $topPlayers = User::select('users.*')
            ->selectRaw('
                users.games_won as wins,
                users.games_lost as losses,
                users.games_drawn as draws,
                (users.games_won + users.games_lost + users.games_drawn) as total_games,
                CASE
                    WHEN (users.games_won + users.games_lost + users.games_drawn) > 0
                    THEN ROUND((users.games_won * 100.0) / (users.games_won + users.games_lost + users.games_drawn), 2)
                    ELSE 0
                END as win_rate
            ')
            ->having('total_games', '>', 0)
            ->orderBy('wins', 'desc')
            ->orderBy('win_rate', 'desc')
            ->limit(50)
            ->get();

        return view('games.leaderboard', compact('topPlayers'));
    }
}
