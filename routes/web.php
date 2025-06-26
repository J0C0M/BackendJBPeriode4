<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{
    AuthController,
    HomeController,
    GameController,
    GameMoveController,
    UserController,
    UserSettingController,
    FriendshipController,
    CommentController,
    LeaderboardController,
    WordController
};
use App\Http\Controllers\Admin\{
    AdminController,
    UserManagementController,
    WordManagementController
};

// Public routes
Route::get('/', [HomeController::class, 'index'])->name('home');

// Authentication routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
    Route::get('/forgot-password', [AuthController::class, 'showForgotPasswordForm'])->name('password.request');
    Route::post('/forgot-password', [AuthController::class, 'sendResetLink'])->name('password.email');
});

// Protected routes
Route::middleware('auth')->group(function () {
    // Dashboard
    Route::get('/dashboard', [HomeController::class, 'dashboard'])->name('dashboard');
    
    // Logout
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    
    // Games
    Route::prefix('games')->name('games.')->group(function () {
        Route::get('/', [GameController::class, 'index'])->name('index');
        Route::get('/create', [GameController::class, 'create'])->name('create');
        Route::post('/', [GameController::class, 'store'])->name('store');
        Route::get('/{game}', [GameController::class, 'show'])->name('show');
        Route::post('/{game}/accept', [GameController::class, 'accept'])->name('accept');
        Route::post('/{game}/decline', [GameController::class, 'decline'])->name('decline');
        Route::delete('/{game}/cancel', [GameController::class, 'cancel'])->name('cancel');
        Route::get('/history', [GameController::class, 'history'])->name('history');
        Route::get('/leaderboard', [GameController::class, 'leaderboard'])->name('leaderboard');
    });
    
    // Game moves
    Route::prefix('games/{game}/moves')->name('games.moves.')->group(function () {
        Route::post('/', [GameMoveController::class, 'store'])->name('store');
        Route::get('/', [GameMoveController::class, 'getMoves'])->name('index');
    });
    
    // Users
    Route::prefix('users')->name('users.')->group(function () {
        Route::get('/', [UserController::class, 'index'])->name('index');
        Route::get('/search', [UserController::class, 'search'])->name('search');
        Route::get('/{user}', [UserController::class, 'show'])->name('show');
        Route::get('/{user}/history', [UserController::class, 'gameHistory'])->name('history');
        Route::get('/{user}/rank', [UserController::class, 'getRank'])->name('rank');
        Route::get('/{user}/statistics', [UserController::class, 'getStatistics'])->name('statistics');
        Route::get('/profile/edit', [UserController::class, 'edit'])->name('edit');
        Route::put('/profile', [UserController::class, 'update'])->name('update');
    });
    
    // Settings
    Route::prefix('settings')->name('settings.')->group(function () {
        Route::get('/', [UserSettingController::class, 'index'])->name('index');
        Route::put('/', [UserSettingController::class, 'update'])->name('update');
        Route::put('/password', [UserSettingController::class, 'updatePassword'])->name('password');
        Route::put('/profile', [UserSettingController::class, 'updateProfile'])->name('profile');
        Route::delete('/account', [UserSettingController::class, 'deleteAccount'])->name('delete');
        Route::get('/get', [UserSettingController::class, 'getSettings'])->name('get');
        Route::put('/ajax', [UserSettingController::class, 'updateSettingsAjax'])->name('ajax');
    });
    
    // Friendships
    Route::prefix('friends')->name('friends.')->group(function () {
        Route::get('/', [FriendshipController::class, 'index'])->name('index');
        Route::get('/create', [FriendshipController::class, 'create'])->name('create');
        Route::post('/search', [FriendshipController::class, 'search'])->name('search');
        Route::post('/', [FriendshipController::class, 'store'])->name('store');
        Route::post('/{friendship}/accept', [FriendshipController::class, 'accept'])->name('accept');
        Route::post('/{friendship}/decline', [FriendshipController::class, 'decline'])->name('decline');
        Route::delete('/{friendship}/cancel', [FriendshipController::class, 'cancel'])->name('cancel');
        Route::delete('/{user}', [FriendshipController::class, 'destroy'])->name('destroy');
        Route::post('/{user}/block', [FriendshipController::class, 'block'])->name('block');
        Route::get('/for-game', [FriendshipController::class, 'getFriendsForGame'])->name('for-game');
    });
    
    // Comments
    Route::prefix('comments')->name('comments.')->group(function () {
        Route::post('/', [CommentController::class, 'store'])->name('store');
        Route::get('/', [CommentController::class, 'getComments'])->name('get');
        Route::put('/{comment}', [CommentController::class, 'update'])->name('update');
        Route::delete('/{comment}', [CommentController::class, 'destroy'])->name('destroy');
        Route::post('/{comment}/toggle-approval', [CommentController::class, 'toggleApproval'])->name('toggle-approval');
        Route::get('/user/{user}', [CommentController::class, 'getUserComments'])->name('user');
        Route::get('/game/{game}', [CommentController::class, 'getGameComments'])->name('game');
    });
    
    // Leaderboard
    Route::prefix('leaderboard')->name('leaderboard.')->group(function () {
        Route::get('/', [LeaderboardController::class, 'index'])->name('index');
        Route::get('/api', [LeaderboardController::class, 'apiLeaderboard'])->name('api');
        Route::get('/top-performers', [LeaderboardController::class, 'getTopPerformers'])->name('top');
        Route::get('/recent-winners', [LeaderboardController::class, 'getRecentWinners'])->name('recent');
        Route::get('/stats', [LeaderboardController::class, 'getStatsSummary'])->name('stats');
    });
});

// Admin routes
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    // Admin dashboard
    Route::get('/', [AdminController::class, 'index'])->name('dashboard');
    Route::get('/system-info', [AdminController::class, 'systemInfo'])->name('system.info');
    Route::get('/logs', [AdminController::class, 'logs'])->name('logs');
    Route::post('/clear-cache', [AdminController::class, 'clearCache'])->name('clear-cache');
    Route::get('/export-data', [AdminController::class, 'exportData'])->name('export-data');
    Route::get('/stats', [AdminController::class, 'getStats'])->name('stats');
    
    // User management
    Route::prefix('users')->name('users.')->group(function () {
        Route::get('/', [UserManagementController::class, 'index'])->name('index');
        Route::get('/create', [UserManagementController::class, 'create'])->name('create');
        Route::post('/', [UserManagementController::class, 'store'])->name('store');
        Route::get('/{user}', [UserManagementController::class, 'show'])->name('show');
        Route::get('/{user}/edit', [UserManagementController::class, 'edit'])->name('edit');
        Route::put('/{user}', [UserManagementController::class, 'update'])->name('update');
        Route::delete('/{user}', [UserManagementController::class, 'destroy'])->name('destroy');
        Route::post('/bulk-action', [UserManagementController::class, 'bulkAction'])->name('bulk-action');
        Route::post('/{user}/reset-password', [UserManagementController::class, 'resetPassword'])->name('reset-password');
        Route::get('/{user}/game-history', [UserManagementController::class, 'gameHistory'])->name('game-history');
        Route::get('/{user}/friendships', [UserManagementController::class, 'friendships'])->name('friendships');
        Route::get('/export', [UserManagementController::class, 'export'])->name('export');
    });
    
    // Word management
    Route::prefix('words')->name('words.')->group(function () {
        Route::get('/', [WordManagementController::class, 'index'])->name('index');
        Route::get('/create', [WordManagementController::class, 'create'])->name('create');
        Route::post('/', [WordManagementController::class, 'store'])->name('store');
        Route::get('/{word}', [WordManagementController::class, 'show'])->name('show');
        Route::get('/{word}/edit', [WordManagementController::class, 'edit'])->name('edit');
        Route::put('/{word}', [WordManagementController::class, 'update'])->name('update');
        Route::delete('/{word}', [WordManagementController::class, 'destroy'])->name('destroy');
        Route::post('/bulk-import', [WordManagementController::class, 'bulkImport'])->name('bulk-import');
        Route::post('/{word}/toggle-status', [WordManagementController::class, 'toggleStatus'])->name('toggle-status');
        Route::get('/export', [WordManagementController::class, 'export'])->name('export');
    });
});

// Public word routes (for validation)
Route::prefix('words')->name('words.')->group(function () {
    Route::get('/validate', [WordController::class, 'validateWord'])->name('validate');
    Route::get('/random', [WordController::class, 'random'])->name('random');
    Route::get('/statistics', [WordController::class, 'statistics'])->name('statistics');
});

// API routes
Route::middleware('auth:sanctum')->prefix('api')->name('api.')->group(function () {
    // Game API
    Route::prefix('games')->name('games.')->group(function () {
        Route::get('/{game}/state', [App\Http\Controllers\Api\GameApiController::class, 'getGameState'])->name('state');
        Route::post('/{game}/move', [App\Http\Controllers\Api\GameApiController::class, 'makeMove'])->name('move');
        Route::get('/user', [App\Http\Controllers\Api\GameApiController::class, 'getUserGames'])->name('user');
        Route::post('/{game}/accept', [App\Http\Controllers\Api\GameApiController::class, 'acceptGame'])->name('accept');
        Route::post('/{game}/decline', [App\Http\Controllers\Api\GameApiController::class, 'declineGame'])->name('decline');
        Route::get('/history', [App\Http\Controllers\Api\GameApiController::class, 'getGameHistory'])->name('history');
    });
    
    // Friendship API
    Route::prefix('friendships')->name('friendships.')->group(function () {
        Route::get('/', [App\Http\Controllers\Api\FriendshipApiController::class, 'index'])->name('index');
        Route::get('/pending', [App\Http\Controllers\Api\FriendshipApiController::class, 'getPendingRequests'])->name('pending');
        Route::get('/sent', [App\Http\Controllers\Api\FriendshipApiController::class, 'getSentRequests'])->name('sent');
        Route::post('/search', [App\Http\Controllers\Api\FriendshipApiController::class, 'searchUsers'])->name('search');
        Route::post('/send-request', [App\Http\Controllers\Api\FriendshipApiController::class, 'sendRequest'])->name('send');
        Route::post('/{friendshipId}/accept', [App\Http\Controllers\Api\FriendshipApiController::class, 'acceptRequest'])->name('accept');
        Route::post('/{friendshipId}/decline', [App\Http\Controllers\Api\FriendshipApiController::class, 'declineRequest'])->name('decline');
        Route::delete('/{friendshipId}/cancel', [App\Http\Controllers\Api\FriendshipApiController::class, 'cancelRequest'])->name('cancel');
        Route::delete('/{friendId}/remove', [App\Http\Controllers\Api\FriendshipApiController::class, 'removeFriend'])->name('remove');
        Route::get('/stats', [App\Http\Controllers\Api\FriendshipApiController::class, 'getStats'])->name('stats');
    });
});

// Fallback route
Route::fallback(function () {
    return view('errors.404');
});
