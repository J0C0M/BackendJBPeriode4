@extends('layouts.app')

@section('title', 'Games')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Games</h1>
            <p class="text-gray-600 mt-2">Manage your Wordle games and challenges</p>
        </div>
        <a href="{{ route('games.create') }}" 
           class="mt-4 sm:mt-0 inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700 transition-colors">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
            </svg>
            New Game
        </a>
    </div>

    <!-- Game Stats -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center">
                        <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                </div>
                <div class="ml-4">
                    <div class="text-2xl font-bold text-gray-900">{{ $stats['total_wins'] }}</div>
                    <div class="text-sm text-gray-500">Games Won</div>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-red-100 rounded-full flex items-center justify-center">
                        <svg class="w-4 h-4 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </div>
                </div>
                <div class="ml-4">
                    <div class="text-2xl font-bold text-gray-900">{{ $stats['total_losses'] }}</div>
                    <div class="text-sm text-gray-500">Games Lost</div>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
                        <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                </div>
                <div class="ml-4">
                    <div class="text-2xl font-bold text-gray-900">{{ $stats['active_games'] }}</div>
                    <div class="text-sm text-gray-500">Active Games</div>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-yellow-100 rounded-full flex items-center justify-center">
                        <svg class="w-4 h-4 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                </div>
                <div class="ml-4">
                    <div class="text-2xl font-bold text-gray-900">{{ $stats['pending_invitations'] }}</div>
                    <div class="text-sm text-gray-500">Pending Invites</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabs -->
    <div class="border-b border-gray-200 mb-8">
        <nav class="-mb-px flex space-x-8">
            <button class="tab-button active py-2 px-1 border-b-2 border-green-500 font-medium text-sm text-green-600" data-tab="active">
                Active Games
            </button>
            <button class="tab-button py-2 px-1 border-b-2 border-transparent font-medium text-sm text-gray-500 hover:text-gray-700" data-tab="pending">
                Pending Invitations
            </button>
            <button class="tab-button py-2 px-1 border-b-2 border-transparent font-medium text-sm text-gray-500 hover:text-gray-700" data-tab="history">
                Game History
            </button>
        </nav>
    </div>

    <!-- Active Games Tab -->
    <div id="active-tab" class="tab-content">
        @if($activeGames->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($activeGames as $game)
                    <div class="bg-white rounded-lg shadow-md overflow-hidden">
                        <div class="p-6">
                            <div class="flex items-center justify-between mb-4">
                                <div class="flex items-center">
                                    @if($game->player1->avatar)
                                        <img class="h-10 w-10 rounded-full" src="{{ Storage::url($game->player1->avatar) }}" alt="{{ $game->player1->name }}">
                                    @else
                                        <div class="h-10 w-10 rounded-full bg-green-500 flex items-center justify-center text-white font-semibold">
                                            {{ substr($game->player1->name, 0, 1) }}
                                        </div>
                                    @endif
                                    <div class="ml-3">
                                        <div class="text-sm font-medium text-gray-900">{{ $game->player1->name }}</div>
                                        <div class="text-sm text-gray-500">vs {{ $game->player2->name }}</div>
                                    </div>
                                </div>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    Active
                                </span>
                            </div>
                            
                            <div class="mb-4">
                                <div class="text-sm text-gray-500">Created {{ $game->created_at->diffForHumans() }}</div>
                                <div class="text-sm text-gray-500">Attempts: {{ $game->getPlayerAttempts(auth()->user()) }}/{{ $game->max_attempts }}</div>
                            </div>
                            
                            <div class="flex space-x-2">
                                <a href="{{ route('games.show', $game) }}" 
                                   class="flex-1 bg-green-600 text-white text-center py-2 px-4 rounded-md hover:bg-green-700 transition-colors text-sm font-medium">
                                    Continue Game
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="text-center py-12">
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

    <!-- Pending Invitations Tab -->
    <div id="pending-tab" class="tab-content hidden">
        @if($pendingGames->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($pendingGames as $game)
                    <div class="bg-white rounded-lg shadow-md overflow-hidden">
                        <div class="p-6">
                            <div class="flex items-center justify-between mb-4">
                                <div class="flex items-center">
                                    @if($game->player1->avatar)
                                        <img class="h-10 w-10 rounded-full" src="{{ Storage::url($game->player1->avatar) }}" alt="{{ $game->player1->name }}">
                                    @else
                                        <div class="h-10 w-10 rounded-full bg-blue-500 flex items-center justify-center text-white font-semibold">
                                            {{ substr($game->player1->name, 0, 1) }}
                                        </div>
                                    @endif
                                    <div class="ml-3">
                                        <div class="text-sm font-medium text-gray-900">{{ $game->player1->name }}</div>
                                        <div class="text-sm text-gray-500">wants to play</div>
                                    </div>
                                </div>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                    Pending
                                </span>
                            </div>
                            
                            <div class="mb-4">
                                <div class="text-sm text-gray-500">Invited {{ $game->created_at->diffForHumans() }}</div>
                            </div>
                            
                            <div class="flex space-x-2">
                                <form method="POST" action="{{ route('games.accept', $game) }}" class="flex-1">
                                    @csrf
                                    <button type="submit" class="w-full bg-green-600 text-white py-2 px-4 rounded-md hover:bg-green-700 transition-colors text-sm font-medium">
                                        Accept
                                    </button>
                                </form>
                                <form method="POST" action="{{ route('games.decline', $game) }}" class="flex-1">
                                    @csrf
                                    <button type="submit" class="w-full bg-red-600 text-white py-2 px-4 rounded-md hover:bg-red-700 transition-colors text-sm font-medium">
                                        Decline
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="text-center py-12">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900">No pending invitations</h3>
                <p class="mt-1 text-sm text-gray-500">You don't have any game invitations waiting.</p>
            </div>
        @endif
    </div>

    <!-- Game History Tab -->
    <div id="history-tab" class="tab-content hidden">
        @if($gameHistory->count() > 0)
            <div class="bg-white shadow overflow-hidden sm:rounded-md">
                <ul class="divide-y divide-gray-200">
                    @foreach($gameHistory as $game)
                        <li>
                            <div class="px-4 py-4 sm:px-6">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0">
                                            @if($game->winner_id === auth()->id())
                                                <div class="h-8 w-8 bg-green-100 rounded-full flex items-center justify-center">
                                                    <svg class="h-4 w-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                    </svg>
                                                </div>
                                            @elseif($game->winner_id)
                                                <div class="h-8 w-8 bg-red-100 rounded-full flex items-center justify-center">
                                                    <svg class="h-4 w-4 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                                    </svg>
                                                </div>
                                            @else
                                                <div class="h-8 w-8 bg-gray-100 rounded-full flex items-center justify-center">
                                                    <svg class="h-4 w-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                    </svg>
                                                </div>
                                            @endif
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-gray-900">
                                                {{ $game->player1->name }} vs {{ $game->player2->name }}
                                            </div>
                                            <div class="text-sm text-gray-500">
                                                @if($game->winner_id === auth()->id())
                                                    You won!
                                                @elseif($game->winner_id)
                                                    {{ $game->winner->name }} won
                                                @else
                                                    Draw
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <div class="text-sm text-gray-900">{{ $game->created_at->format('M j, Y') }}</div>
                                        <div class="text-sm text-gray-500">{{ $game->created_at->format('g:i A') }}</div>
                                    </div>
                                </div>
                            </div>
                        </li>
                    @endforeach
                </ul>
            </div>
            
            <div class="mt-6">
                {{ $gameHistory->links() }}
            </div>
        @else
            <div class="text-center py-12">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900">No game history</h3>
                <p class="mt-1 text-sm text-gray-500">Start playing to see your game history here.</p>
            </div>
        @endif
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const tabButtons = document.querySelectorAll('.tab-button');
    const tabContents = document.querySelectorAll('.tab-content');
    
    tabButtons.forEach(button => {
        button.addEventListener('click', function() {
            const targetTab = this.dataset.tab;
            
            // Remove active class from all buttons and contents
            tabButtons.forEach(btn => {
                btn.classList.remove('active', 'border-green-500', 'text-green-600');
                btn.classList.add('border-transparent', 'text-gray-500');
            });
            
            tabContents.forEach(content => {
                content.classList.add('hidden');
            });
            
            // Add active class to clicked button
            this.classList.add('active', 'border-green-500', 'text-green-600');
            this.classList.remove('border-transparent', 'text-gray-500');
            
            // Show target content
            document.getElementById(targetTab + '-tab').classList.remove('hidden');
        });
    });
});
</script>
@endsection 