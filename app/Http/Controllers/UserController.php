<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Game;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    /**
     * Display a listing of users (search/browse)
     */
    public function index(Request $request)
    {
        $query = User::query();

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('username', 'LIKE', "%{$search}%")
                    ->orWhere('name', 'LIKE', "%{$search}%");
            });
        }

        // Filter by game statistics
        if ($request->filled('sort')) {
            switch ($request->input('sort')) {
                case 'wins':
                    $query->orderBy('games_won', 'desc');
                    break;
                case 'games':
                    $query->orderByRaw('(games_won + games_lost + games_drawn) DESC');
                    break;
                case 'winrate':
                    $query->orderByRaw('CASE WHEN (games_won + games_lost + games_drawn) > 0 THEN (games_won * 100.0 / (games_won + games_lost + games_drawn)) ELSE 0 END DESC');
                    break;
                default:
                    $query->orderBy('created_at', 'desc');
                    break;
            }
        } else {
            $query->orderBy('created_at', 'desc');
        }

        $users = $query->paginate(20);

        return view('users.index', compact('users'));
    }

    /**
     * Display the specified user's profile
     */
    public function show(User $user)
    {
        $currentUser = Auth::user();

        // Check privacy settings
        if ($user->settings && $user->settings->privacy_level === 'private' &&
            $currentUser->id !== $user->id && !$currentUser->isFriendWith($user)) {
            abort(403, 'This profile is private.');
        }

        if ($user->settings && $user->settings->privacy_level === 'friends_only' &&
            $currentUser->id !== $user->id && !$currentUser->isFriendWith($user)) {
            abort(403, 'This profile is only visible to friends.');
        }

        // Get user's game statistics
        $totalGames = $user->getTotalGamesAttribute();
        $winRate = $user->getWinRateAttribute();

        // Get recent games
        $recentGames = Game::where(function ($query) use ($user) {
            $query->where('player1_id', $user->id)
                ->orWhere('player2_id', $user->id);
        })
            ->where('status', 'completed')
            ->with(['player1', 'player2', 'winner', 'word'])
            ->orderBy('completed_at', 'desc')
            ->limit(10)
            ->get();

        // Get friendship status if viewing another user's profile
        $friendshipStatus = null;
        if ($currentUser && $currentUser->id !== $user->id) {
            if ($currentUser->isFriendWith($user)) {
                $friendshipStatus = 'friends';
            } elseif ($currentUser->hasSentFriendRequestTo($user)) {
                $friendshipStatus = 'pending_sent';
            } elseif ($currentUser->hasPendingFriendRequestFrom($user)) {
                $friendshipStatus = 'pending_received';
            } else {
                $friendshipStatus = 'none';
            }
        }

        // Get comments on this user's profile
        $comments = $user->comments()
            ->with('user')
            ->where('is_approved', true)
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('users.show', compact(
            'user',
            'totalGames',
            'winRate',
            'recentGames',
            'friendshipStatus',
            'comments'
        ));
    }

    /**
     * Show the form for editing the user's profile
     */
    public function edit()
    {
        $user = Auth::user();
        return view('users.edit', compact('user'));
    }

    /**
     * Update the user's profile
     */
    public function update(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'name' => 'required|string|max:255',
            'username' => [
                'required',
                'string',
                'max:255',
                'regex:/^[a-zA-Z0-9_]+$/',
                Rule::unique('users')->ignore($user->id)
            ],
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users')->ignore($user->id)
            ],
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'current_password' => 'nullable|required_with:password|current_password',
            'password' => 'nullable|string|min:8|confirmed',
        ]);

        // Handle avatar upload
        if ($request->hasFile('avatar')) {
            // Delete old avatar if it exists
            if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
                Storage::disk('public')->delete($user->avatar);
            }

            $avatarPath = $request->file('avatar')->store('avatars', 'public');
            $user->avatar = $avatarPath;
        }

        // Update basic profile information
        $user->name = $request->input('name');
        $user->username = $request->input('username');
        $user->email = $request->input('email');

        // Update password if provided
        if ($request->filled('password')) {
            $user->password = Hash::make($request->input('password'));
        }

        $user->save();

        return redirect()->route('users.show', $user)
            ->with('success', 'Profile updated successfully.');
    }

    /**
     * Get user statistics for dashboard/profile
     */
    public function getStatistics(User $user = null)
    {
        if (!$user) {
            $user = Auth::user();
        }

        // Check if user can view these statistics
        if ($user->settings && !$user->settings->show_statistics &&
            Auth::id() !== $user->id) {
            return response()->json(['error' => 'Statistics are private'], 403);
        }

        $stats = [
            'total_games' => $user->getTotalGamesAttribute(),
            'games_won' => $user->games_won,
            'games_lost' => $user->games_lost,
            'games_drawn' => $user->games_drawn,
            'win_rate' => $user->getWinRateAttribute(),
        ];

        // Get additional statistics
        $stats['average_attempts'] = $this->getAverageAttempts($user);
        $stats['best_streak'] = $this->getBestWinStreak($user);
        $stats['current_streak'] = $this->getCurrentWinStreak($user);
        $stats['favorite_difficulty'] = $this->getFavoriteDifficulty($user);

        return response()->json($stats);
    }

    /**
     * Get user's game history
     */
    public function gameHistory(User $user = null, Request $request)
    {
        if (!$user) {
            $user = Auth::user();
        }

        // Check privacy
        if (Auth::id() !== $user->id &&
            $user->settings && $user->settings->privacy_level === 'private') {
            abort(403, 'Game history is private.');
        }

        $query = Game::where(function ($q) use ($user) {
            $q->where('player1_id', $user->id)
                ->orWhere('player2_id', $user->id);
        });

        // Filter by result
        if ($request->filled('result')) {
            switch ($request->input('result')) {
                case 'won':
                    $query->where('winner_id', $user->id);
                    break;
                case 'lost':
                    $query->where('winner_id', '!=', $user->id)
                        ->whereNotNull('winner_id');
                    break;
                case 'draw':
                    $query->whereNull('winner_id')
                        ->where('status', 'completed');
                    break;
            }
        }

        // Filter by opponent type
        if ($request->filled('opponent_type')) {
            $query->where('game_type', $request->input('opponent_type'));
        }

        $games = $query->with(['player1', 'player2', 'winner', 'word'])
            ->orderBy('completed_at', 'desc')
            ->paginate(20);

        return view('users.game-history', compact('user', 'games'));
    }

    /**
     * Search users (AJAX endpoint)
     */
    public function search(Request $request)
    {
        $request->validate([
            'query' => 'required|string|min:2'
        ]);

        $query = $request->input('query');
        $currentUser = Auth::user();

        $users = User::where('id', '!=', $currentUser->id)
            ->where(function ($q) use ($query) {
                $q->where('username', 'LIKE', "%{$query}%")
                    ->orWhere('name', 'LIKE', "%{$query}%");
            })
            ->select('id', 'username', 'name', 'avatar', 'games_won', 'games_lost', 'games_drawn')
            ->limit(10)
            ->get();

        return response()->json($users);
    }

    /**
     * Get user's rank/position in leaderboard
     */
    public function getRank(User $user = null)
    {
        if (!$user) {
            $user = Auth::user();
        }

        // Rank by total wins
        $winsRank = User::where('games_won', '>', $user->games_won)->count() + 1;

        // Rank by win rate (for users with at least 5 games)
        $totalGames = $user->getTotalGamesAttribute();
        $winRateRank = null;

        if ($totalGames >= 5) {
            $winRate = $user->getWinRateAttribute();
            $winRateRank = User::whereRaw('(games_won + games_lost + games_drawn) >= 5')
                    ->whereRaw('(games_won * 100.0 / (games_won + games_lost + games_drawn)) > ?', [$winRate])
                    ->count() + 1;
        }

        return response()->json([
            'wins_rank' => $winsRank,
            'win_rate_rank' => $winRateRank,
            'total_games' => $totalGames,
        ]);
    }

    /**
     * Helper method to get average attempts for won games
     */
    private function getAverageAttempts(User $user)
    {
        $wonGames = Game::where('winner_id', $user->id)
            ->with('moves')
            ->get();

        if ($wonGames->isEmpty()) {
            return 0;
        }

        $totalAttempts = $wonGames->sum(function ($game) use ($user) {
            return $game->moves()->where('player_id', $user->id)->count();
        });

        return round($totalAttempts / $wonGames->count(), 2);
    }

    /**
     * Helper method to get best win streak
     */
    private function getBestWinStreak(User $user)
    {
        $games = Game::where(function ($q) use ($user) {
            $q->where('player1_id', $user->id)
                ->orWhere('player2_id', $user->id);
        })
            ->where('status', 'completed')
            ->orderBy('completed_at', 'asc')
            ->get();

        $bestStreak = 0;
        $currentStreak = 0;

        foreach ($games as $game) {
            if ($game->winner_id === $user->id) {
                $currentStreak++;
                $bestStreak = max($bestStreak, $currentStreak);
            } else {
                $currentStreak = 0;
            }
        }

        return $bestStreak;
    }

    /**
     * Helper method to get current win streak
     */
    private function getCurrentWinStreak(User $user)
    {
        $recentGames = Game::where(function ($q) use ($user) {
            $q->where('player1_id', $user->id)
                ->orWhere('player2_id', $user->id);
        })
            ->where('status', 'completed')
            ->orderBy('completed_at', 'desc')
            ->get();

        $currentStreak = 0;

        foreach ($recentGames as $game) {
            if ($game->winner_id === $user->id) {
                $currentStreak++;
            } else {
                break;
            }
        }

        return $currentStreak;
    }

    /**
     * Helper method to get user's favorite difficulty
     */
    private function getFavoriteDifficulty(User $user)
    {
        $difficulty = Game::where(function ($q) use ($user) {
            $q->where('player1_id', $user->id)
                ->orWhere('player2_id', $user->id);
        })
            ->join('words', 'games.word_id', '=', 'words.id')
            ->selectRaw('words.difficulty, COUNT(*) as count')
            ->groupBy('words.difficulty')
            ->orderBy('count', 'desc')
            ->first();

        return $difficulty ? $difficulty->difficulty : null;
    }
}
