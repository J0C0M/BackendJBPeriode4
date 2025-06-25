@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- Welcome Section -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
            <div class="p-6 bg-white border-b border-gray-200">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">Welcome back, {{ $user->name }}!</h1>
                        <p class="mt-1 text-sm text-gray-600">Ready for your next word challenge?</p>
                    </div>
                    <div class="flex space-x-4">
                        <a href="{{ route('games.create') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:border-indigo-900 focus:ring ring-indigo-300 disabled:opacity-25 transition ease-in-out duration-150">
                            New Game
                        </a>
                        <a href="{{ route('friends.create') }}" class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 active:bg-green-900 focus:outline-none focus:border-green-900 focus:ring ring-green-300 disabled:opacity-25 transition ease-in-out duration-150">
                            Add Friends
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center">
                                <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-500">Games Won</p>
                            <p class="text-2xl font-semibold text-gray-900">{{ $userStats['games_won'] }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-red-100 rounded-full flex items-center justify-center">
                                <svg class="w-4 h-4 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </div>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-500">Games Lost</p>
                            <p class="text-2xl font-semibold text-gray-900">{{ $userStats['games_lost'] }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-yellow-100 rounded-full flex items-center justify-center">
                                <svg class="w-4 h-4 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"></path>
                                </svg>
                            </div>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-500">Games Drawn</p>
                            <p class="text-2xl font-semibold text-gray-900">{{ $userStats['games_drawn'] }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
                                <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                                </svg>
                            </div>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-500">Win Rate</p>
                            <p class="text-2xl font-semibold text-gray-900">{{ number_format($userStats['win_rate'], 1) }}%</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Pending Invitations -->
            @if($pendingInvitations->count() > 0)
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h2 class="text-lg font-medium text-gray-900 mb-4">Pending Game Invitations</h2>
                    <div class="space-y-3">
                        @foreach($pendingInvitations as $game)
                        <div class="flex items-center justify-between p-3 bg-yellow-50 rounded-lg">
                            <div class="flex items-center">
                                @if($game->player1->avatar)
                                <img class="h-8 w-8 rounded-full object-cover" src="{{ Storage::url($game->player1->avatar) }}" alt="{{ $game->player1->name }}">
                                @else
                                <div class="h-8 w-8 rounded-full bg-indigo-500 flex items-center justify-center text-white text-sm">
                                    {{ strtoupper(substr($game->player1->name, 0, 1)) }}
                                </div>
                                @endif
                                <div class="ml-3">
                                    <p class="text-sm font-medium text-gray-900">{{ $game->player1->name }}</p>
                                    <p class="text-xs text-gray-500">{{ $game->created_at->diffForHumans() }}</p>
                                </div>
                            </div>
                            <div class="flex space-x-2">
                                <form action="{{ route('games.accept', $game) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" class="px-3 py-1 bg-green-600 text-white text-xs font-medium rounded hover:bg-green-700">
                                        Accept
                                    </button>
                                </form>
                                <form action="{{ route('games.decline', $game) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" class="px-3 py-1 bg-red-600 text-white text-xs font-medium rounded hover:bg-red-700">
                                        Decline
                                    </button>
                                </form>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
            @endif

            <!-- Active Games -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h2 class="text-lg font-medium text-gray-900 mb-4">Active Games</h2>
                    @if($activeGames->count() > 0)
                    <div class="space-y-3">
                        @foreach($activeGames as $game)
                        <div class="flex items-center justify-between p-3 bg-blue-50 rounded-lg">
                            <div class="flex items-center">
                                @php
                                $opponent = $game->getOpponent($user);
                                @endphp
                                @if($opponent->avatar)
                                <img class="h-8 w-8 rounded-full object-cover" src="{{ Storage::url($opponent->avatar) }}" alt="{{ $opponent->name }}">
                                @else
                                <div class="h-8 w-8 rounded-full bg-indigo-500 flex items-center justify-center text-white text-sm">
                                    {{ strtoupper(substr($opponent->name, 0, 1)) }}
                                </div>
                                @endif
                                <div class="ml-3">
                                    <p class="text-sm font-medium text-gray-900">vs {{ $opponent->name }}</p>
                                    <p class="text-xs text-gray-500">
                                        @if($game->canPlayerMakeMove($user))
                                        <span class="text-green-600">Your turn</span>
                                        @else
                                        <span class="text-yellow-600">Waiting for opponent</span>
                                        @endif
                                    </p>
                                </div>
                            </div>
                            <a href="{{ route('games.show', $game) }}" class="px-3 py-1 bg-blue-600 text-white text-xs font-medium rounded hover:bg-blue-700">
                                Play
                            </a>
                        </div>
                        @endforeach
                    </div>
                    @else
                    <p class="text-gray-500 text-sm">No active games. <a href="{{ route('games.create') }}" class="text-indigo-600 hover:text-indigo-500">Start a new game!</a></p>
                    @endif
                </div>
            </div>

            <!-- Recent Games -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h2 class="text-lg font-medium text-gray-900 mb-4">Recent Games</h2>
                    @if($recentGames->count() > 0)
                    <div class="space-y-3">
                        @foreach($recentGames as $game)
                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                            <div class="flex items-center">
                                @php
                                $opponent = $game->getOpponent($user);
                                @endphp
                                @if($opponent->avatar)
                                <img class="h-8 w-8 rounded-full object-cover" src="{{ Storage::url($opponent->avatar) }}" alt="{{ $opponent->name }}">
                                @else
                                <div class="h-8 w-8 rounded-full bg-indigo-500 flex items-center justify-center text-white text-sm">
                                    {{ strtoupper(substr($opponent->name, 0, 1)) }}
                                </div>
                                @endif
                                <div class="ml-3">
                                    <p class="text-sm font-medium text-gray-900">vs {{ $opponent->name }}</p>
                                    <p class="text-xs text-gray-500">{{ $game->completed_at ? $game->completed_at->diffForHumans() : $game->created_at->diffForHumans() }}</p>
                                </div>
                            </div>
                            <div class="text-right">
                                @if($game->status === 'completed')
                                @if($game->winner_id === $user->id)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">Won</span>
                                @elseif($game->result === 'draw')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">Draw</span>
                                @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">Lost</span>
                                @endif
                                @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">{{ ucfirst($game->status) }}</span>
                                @endif
                                <a href="{{ route('games.show', $game) }}" class="block text-xs text-indigo-600 hover:text-indigo-500 mt-1">View</a>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    @else
                    <p class="text-gray-500 text-sm">No games played yet. <a href="{{ route('games.create') }}" class="text-indigo-600 hover:text-indigo-500">Start your first game!</a></p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
