<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Game;
use App\Models\Friendship;
use App\Models\Comment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class UserManagementController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('admin'); // You'll need to create this middleware
    }

    /**
     * Display a listing of users with search and filter capabilities
     */
    public function index(Request $request)
    {
        $query = User::query();

        // Search functionality
        if ($request->filled('search')) {
            $searchTerm = $request->get('search');
            $query->where(function($q) use ($searchTerm) {
                $q->where('name', 'like', "%{$searchTerm}%")
                    ->orWhere('username', 'like', "%{$searchTerm}%")
                    ->orWhere('email', 'like', "%{$searchTerm}%");
            });
        }

        // Filter by registration date
        if ($request->filled('date_from')) {
            $query->where('created_at', '>=', $request->get('date_from'));
        }

        if ($request->filled('date_to')) {
            $query->where('created_at', '<=', $request->get('date_to') . ' 23:59:59');
        }

        // Filter by activity (last login)
        if ($request->filled('activity_filter')) {
            $activityFilter = $request->get('activity_filter');
            switch ($activityFilter) {
                case 'active_today':
                    $query->whereDate('last_login_at', today());
                    break;
                case 'active_week':
                    $query->where('last_login_at', '>=', now()->subWeek());
                    break;
                case 'active_month':
                    $query->where('last_login_at', '>=', now()->subMonth());
                    break;
                case 'inactive':
                    $query->where(function($q) {
                        $q->whereNull('last_login_at')
                            ->orWhere('last_login_at', '<', now()->subMonth());
                    });
                    break;
            }
        }

        // Sorting
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');

        $allowedSorts = ['name', 'username', 'email', 'created_at', 'last_login_at', 'games_won', 'games_lost'];
        if (in_array($sortBy, $allowedSorts)) {
            $query->orderBy($sortBy, $sortOrder);
        }

        $users = $query->withCount(['gamesAsPlayer1', 'gamesAsPlayer2', 'friends', 'comments'])
            ->paginate(20)
            ->appends($request->all());

        // Get statistics for dashboard
        $stats = [
            'total_users' => User::count(),
            'new_users_today' => User::whereDate('created_at', today())->count(),
            'new_users_week' => User::where('created_at', '>=', now()->subWeek())->count(),
            'active_users_today' => User::whereDate('last_login_at', today())->count(),
            'active_users_week' => User::where('last_login_at', '>=', now()->subWeek())->count(),
        ];

        return view('admin.users.index', compact('users', 'stats', 'request'));
    }

    /**
     * Show the form for creating a new user
     */
    public function create()
    {
        return view('admin.users.create');
    }

    /**
     * Store a newly created user
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'max:255', 'unique:users'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'avatar' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048'],
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $userData = [
            'name' => $request->name,
            'username' => $request->username,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'email_verified_at' => now(), // Admin created users are auto-verified
        ];

        // Handle avatar upload
        if ($request->hasFile('avatar')) {
            $avatarPath = $request->file('avatar')->store('avatars', 'public');
            $userData['avatar'] = $avatarPath;
        }

        $user = User::create($userData);

        // Create default user settings
        $user->settings()->create([
            'email_notifications' => true,
            'friend_requests_notifications' => true,
            'game_invitations_notifications' => true,
            'privacy_level' => 'public',
            'show_statistics' => true,
            'theme' => 'light',
        ]);

        return redirect()->route('admin.users.index')
            ->with('success', 'User created successfully.');
    }

    /**
     * Display the specified user with detailed information
     */
    public function show(User $user)
    {
        $user->load(['settings', 'gamesAsPlayer1', 'gamesAsPlayer2', 'friends', 'comments']);

        // Get user statistics
        $stats = [
            'total_games' => $user->getTotalGamesAttribute(),
            'win_rate' => $user->getWinRateAttribute(),
            'games_won' => $user->games_won,
            'games_lost' => $user->games_lost,
            'games_drawn' => $user->games_drawn,
            'friends_count' => $user->friends()->count(),
            'comments_count' => $user->comments()->count(),
        ];

        // Get recent games
        $recentGames = Game::where('player1_id', $user->id)
            ->orWhere('player2_id', $user->id)
            ->with(['player1', 'player2', 'winner', 'word'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        // Get recent comments
        $recentComments = $user->comments()
            ->with('commentable')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        return view('admin.users.show', compact('user', 'stats', 'recentGames', 'recentComments'));
    }

    /**
     * Show the form for editing the specified user
     */
    public function edit(User $user)
    {
        $user->load('settings');
        return view('admin.users.edit', compact('user'));
    }

    /**
     * Update the specified user
     */
    public function update(Request $request, User $user)
    {
        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'max:255', Rule::unique('users')->ignore($user->id)],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
            'avatar' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048'],
            'games_won' => ['nullable', 'integer', 'min:0'],
            'games_lost' => ['nullable', 'integer', 'min:0'],
            'games_drawn' => ['nullable', 'integer', 'min:0'],
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $userData = [
            'name' => $request->name,
            'username' => $request->username,
            'email' => $request->email,
        ];

        // Update password if provided
        if ($request->filled('password')) {
            $userData['password'] = Hash::make($request->password);
        }

        // Handle avatar upload
        if ($request->hasFile('avatar')) {
            // Delete old avatar if exists
            if ($user->avatar && \Storage::disk('public')->exists($user->avatar)) {
                \Storage::disk('public')->delete($user->avatar);
            }

            $avatarPath = $request->file('avatar')->store('avatars', 'public');
            $userData['avatar'] = $avatarPath;
        }

        // Update game statistics if provided
        if ($request->filled('games_won')) {
            $userData['games_won'] = $request->games_won;
        }
        if ($request->filled('games_lost')) {
            $userData['games_lost'] = $request->games_lost;
        }
        if ($request->filled('games_drawn')) {
            $userData['games_drawn'] = $request->games_drawn;
        }

        $user->update($userData);

        return redirect()->route('admin.users.show', $user)
            ->with('success', 'User updated successfully.');
    }

    /**
     * Remove the specified user from storage
     */
    public function destroy(User $user)
    {
        // Prevent deletion of admin users (you might want to add role checking)
        if ($user->id === auth()->id()) {
            return back()->with('error', 'You cannot delete your own account.');
        }

        // Delete user avatar if exists
        if ($user->avatar && \Storage::disk('public')->exists($user->avatar)) {
            \Storage::disk('public')->delete($user->avatar);
        }

        // The foreign key constraints will handle cascading deletes
        $user->delete();

        return redirect()->route('admin.users.index')
            ->with('success', 'User deleted successfully.');
    }

    /**
     * Bulk actions for multiple users
     */
    public function bulkAction(Request $request)
    {
        $request->validate([
            'action' => 'required|in:delete,activate,deactivate',
            'user_ids' => 'required|array',
            'user_ids.*' => 'exists:users,id',
        ]);

        $userIds = $request->user_ids;
        $action = $request->action;

        // Prevent admin from affecting their own account in bulk actions
        $userIds = array_filter($userIds, function($id) {
            return $id != auth()->id();
        });

        switch ($action) {
            case 'delete':
                User::whereIn('id', $userIds)->delete();
                $message = 'Selected users have been deleted successfully.';
                break;

            case 'activate':
                User::whereIn('id', $userIds)->update(['email_verified_at' => now()]);
                $message = 'Selected users have been activated successfully.';
                break;

            case 'deactivate':
                User::whereIn('id', $userIds)->update(['email_verified_at' => null]);
                $message = 'Selected users have been deactivated successfully.';
                break;
        }

        return redirect()->route('admin.users.index')->with('success', $message);
    }

    /**
     * Reset user password
     */
    public function resetPassword(User $user)
    {
        // Generate a temporary password
        $tempPassword = \Str::random(10);

        $user->update([
            'password' => Hash::make($tempPassword)
        ]);

        // In a real application, you would email this to the user
        // For now, we'll just show it to the admin
        return back()->with('success', "Password reset successfully. Temporary password: {$tempPassword}");
    }

    /**
     * View user's game history
     */
    public function gameHistory(User $user)
    {
        $games = Game::where('player1_id', $user->id)
            ->orWhere('player2_id', $user->id)
            ->with(['player1', 'player2', 'winner', 'word', 'moves'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('admin.users.game-history', compact('user', 'games'));
    }

    /**
     * View user's friends and friendships
     */
    public function friendships(User $user)
    {
        $friendships = Friendship::where('requester_id', $user->id)
            ->orWhere('addressee_id', $user->id)
            ->with(['requester', 'addressee'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('admin.users.friendships', compact('user', 'friendships'));
    }

    /**
     * Export users data to CSV
     */
    public function export(Request $request)
    {
        $query = User::query();

        // Apply same filters as index method
        if ($request->filled('search')) {
            $searchTerm = $request->get('search');
            $query->where(function($q) use ($searchTerm) {
                $q->where('name', 'like', "%{$searchTerm}%")
                    ->orWhere('username', 'like', "%{$searchTerm}%")
                    ->orWhere('email', 'like', "%{$searchTerm}%");
            });
        }

        $users = $query->get();

        $filename = 'users_export_' . now()->format('Y-m-d_H-i-s') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        return response()->stream(function() use ($users) {
            $file = fopen('php://output', 'w');

            // CSV headers
            fputcsv($file, [
                'ID', 'Name', 'Username', 'Email', 'Games Won', 'Games Lost',
                'Games Drawn', 'Total Games', 'Win Rate', 'Created At', 'Last Login'
            ]);

            // CSV data
            foreach ($users as $user) {
                fputcsv($file, [
                    $user->id,
                    $user->name,
                    $user->username,
                    $user->email,
                    $user->games_won,
                    $user->games_lost,
                    $user->games_drawn,
                    $user->getTotalGamesAttribute(),
                    round($user->getWinRateAttribute(), 2) . '%',
                    $user->created_at->format('Y-m-d H:i:s'),
                    $user->last_login_at ? $user->last_login_at->format('Y-m-d H:i:s') : 'Never',
                ]);
            }

            fclose($file);
        }, 200, $headers);
    }
}
