<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Game;
use App\Models\Word;
use App\Models\Comment;
use App\Models\Friendship;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    public function __construct()
    {
        // Add middleware to ensure only admins can access
        $this->middleware('auth');
        // You may want to add an admin middleware here
        // $this->middleware('admin');
    }

    /**
     * Display the admin dashboard with overview statistics
     */
    public function index()
    {
        // Get general statistics
        $totalUsers = User::count();
        $totalGames = Game::count();
        $totalWords = Word::count();
        $totalComments = Comment::count();
        $totalFriendships = Friendship::where('status', 'accepted')->count();

        // Get recent activity
        $recentUsers = User::latest()->take(5)->get();
        $recentGames = Game::with(['player1', 'player2', 'word'])
            ->latest()
            ->take(5)
            ->get();

        // Get game statistics
        $gameStats = [
            'completed' => Game::where('status', 'completed')->count(),
            'in_progress' => Game::where('status', 'in_progress')->count(),
            'pending' => Game::where('status', 'pending')->count(),
            'cancelled' => Game::where('status', 'cancelled')->count(),
        ];

        // Get daily stats for the last 7 days
        $dailyStats = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $dailyStats[] = [
                'date' => $date->format('M d'),
                'users' => User::whereDate('created_at', $date)->count(),
                'games' => Game::whereDate('created_at', $date)->count(),
            ];
        }

        // Get top players
        $topPlayers = User::orderByDesc('games_won')
            ->limit(5)
            ->get();

        // Get most used words
        $mostUsedWords = Word::withCount('games')
            ->orderByDesc('games_count')
            ->limit(5)
            ->get();

        // Get system health metrics
        $systemMetrics = [
            'active_users_today' => User::whereDate('last_login_at', Carbon::today())->count(),
            'active_games' => Game::whereIn('status', ['pending', 'in_progress'])->count(),
            'pending_friend_requests' => Friendship::where('status', 'pending')->count(),
            'comments_today' => Comment::whereDate('created_at', Carbon::today())->count(),
        ];

        return view('admin.dashboard', compact(
            'totalUsers',
            'totalGames',
            'totalWords',
            'totalComments',
            'totalFriendships',
            'recentUsers',
            'recentGames',
            'gameStats',
            'dailyStats',
            'topPlayers',
            'mostUsedWords',
            'systemMetrics'
        ));
    }

    /**
     * Display system information and health
     */
    public function systemInfo()
    {
        $info = [
            'php_version' => PHP_VERSION,
            'laravel_version' => app()->version(),
            'database_type' => config('database.default'),
            'environment' => app()->environment(),
            'debug_mode' => config('app.debug'),
            'timezone' => config('app.timezone'),
            'memory_limit' => ini_get('memory_limit'),
            'max_execution_time' => ini_get('max_execution_time'),
        ];

        // Database health check
        try {
            DB::connection()->getPdo();
            $dbStatus = 'Connected';
        } catch (\Exception $e) {
            $dbStatus = 'Connection Failed: ' . $e->getMessage();
        }

        // Storage check
        $storageInfo = [
            'logs_writable' => is_writable(storage_path('logs')),
            'cache_writable' => is_writable(storage_path('framework/cache')),
            'sessions_writable' => is_writable(storage_path('framework/sessions')),
        ];

        return view('admin.system-info', compact('info', 'dbStatus', 'storageInfo'));
    }

    /**
     * Display application logs
     */
    public function logs(Request $request)
    {
        $logFile = storage_path('logs/laravel.log');
        $logs = [];

        if (file_exists($logFile)) {
            $content = file_get_contents($logFile);
            $lines = explode("\n", $content);

            // Get the last 100 lines
            $lines = array_slice($lines, -100);

            foreach ($lines as $line) {
                if (!empty(trim($line))) {
                    $logs[] = $line;
                }
            }
        }

        return view('admin.logs', compact('logs'));
    }

    /**
     * Clear application cache
     */
    public function clearCache()
    {
        try {
            \Artisan::call('cache:clear');
            \Artisan::call('config:clear');
            \Artisan::call('route:clear');
            \Artisan::call('view:clear');

            return redirect()->back()->with('success', 'Cache cleared successfully!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to clear cache: ' . $e->getMessage());
        }
    }

    /**
     * Export data for backup
     */
    public function exportData(Request $request)
    {
        $type = $request->get('type', 'users');

        switch ($type) {
            case 'users':
                $data = User::select('id', 'name', 'username', 'email', 'games_won', 'games_lost', 'games_drawn', 'created_at')->get();
                break;
            case 'games':
                $data = Game::with(['player1:id,username', 'player2:id,username', 'word:id,word'])
                    ->select('id', 'player1_id', 'player2_id', 'word_id', 'status', 'result', 'created_at', 'completed_at')
                    ->get();
                break;
            case 'words':
                $data = Word::select('id', 'word', 'difficulty', 'is_active', 'created_at')->get();
                break;
            default:
                return redirect()->back()->with('error', 'Invalid export type');
        }

        $filename = $type . '_export_' . date('Y-m-d_H-i-s') . '.json';

        return response()->json($data, 200, [
            'Content-Type' => 'application/json',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }

    /**
     * Get statistics for AJAX requests
     */
    public function getStats(Request $request)
    {
        $period = $request->get('period', '7'); // days

        $stats = [];
        for ($i = intval($period) - 1; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $stats[] = [
                'date' => $date->format('Y-m-d'),
                'users' => User::whereDate('created_at', $date)->count(),
                'games' => Game::whereDate('created_at', $date)->count(),
                'completed_games' => Game::whereDate('completed_at', $date)->count(),
            ];
        }

        return response()->json($stats);
    }
}
