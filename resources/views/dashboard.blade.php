@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Welcome Header -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-8">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Welcome back, {{ auth()->user()->name }}!</h1>
                <p class="text-gray-600 mt-1">Here's what's happening with your Wordle games</p>
            </div>
            <div class="flex space-x-3">
                <a href="{{ route('games.create') }}" 
                   class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                    New Game
                </a>
            </div>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center">
                        <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                </div>
                <div class="ml-4">
                    <div class="text-2xl font-bold text-gray-900">{{ auth()->user()->games_won }}</div>
                    <div class="text-sm text-gray-500">Games Won</div>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
                        <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                        </svg>
                    </div>
                </div>
                <div class="ml-4">
                    <div class="text-2xl font-bold text-gray-900">{{ auth()->user()->games_played }}</div>
                    <div class="text-sm text-gray-500">Games Played</div>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-purple-100 rounded-full flex items-center justify-center">
                        <svg class="w-4 h-4 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                        </svg>
                    </div>
                </div>
                <div class="ml-4">
                    <div class="text-2xl font-bold text-gray-900">{{ auth()->user()->win_rate }}%</div>
                    <div class="text-sm text-gray-500">Win Rate</div>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-orange-100 rounded-full flex items-center justify-center">
                        <svg class="w-4 h-4 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                        </svg>
                    </div>
                </div>
                <div class="ml-4">
                    <div class="text-2xl font-bold text-gray-900">{{ auth()->user()->friends()->count() }}</div>
                    <div class="text-sm text-gray-500">Friends</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- Active Games -->
        <div class="bg-white rounded-lg shadow-md">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">Active Games</h3>
            </div>
            <div class="p-6">
                @if($activeGames->count() > 0)
                    <div class="space-y-4">
                        @foreach($activeGames as $game)
                            <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                                <div class="flex items-center">
                                    <div class="flex items-center mr-4">
                                        @if($game->getOpponent(auth()->user())->avatar)
                                            <img class="h-8 w-8 rounded-full" src="{{ Storage::url($game->getOpponent(auth()->user())->avatar) }}" alt="{{ $game->getOpponent(auth()->user())->name }}">
                                        @else
                                            <div class="h-8 w-8 rounded-full bg-gray-500 flex items-center justify-center text-white font-semibold">
                                                {{ substr($game->getOpponent(auth()->user())->name, 0, 1) }}
                                            </div>
                                        @endif
                                    </div>
                                    <div>
                                        <div class="text-sm font-medium text-gray-900">vs {{ $game->getOpponent(auth()->user())->name }}</div>
                                        <div class="text-sm text-gray-500">{{ $game->created_at->diffForHumans() }}</div>
                                    </div>
                                </div>
                                <div class="flex items-center space-x-2">
                                    @if($game->canPlayerMakeMove(auth()->user()))
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            Your Turn
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                            Waiting
                                        </span>
                                    @endif
                                    <a href="{{ route('games.show', $game) }}" 
                                       class="text-green-600 hover:text-green-500 text-sm font-medium">
                                        Continue
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <div class="mt-4">
                        <a href="{{ route('games.index') }}" class="text-green-600 hover:text-green-500 text-sm font-medium">
                            View all games →
                        </a>
                    </div>
                @else
                    <div class="text-center py-8">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900">No active games</h3>
                        <p class="mt-1 text-sm text-gray-500">Start a new game to begin playing!</p>
                        <div class="mt-6">
                            <a href="{{ route('games.create') }}" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                </svg>
                                New Game
                            </a>
                        </div>
                    </div>
                @endif
            </div>
        </div>

        <!-- Recent Activity -->
        <div class="bg-white rounded-lg shadow-md">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">Recent Activity</h3>
            </div>
            <div class="p-6">
                @if($recentGames->count() > 0)
                    <div class="space-y-4">
                        @foreach($recentGames as $game)
                            <div class="flex items-center justify-between">
                                <div class="flex items-center">
                                    <div class="flex items-center mr-3">
                                        @if($game->getOpponent(auth()->user())->avatar)
                                            <img class="h-6 w-6 rounded-full" src="{{ Storage::url($game->getOpponent(auth()->user())->avatar) }}" alt="{{ $game->getOpponent(auth()->user())->name }}">
                                        @else
                                            <div class="h-6 w-6 rounded-full bg-gray-500 flex items-center justify-center text-white text-xs font-semibold">
                                                {{ substr($game->getOpponent(auth()->user())->name, 0, 1) }}
                                            </div>
                                        @endif
                                    </div>
                                    <div>
                                        <div class="text-sm text-gray-900">
                                            @if($game->winner_id === auth()->id())
                                                <span class="text-green-600">You won</span> against {{ $game->getOpponent(auth()->user())->name }}
                                            @elseif($game->winner_id === $game->getOpponent(auth()->user())->id)
                                                <span class="text-red-600">You lost</span> to {{ $game->getOpponent(auth()->user())->name }}
                                            @else
                                                <span class="text-gray-600">Draw</span> with {{ $game->getOpponent(auth()->user())->name }}
                                            @endif
                                        </div>
                                        <div class="text-xs text-gray-500">{{ $game->updated_at->diffForHumans() }}</div>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <div class="text-xs text-gray-500">{{ $game->moves->count() }} moves</div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <div class="mt-4">
                        <a href="{{ route('games.history') }}" class="text-green-600 hover:text-green-500 text-sm font-medium">
                            View full history →
                        </a>
                    </div>
                @else
                    <div class="text-center py-8">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900">No recent activity</h3>
                        <p class="mt-1 text-sm text-gray-500">Start playing to see your activity here!</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="mt-8 bg-white rounded-lg shadow-md p-6">
        <h3 class="text-lg font-medium text-gray-900 mb-4">Quick Actions</h3>
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <a href="{{ route('games.create') }}" 
               class="flex items-center p-4 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center">
                        <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                    </div>
                </div>
                <div class="ml-3">
                    <div class="text-sm font-medium text-gray-900">New Game</div>
                    <div class="text-sm text-gray-500">Challenge a friend</div>
                </div>
            </a>
            
            <a href="{{ route('leaderboard.index') }}" 
               class="flex items-center p-4 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
                        <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                        </svg>
                    </div>
                </div>
                <div class="ml-3">
                    <div class="text-sm font-medium text-gray-900">Leaderboard</div>
                    <div class="text-sm text-gray-500">See rankings</div>
                </div>
            </a>
            
            <a href="{{ route('users.index') }}" 
               class="flex items-center p-4 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-purple-100 rounded-full flex items-center justify-center">
                        <svg class="w-4 h-4 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                        </svg>
                    </div>
                </div>
                <div class="ml-3">
                    <div class="text-sm font-medium text-gray-900">Find Players</div>
                    <div class="text-sm text-gray-500">Discover new friends</div>
                </div>
            </a>
            
            <a href="{{ route('settings.index') }}" 
               class="flex items-center p-4 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-gray-100 rounded-full flex items-center justify-center">
                        <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                    </div>
                </div>
                <div class="ml-3">
                    <div class="text-sm font-medium text-gray-900">Settings</div>
                    <div class="text-sm text-gray-500">Manage account</div>
                </div>
            </a>
        </div>
    </div>
</div>
@endsection 