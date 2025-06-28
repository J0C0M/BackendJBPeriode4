@extends('layouts.app')

@section('title', 'Home')

@section('content')
<div class="bg-gradient-to-br from-green-50 to-blue-50 min-h-screen">
    <!-- Hero Section -->
    <div class="relative overflow-hidden">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-24">
            <div class="text-center">
                <h1 class="text-4xl md:text-6xl font-bold text-gray-900 mb-6">
                    Challenge Your Friends in
                    <span class="text-green-600">Wordle</span>
                </h1>
                <p class="text-xl text-gray-600 mb-8 max-w-3xl mx-auto">
                    Experience the classic word-guessing game with multiplayer features. 
                    Compete against friends, track your progress, and climb the leaderboards!
                </p>
                
                <div class="flex flex-col sm:flex-row gap-4 justify-center">
                    @auth
                        <a href="{{ route('games.create') }}" 
                           class="inline-flex items-center px-8 py-3 border border-transparent text-base font-medium rounded-md text-white bg-green-600 hover:bg-green-700 transition-colors">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                            </svg>
                            Start New Game
                        </a>
                        <a href="{{ route('leaderboard.index') }}" 
                           class="inline-flex items-center px-8 py-3 border border-gray-300 text-base font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 transition-colors">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                            </svg>
                            View Leaderboard
                        </a>
                    @else
                        <a href="{{ route('register') }}" 
                           class="inline-flex items-center px-8 py-3 border border-transparent text-base font-medium rounded-md text-white bg-green-600 hover:bg-green-700 transition-colors">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path>
                            </svg>
                            Get Started
                        </a>
                        <a href="{{ route('login') }}" 
                           class="inline-flex items-center px-8 py-3 border border-gray-300 text-base font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 transition-colors">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"></path>
                            </svg>
                            Sign In
                        </a>
                    @endauth
                </div>
            </div>
        </div>
    </div>

    <!-- Game Statistics -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
        <div class="text-center mb-12">
            <h2 class="text-3xl font-bold text-gray-900 mb-4">Game Statistics</h2>
            <p class="text-lg text-gray-600">See how the community is performing</p>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
            <div class="bg-white rounded-lg shadow-md p-6 text-center">
                <div class="text-3xl font-bold text-green-600 mb-2">{{ number_format($gameStats['total_games']) }}</div>
                <div class="text-gray-600">Games Played</div>
            </div>
            
            <div class="bg-white rounded-lg shadow-md p-6 text-center">
                <div class="text-3xl font-bold text-blue-600 mb-2">{{ number_format($gameStats['total_players']) }}</div>
                <div class="text-gray-600">Active Players</div>
            </div>
            
            <div class="bg-white rounded-lg shadow-md p-6 text-center">
                <div class="text-3xl font-bold text-purple-600 mb-2">{{ number_format($gameStats['games_today']) }}</div>
                <div class="text-gray-600">Games Today</div>
            </div>
            
            <div class="bg-white rounded-lg shadow-md p-6 text-center">
                <div class="text-3xl font-bold text-orange-600 mb-2">{{ number_format($gameStats['active_games']) }}</div>
                <div class="text-gray-600">Active Games</div>
            </div>
        </div>
    </div>

    <!-- Leaderboards Section -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
        <div class="text-center mb-12">
            <h2 class="text-3xl font-bold text-gray-900 mb-4">Top Players</h2>
            <p class="text-lg text-gray-600">See who's dominating the leaderboards</p>
        </div>
        
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Daily Leaderboard -->
            <div class="bg-white rounded-lg shadow-md">
                <div class="p-6 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">Today's Winners</h3>
                    <p class="text-sm text-gray-600">Most wins today</p>
                </div>
                <div class="p-6">
                    @if($leaderboards['daily']->count() > 0)
                        <div class="space-y-3">
                            @foreach($leaderboards['daily']->take(5) as $index => $player)
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center">
                                        <span class="text-lg font-bold text-gray-400 w-6">{{ $index + 1 }}</span>
                                        <div class="ml-3">
                                            <div class="font-medium text-gray-900">{{ $player->name }}</div>
                                            <div class="text-sm text-gray-500">@{{ $player->username }}</div>
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <div class="font-semibold text-green-600">{{ $player->daily_wins }}</div>
                                        <div class="text-xs text-gray-500">wins</div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-gray-500 text-center py-4">No games played today</p>
                    @endif
                </div>
            </div>
            
            <!-- Weekly Leaderboard -->
            <div class="bg-white rounded-lg shadow-md">
                <div class="p-6 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">This Week</h3>
                    <p class="text-sm text-gray-600">Most wins this week</p>
                </div>
                <div class="p-6">
                    @if($leaderboards['weekly']->count() > 0)
                        <div class="space-y-3">
                            @foreach($leaderboards['weekly']->take(5) as $index => $player)
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center">
                                        <span class="text-lg font-bold text-gray-400 w-6">{{ $index + 1 }}</span>
                                        <div class="ml-3">
                                            <div class="font-medium text-gray-900">{{ $player->name }}</div>
                                            <div class="text-sm text-gray-500">@{{ $player->username }}</div>
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <div class="font-semibold text-blue-600">{{ $player->weekly_wins }}</div>
                                        <div class="text-xs text-gray-500">wins</div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-gray-500 text-center py-4">No games played this week</p>
                    @endif
                </div>
            </div>
            
            <!-- All-Time Leaderboard -->
            <div class="bg-white rounded-lg shadow-md">
                <div class="p-6 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">All Time</h3>
                    <p class="text-sm text-gray-600">Most wins ever</p>
                </div>
                <div class="p-6">
                    @if($leaderboards['all_time']->count() > 0)
                        <div class="space-y-3">
                            @foreach($leaderboards['all_time']->take(5) as $index => $player)
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center">
                                        <span class="text-lg font-bold text-gray-400 w-6">{{ $index + 1 }}</span>
                                        <div class="ml-3">
                                            <div class="font-medium text-gray-900">{{ $player->name }}</div>
                                            <div class="text-sm text-gray-500">@{{ $player->username }}</div>
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <div class="font-semibold text-purple-600">{{ $player->games_won }}</div>
                                        <div class="text-xs text-gray-500">wins</div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-gray-500 text-center py-4">No games played yet</p>
                    @endif
                </div>
            </div>
        </div>
        
        <div class="text-center mt-8">
            <a href="{{ route('leaderboard.index') }}" 
               class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-md text-white bg-green-600 hover:bg-green-700 transition-colors">
                View Full Leaderboard
                <svg class="ml-2 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                </svg>
            </a>
        </div>
    </div>

    <!-- Features Section -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
        <div class="text-center mb-12">
            <h2 class="text-3xl font-bold text-gray-900 mb-4">Why Choose Our Wordle?</h2>
            <p class="text-lg text-gray-600">Discover what makes our multiplayer Wordle experience unique</p>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <div class="text-center">
                <div class="mx-auto h-16 w-16 flex items-center justify-center rounded-full bg-green-100 mb-4">
                    <svg class="h-8 w-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                </div>
                <h3 class="text-xl font-semibold text-gray-900 mb-2">Multiplayer Experience</h3>
                <p class="text-gray-600">Challenge your friends in real-time multiplayer games with turn-based gameplay.</p>
            </div>
            
            <div class="text-center">
                <div class="mx-auto h-16 w-16 flex items-center justify-center rounded-full bg-blue-100 mb-4">
                    <svg class="h-8 w-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                    </svg>
                </div>
                <h3 class="text-xl font-semibold text-gray-900 mb-2">Leaderboards</h3>
                <p class="text-gray-600">Compete on daily, weekly, and all-time leaderboards to prove your word-guessing skills.</p>
            </div>
            
            <div class="text-center">
                <div class="mx-auto h-16 w-16 flex items-center justify-center rounded-full bg-purple-100 mb-4">
                    <svg class="h-8 w-8 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <h3 class="text-xl font-semibold text-gray-900 mb-2">Real-time Updates</h3>
                <p class="text-gray-600">Experience smooth, real-time gameplay with instant feedback and live game state updates.</p>
            </div>
        </div>
    </div>
</div>
@endsection 