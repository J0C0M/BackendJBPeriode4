<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Game;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class HomeController extends Controller
{
    public function index()
    {
        $leaderboards = $this->getLeaderboards();
        $gameStats = $this->getGameStats();

        return view('home', compact('leaderboards', 'gameStats'));
    }

    public function dashboard()
    {
        $user = auth()->user();

        // Get user's recent games
        $recentGames = Game::where(function($query) use ($user) {
            $query->where('player1_id', $user->id)
                ->orWhere('player2_id', $user->id);
        })
            ->with(['player1', 'player2', 'winner', 'word'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // Get pending game invitations
        $pendingInvitations = Game::where('player2_id', $user->id)
            ->where('status', 'pending')
            ->with(['player1', 'word'])
            ->orderBy('created_at', 'desc')
            ->get();

        // Get active games
        $activeGames = Game::where(function($query) use ($user) {
            $query->where('player1_id', $user->id)
                ->orWhere('player2_id', $user->id);
        })
            ->where('status', 'in_progress')
            ->with(['player1', 'player2', 'word'])
            ->orderBy('updated_at', 'desc')
            ->get();

        // Get user statistics
        $userStats = [
            'total_games' => $user->getTotalGamesAttribute(),
            'win_rate' => $user->getWinRateAttribute(),
            'games_won' => $user->games_won,
            'games_lost' => $user->games_lost,
            'games_drawn' => $user->games_drawn,
        ];

        return view('dashboard', compact(
            'user',
            'recentGames',
            'pendingInvitations',
            'activeGames',
            'userStats'
        ));
    }

    private function getLeaderboards()
    {
        $today = Carbon::today();
        $weekStart = Carbon::now()->startOfWeek();

        return [
            'daily' => $this->getDailyLeaderboard($today),
            'weekly' => $this->getWeeklyLeaderboard($weekStart),
            'all_time' => $this->getAllTimeLeaderboard(),
        ];
    }

    private function getDailyLeaderboard($date)
    {
        return User::select('users.id', 'users.name', 'users.username', 'users.avatar')
            ->selectRaw('COUNT(games.id) as daily_wins')
            ->join('games', 'users.id', '=', 'games.winner_id')
            ->where('games.status', 'completed')
            ->whereDate('games.completed_at', $date)
            ->groupBy('users.id', 'users.name', 'users.username', 'users.avatar')
            ->orderBy('daily_wins', 'desc')
            ->limit(10)
            ->get();
    }

    private function getWeeklyLeaderboard($weekStart)
    {
        return User::select('users.id', 'users.name', 'users.username', 'users.avatar')
            ->selectRaw('COUNT(games.id) as weekly_wins')
            ->join('games', 'users.id', '=', 'games.winner_id')
            ->where('games.status', 'completed')
            ->where('games.completed_at', '>=', $weekStart)
            ->groupBy('users.id', 'users.name', 'users.username', 'users.avatar')
            ->orderBy('weekly_wins', 'desc')
            ->limit(10)
            ->get();
    }

    private function getAllTimeLeaderboard()
    {
        return User::select('id', 'name', 'username', 'avatar', 'games_won')
            ->where('games_won', '>', 0)
            ->orderBy('games_won', 'desc')
            ->limit(10)
            ->get();
    }

    private function getGameStats()
    {
        return [
            'total_games' => Game::where('status', 'completed')->count(),
            'total_players' => User::count(),
            'games_today' => Game::where('status', 'completed')
                ->whereDate('completed_at', Carbon::today())
                ->count(),
            'active_games' => Game::where('status', 'in_progress')->count(),
        ];
    }
}
