<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\GameController;
use App\Http\Controllers\GameMoveController;
use App\Http\Controllers\FriendshipController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\UserSettingController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\LeaderboardController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\UserManagementController;
use App\Http\Controllers\Admin\WordManagementController;

// Publieke routes
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/leaderboard', [LeaderboardController::class, 'index'])->name('leaderboard');

// Authenticatie routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
    Route::get('/forgot-password', [AuthController::class, 'showForgotPasswordForm'])->name('password.request');
});

Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

// Routes die authenticatie vereisen
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [HomeController::class, 'dashboard'])->name('dashboard');

    // Game routes
    Route::resource('games', GameController::class);
    Route::post('/games/{game}/moves', [GameMoveController::class, 'store'])->name('games.moves.store');
    Route::post('/games/{game}/accept', [GameController::class, 'accept'])->name('games.accept');
    Route::post('/games/{game}/decline', [GameController::class, 'decline'])->name('games.decline');
    Route::post('/games/{game}/cancel', [GameController::class, 'cancel'])->name('games.cancel');

    // Friendship routes
    Route::resource('friends', FriendshipController::class)->except(['show', 'edit', 'update']);
    Route::get('/friends/search', [FriendshipController::class, 'search'])->name('friends.search');
    Route::post('/friends/{friendship}/accept', [FriendshipController::class, 'accept'])->name('friends.accept');
    Route::post('/friends/{friendship}/decline', [FriendshipController::class, 'decline'])->name('friends.decline');
    Route::delete('/friends/{friendship}', [FriendshipController::class, 'destroy'])->name('friends.destroy');

    // User routes
    Route::resource('users', UserController::class)->only(['index', 'show', 'edit', 'update']);

    // Settings routes
    Route::get('/settings', [UserSettingController::class, 'index'])->name('settings.index');
    Route::post('/settings', [UserSettingController::class, 'update'])->name('settings.update');
    Route::post('/settings/password', [UserSettingController::class, 'updatePassword'])->name('settings.password');
    Route::post('/settings/profile', [UserSettingController::class, 'updateProfile'])->name('settings.profile');

    // Comment routes
    Route::post('/comments', [CommentController::class, 'store'])->name('comments.store');
    Route::get('/comments', [CommentController::class, 'getComments'])->name('comments.get');
    Route::delete('/comments/{comment}', [CommentController::class, 'destroy'])->name('comments.destroy');
});

// Admin routes
Route::prefix('admin')->name('admin.')->middleware(['auth', 'admin'])->group(function () {
    Route::get('/', [AdminController::class, 'index'])->name('dashboard');
    Route::get('/system-info', [AdminController::class, 'systemInfo'])->name('system.info');
    Route::get('/logs', [AdminController::class, 'logs'])->name('logs');
    Route::post('/cache/clear', [AdminController::class, 'clearCache'])->name('cache.clear');
    Route::get('/export/{type}', [AdminController::class, 'exportData'])->name('export');

    // User management
    Route::resource('users', UserManagementController::class);
    Route::post('/users/{user}/ban', [UserManagementController::class, 'ban'])->name('users.ban');
    Route::post('/users/{user}/unban', [UserManagementController::class, 'unban'])->name('users.unban');

    // Word management
    Route::resource('words', WordManagementController::class);
    Route::post('/words/bulk-import', [WordManagementController::class, 'bulkImport'])->name('words.bulk-import');
    Route::post('/words/{word}/toggle', [WordManagementController::class, 'toggle'])->name('words.toggle');
});

// API routes
Route::prefix('api')->middleware('auth:sanctum')->group(function () {
    Route::get('/games/{game}/state', [App\Http\Controllers\Api\GameApiController::class, 'getGameState']);
    Route::post('/games/{game}/moves', [App\Http\Controllers\Api\GameApiController::class, 'makeMove']);

    Route::get('/friends', [App\Http\Controllers\Api\FriendshipApiController::class, 'index']);
    Route::get('/friends/pending', [App\Http\Controllers\Api\FriendshipApiController::class, 'getPendingRequests']);
    Route::get('/friends/sent', [App\Http\Controllers\Api\FriendshipApiController::class, 'getSentRequests']);
    Route::get('/friends/search', [App\Http\Controllers\Api\FriendshipApiController::class, 'searchUsers']);
    Route::post('/friends/request', [App\Http\Controllers\Api\FriendshipApiController::class, 'sendRequest']);
});
