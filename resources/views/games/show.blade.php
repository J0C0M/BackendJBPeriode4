@extends('layouts.app')

@section('title', 'Game')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Game Header -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-8">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 mb-2">Wordle Game</h1>
                <div class="flex items-center space-x-4 text-sm text-gray-600">
                    <div class="flex items-center">
                        @if($game->player1->avatar)
                            <img class="h-6 w-6 rounded-full mr-2" src="{{ Storage::url($game->player1->avatar) }}" alt="{{ $game->player1->name }}">
                        @else
                            <div class="h-6 w-6 rounded-full bg-green-500 flex items-center justify-center text-white text-xs font-semibold mr-2">
                                {{ substr($game->player1->name, 0, 1) }}
                            </div>
                        @endif
                        <span>{{ $game->player1->name }}</span>
                    </div>
                    <span>vs</span>
                    <div class="flex items-center">
                        @if($game->player2->avatar)
                            <img class="h-6 w-6 rounded-full mr-2" src="{{ Storage::url($game->player2->avatar) }}" alt="{{ $game->player2->name }}">
                        @else
                            <div class="h-6 w-6 rounded-full bg-blue-500 flex items-center justify-center text-white text-xs font-semibold mr-2">
                                {{ substr($game->player2->name, 0, 1) }}
                            </div>
                        @endif
                        <span>{{ $game->player2->name }}</span>
                    </div>
                </div>
            </div>
            
            <div class="mt-4 md:mt-0 flex items-center space-x-4">
                <div class="text-right">
                    <div class="text-sm text-gray-500">Status</div>
                    <div class="text-sm font-medium">
                        @if($game->status === 'pending')
                            <span class="text-yellow-600">Pending</span>
                        @elseif($game->status === 'in_progress')
                            <span class="text-green-600">In Progress</span>
                        @elseif($game->status === 'completed')
                            <span class="text-blue-600">Completed</span>
                        @else
                            <span class="text-red-600">Cancelled</span>
                        @endif
                    </div>
                </div>
                
                <div class="text-right">
                    <div class="text-sm text-gray-500">Created</div>
                    <div class="text-sm font-medium">{{ $game->created_at->format('M j, Y') }}</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Game Board -->
    <div class="bg-white rounded-lg shadow-md p-8 mb-8">
        @include('components.game-board')
    </div>

    <!-- Virtual Keyboard (Mobile) -->
    <div class="md:hidden mb-8">
        @include('components.keyboard')
    </div>

    <!-- Game Details -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- Player Stats -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Player Statistics</h3>
            
            <div class="space-y-4">
                <!-- Player 1 Stats -->
                <div class="border-b border-gray-200 pb-4">
                    <div class="flex items-center justify-between mb-2">
                        <div class="flex items-center">
                            @if($game->player1->avatar)
                                <img class="h-8 w-8 rounded-full mr-3" src="{{ Storage::url($game->player1->avatar) }}" alt="{{ $game->player1->name }}">
                            @else
                                <div class="h-8 w-8 rounded-full bg-green-500 flex items-center justify-center text-white font-semibold mr-3">
                                    {{ substr($game->player1->name, 0, 1) }}
                                </div>
                            @endif
                            <div>
                                <div class="font-medium text-gray-900">{{ $game->player1->name }}</div>
                                <div class="text-sm text-gray-500">@{{ $game->player1->username }}</div>
                            </div>
                        </div>
                        @if($game->status === 'in_progress' && $game->current_player_id === $game->player1->id)
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                Current Turn
                            </span>
                        @endif
                    </div>
                    
                    <div class="grid grid-cols-3 gap-4 text-sm">
                        <div>
                            <div class="text-gray-500">Attempts</div>
                            <div class="font-medium">{{ $game->getPlayerAttempts($game->player1) }}/{{ $game->max_attempts }}</div>
                        </div>
                        <div>
                            <div class="text-gray-500">Games Won</div>
                            <div class="font-medium">{{ $game->player1->games_won }}</div>
                        </div>
                        <div>
                            <div class="text-gray-500">Win Rate</div>
                            <div class="font-medium">{{ $game->player1->win_rate }}%</div>
                        </div>
                    </div>
                </div>
                
                <!-- Player 2 Stats -->
                <div>
                    <div class="flex items-center justify-between mb-2">
                        <div class="flex items-center">
                            @if($game->player2->avatar)
                                <img class="h-8 w-8 rounded-full mr-3" src="{{ Storage::url($game->player2->avatar) }}" alt="{{ $game->player2->name }}">
                            @else
                                <div class="h-8 w-8 rounded-full bg-blue-500 flex items-center justify-center text-white font-semibold mr-3">
                                    {{ substr($game->player2->name, 0, 1) }}
                                </div>
                            @endif
                            <div>
                                <div class="font-medium text-gray-900">{{ $game->player2->name }}</div>
                                <div class="text-sm text-gray-500">@{{ $game->player2->username }}</div>
                            </div>
                        </div>
                        @if($game->status === 'in_progress' && $game->current_player_id === $game->player2->id)
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                Current Turn
                            </span>
                        @endif
                    </div>
                    
                    <div class="grid grid-cols-3 gap-4 text-sm">
                        <div>
                            <div class="text-gray-500">Attempts</div>
                            <div class="font-medium">{{ $game->getPlayerAttempts($game->player2) }}/{{ $game->max_attempts }}</div>
                        </div>
                        <div>
                            <div class="text-gray-500">Games Won</div>
                            <div class="font-medium">{{ $game->player2->games_won }}</div>
                        </div>
                        <div>
                            <div class="text-gray-500">Win Rate</div>
                            <div class="font-medium">{{ $game->player2->win_rate }}%</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Game Moves History -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Move History</h3>
            
            @if($game->moves->count() > 0)
                <div class="space-y-3 max-h-64 overflow-y-auto">
                    @foreach($game->moves as $move)
                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                            <div class="flex items-center">
                                <div class="flex items-center mr-3">
                                    @if($move->player->avatar)
                                        <img class="h-6 w-6 rounded-full" src="{{ Storage::url($move->player->avatar) }}" alt="{{ $move->player->name }}">
                                    @else
                                        <div class="h-6 w-6 rounded-full bg-gray-500 flex items-center justify-center text-white text-xs font-semibold">
                                            {{ substr($move->player->name, 0, 1) }}
                                        </div>
                                    @endif
                                </div>
                                <div>
                                    <div class="text-sm font-medium text-gray-900">{{ $move->player->name }}</div>
                                    <div class="text-sm text-gray-500">Attempt {{ $move->attempt_number }}</div>
                                </div>
                            </div>
                            <div class="text-right">
                                <div class="text-sm font-medium text-gray-900">{{ strtoupper($move->guessed_word) }}</div>
                                <div class="text-xs text-gray-500">{{ $move->created_at->format('g:i A') }}</div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-8">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">No moves yet</h3>
                    <p class="mt-1 text-sm text-gray-500">Make the first move to start the game!</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Comments Section -->
    @if($game->status === 'completed')
        <div class="mt-8 bg-white rounded-lg shadow-md p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Comments</h3>
            
            <!-- Comment Form -->
            <form method="POST" action="{{ route('comments.store') }}" class="mb-6">
                @csrf
                <input type="hidden" name="commentable_type" value="App\Models\Game">
                <input type="hidden" name="commentable_id" value="{{ $game->id }}">
                
                <div class="flex space-x-3">
                    <div class="flex-shrink-0">
                        @if(auth()->user()->avatar)
                            <img class="h-8 w-8 rounded-full" src="{{ Storage::url(auth()->user()->avatar) }}" alt="{{ auth()->user()->name }}">
                        @else
                            <div class="h-8 w-8 rounded-full bg-green-500 flex items-center justify-center text-white font-semibold">
                                {{ substr(auth()->user()->name, 0, 1) }}
                            </div>
                        @endif
                    </div>
                    <div class="flex-1">
                        <textarea name="content" 
                                  rows="3" 
                                  class="block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 sm:text-sm"
                                  placeholder="Add a comment about this game..."></textarea>
                        <div class="mt-2 flex justify-end">
                            <button type="submit" 
                                    class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                                Post Comment
                            </button>
                        </div>
                    </div>
                </div>
            </form>
            
            <!-- Comments List -->
            <div class="space-y-4">
                @foreach($game->comments as $comment)
                    <div class="flex space-x-3">
                        <div class="flex-shrink-0">
                            @if($comment->user->avatar)
                                <img class="h-8 w-8 rounded-full" src="{{ Storage::url($comment->user->avatar) }}" alt="{{ $comment->user->name }}">
                            @else
                                <div class="h-8 w-8 rounded-full bg-gray-500 flex items-center justify-center text-white font-semibold">
                                    {{ substr($comment->user->name, 0, 1) }}
                                </div>
                            @endif
                        </div>
                        <div class="flex-1">
                            <div class="bg-gray-50 rounded-lg px-4 py-2">
                                <div class="flex items-center justify-between">
                                    <div class="text-sm font-medium text-gray-900">{{ $comment->user->name }}</div>
                                    <div class="text-sm text-gray-500">{{ $comment->created_at->diffForHumans() }}</div>
                                </div>
                                <div class="mt-1 text-sm text-gray-700">{{ $comment->content }}</div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif
</div>

<script>
// Auto-refresh game state every 5 seconds if game is in progress
@if($game->status === 'in_progress')
setInterval(function() {
    fetch(`/api/games/{{ $game->id }}/state`)
        .then(response => response.json())
        .then(data => {
            if (data.success && data.game.status !== '{{ $game->status }}') {
                window.location.reload();
            }
        })
        .catch(error => console.error('Error fetching game state:', error));
}, 5000);
@endif
</script>
@endsection 