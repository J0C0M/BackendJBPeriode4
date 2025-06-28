<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Game;
use App\Models\GameResult;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class LeaderboardController extends Controller
{
    /**
     * Display the main leaderboard page
     */
    public function index(Request $request)
    {
        $period = $request->get('period', 'all_time');

        $leaderboardData = [
            'daily' => $this->getDailyLeaderboard(),
            'weekly' => $this->getWeeklyLeaderboard(),
            'all_time' => $this->getAllTimeLeaderboard(),
            'current_period' => $period
        ];

        $stats = [
            'total_players' => User::count(),
            'total_games' => Game::count(),
            'games_today' => Game::whereDate('created_at', now()->toDateString())->count(),
            'active_games' => Game::where('status', 'in_progress')->count(),
        ];

        $leaderboards = [
            'all_time' => $this->getAllTimeLeaderboard(10),
            'weekly' => $this->getWeeklyLeaderboard(10),
            'daily' => $this->getDailyLeaderboard(10),
        ];

        // Get recent activity (recent completed games)
        $recentActivity = Game::where('status', 'completed')
            ->with(['winner', 'player1', 'player2'])
            ->orderBy('completed_at', 'desc')
            ->limit(10)
            ->get()
            ->map(function($game) {
                return (object) [
                    'winner' => $game->winner,
                    'loser' => $game->winner_id === $game->player1_id ? $game->player2 : $game->player1,
                    'attempts' => $game->moves()->where('player_id', $game->winner_id)->count(),
                    'created_at' => $game->completed_at
                ];
            });

        return view('leaderboard.index', compact('stats', 'leaderboards', 'leaderboardData', 'recentActivity'));
    }

    /**
     * Get daily leaderboard (today's winners)
     */
    public function getDailyLeaderboard($limit = 10)
    {
        return $this->getLeaderboardByPeriod(Carbon::today(), Carbon::tomorrow(), $limit);
    }

    /**
     * Get weekly leaderboard (this week's winners)
     */
    public function getWeeklyLeaderboard($limit = 10)
    {
        return $this->getLeaderboardByPeriod(Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek(), $limit);
    }

    /**
     * Get all-time leaderboard
     */
    public function getAllTimeLeaderboard($limit = 10)
    {
        return User::select('users.*')
            ->selectRaw('users.games_won as wins')
            ->selectRaw('users.games_lost as losses')
            ->selectRaw('users.games_drawn as draws')
            ->selectRaw('(users.games_won + users.games_lost + users.games_drawn) as total_games')
            ->selectRaw('CASE
                WHEN (users.games_won + users.games_lost + users.games_drawn) > 0
                THEN ROUND((users.games_won * 100.0) / (users.games_won + users.games_lost + users.games_drawn), 2)
                ELSE 0
            END as win_rate')
            ->where(function($query) {
                $query->where('games_won', '>', 0)
                    ->orWhere('games_lost', '>', 0)
                    ->orWhere('games_drawn', '>', 0);
            })
            ->orderByDesc('games_won')
            ->orderByDesc(DB::raw('CASE
                WHEN (users.games_won + users.games_lost + users.games_drawn) > 0
                THEN (users.games_won * 100.0) / (users.games_won + users.games_lost + users.games_drawn)
                ELSE 0
            END'))
            ->limit($limit)
            ->get();
    }

    /**
     * Get leaderboard for a specific time period
     */
    private function getLeaderboardByPeriod($startDate, $endDate, $limit = 10)
    {
        return User::select('users.*')
            ->selectRaw('COUNT(CASE WHEN games.result = "player1_wins" AND games.player1_id = users.id THEN 1
                                   WHEN games.result = "player2_wins" AND games.player2_id = users.id THEN 1
                                   END) as wins')
            ->selectRaw('COUNT(CASE WHEN games.result = "player1_wins" AND games.player2_id = users.id THEN 1
                                   WHEN games.result = "player2_wins" AND games.player1_id = users.id THEN 1
                                   END) as losses')
            ->selectRaw('COUNT(CASE WHEN games.result = "draw" AND (games.player1_id = users.id OR games.player2_id = users.id) THEN 1 END) as draws')
            ->selectRaw('COUNT(games.id) as total_games')
            ->selectRaw('CASE
                WHEN COUNT(games.id) > 0
                THEN ROUND((COUNT(CASE WHEN games.result = "player1_wins" AND games.player1_id = users.id THEN 1
                                            WHEN games.result = "player2_wins" AND games.player2_id = users.id THEN 1
                                            END) * 100.0) / COUNT(games.id), 2)
                ELSE 0
            END as win_rate')
            ->leftJoin('games', function($join) {
                $join->on(function($query) {
                    $query->where('games.player1_id', '=', DB::raw('users.id'))
                        ->orWhere('games.player2_id', '=', DB::raw('users.id'));
                });
            })
            ->where('games.status', 'completed')
            ->whereBetween('games.completed_at', [$startDate, $endDate])
            ->groupBy('users.id', 'users.name', 'users.username', 'users.email', 'users.avatar', 'users.created_at', 'users.updated_at', 'users.email_verified_at', 'users.last_login_at', 'users.games_won', 'users.games_lost', 'users.games_drawn')
            ->havingRaw('COUNT(games.id) > 0')
            ->orderByDesc('wins')
            ->orderByDesc('win_rate')
            ->limit($limit)
            ->get();
    }

    /**
     * Get user's ranking in different periods
     */
    public function getUserRanking(User $user)
    {
        return [
            'daily' => $this->getUserRankingByPeriod($user, Carbon::today(), Carbon::tomorrow()),
            'weekly' => $this->getUserRankingByPeriod($user, Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()),
            'all_time' => $this->getUserAllTimeRanking($user)
        ];
    }

    /**
     * Get user's ranking for a specific period
     */
    private function getUserRankingByPeriod(User $user, $startDate, $endDate)
    {
        $userStats = User::select('users.id')
            ->selectRaw('COUNT(CASE WHEN games.result = "player1_wins" AND games.player1_id = users.id THEN 1
                                   WHEN games.result = "player2_wins" AND games.player2_id = users.id THEN 1
                                   END) as wins')
            ->leftJoin('games', function($join) {
                $join->on(function($query) {
                    $query->where('games.player1_id', '=', DB::raw('users.id'))
                        ->orWhere('games.player2_id', '=', DB::raw('users.id'));
                });
            })
            ->where('games.status', 'completed')
            ->whereBetween('games.completed_at', [$startDate, $endDate])
            ->where('users.id', $user->id)
            ->groupBy('users.id')
            ->first();

        if (!$userStats || $userStats->wins == 0) {
            return null;
        }

        $rank = User::select('users.id')
            ->selectRaw('COUNT(CASE WHEN games.result = "player1_wins" AND games.player1_id = users.id THEN 1
                                   WHEN games.result = "player2_wins" AND games.player2_id = users.id THEN 1
                                   END) as wins')
            ->leftJoin('games', function($join) {
                $join->on(function($query) {
                    $query->where('games.player1_id', '=', DB::raw('users.id'))
                        ->orWhere('games.player2_id', '=', DB::raw('users.id'));
                });
            })
            ->where('games.status', 'completed')
            ->whereBetween('games.completed_at', [$startDate, $endDate])
            ->groupBy('users.id')
            ->havingRaw('COUNT(CASE WHEN games.result = "player1_wins" AND games.player1_id = users.id THEN 1
                                           WHEN games.result = "player2_wins" AND games.player2_id = users.id THEN 1
                                           END) > ?', [$userStats->wins])
            ->count();

        return $rank + 1;
    }

    /**
     * Get user's all-time ranking
     */
    private function getUserAllTimeRanking(User $user)
    {
        if ($user->games_won == 0) {
            return null;
        }

        $rank = User::where('games_won', '>', $user->games_won)
            ->orWhere(function($query) use ($user) {
                $query->where('games_won', $user->games_won)
                    ->whereRaw('(games_won + games_lost + games_drawn) > 0')
                    ->whereRaw('(games_won * 100.0) / (games_won + games_lost + games_drawn) > ?', [
                        $user->getTotalGamesAttribute() > 0 ? ($user->games_won * 100.0) / $user->getTotalGamesAttribute() : 0
                    ]);
            })
            ->count();

        return $rank + 1;
    }

    /**
     * API endpoint for leaderboard data
     */
    public function apiLeaderboard(Request $request)
    {
        $period = $request->get('period', 'all_time');
        $limit = $request->get('limit', 10);

        $data = match($period) {
            'daily' => $this->getDailyLeaderboard($limit),
            'weekly' => $this->getWeeklyLeaderboard($limit),
            'all_time' => $this->getAllTimeLeaderboard($limit),
            default => $this->getAllTimeLeaderboard($limit)
        };

        return response()->json([
            'success' => true,
            'period' => $period,
            'leaderboard' => $data
        ]);
    }

    /**
     * Get top performing users (for homepage widget)
     */
    public function getTopPerformers($limit = 5)
    {
        return $this->getAllTimeLeaderboard($limit);
    }

    /**
     * Get recent winners (for homepage activity feed)
     */
    public function getRecentWinners($limit = 10)
    {
        return Game::with(['winner', 'player1', 'player2', 'word'])
            ->where('status', 'completed')
            ->whereNotNull('winner_id')
            ->orderByDesc('completed_at')
            ->limit($limit)
            ->get()
            ->map(function($game) {
                return [
                    'winner' => $game->winner,
                    'loser' => $game->winner_id === $game->player1_id ? $game->player2 : $game->player1,
                    'word' => $game->word->word,
                    'completed_at' => $game->completed_at,
                    'attempts' => $game->moves()->where('player_id', $game->winner_id)->count()
                ];
            });
    }

    /**
     * Get statistics summary for dashboard
     */
    public function getStatsSummary()
    {
        $totalGames = Game::where('status', 'completed')->count();
        $totalPlayers = User::whereHas('games')->count();
        $gamesPlayedToday = Game::where('status', 'completed')
            ->whereDate('completed_at', Carbon::today())
            ->count();

        $averageAttemptsToWin = Game::join('game_moves', 'games.id', '=', 'game_moves.game_id')
            ->where('games.status', 'completed')
            ->whereNotNull('games.winner_id')
            ->where('game_moves.player_id', DB::raw('games.winner_id'))
            ->avg('game_moves.attempt_number');

        return [
            'total_games' => $totalGames,
            'total_players' => $totalPlayers,
            'games_today' => $gamesPlayedToday,
            'average_attempts' => round($averageAttemptsToWin, 1)
        ];
    }
}
