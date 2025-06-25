@extends('layouts.app')

@section('title', 'Game vs ' . $game->getOpponent(auth()->user())->name)

@section('content')
    <div class="py-12">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">
            <!-- Game Header -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 bg-white border-b border-gray-200">
                    <div class="flex justify-between items-center">
                        <div>
                            <h1 class="text-2xl font-bold text-gray-900">
                                Game vs {{ $game->getOpponent(auth()->user())->name }}
                            </h1>
                            <p class="text-sm text-gray-500 mt-1">
                                Created {{ $game->created_at->diffForHumans() }}
                                @if($game->completed_at)
                                    • Completed {{ $game->completed_at->diffForHumans() }}
                                @endif
                            </p>
                        </div>
                        <div class="text-right">
                            @if($game->status === 'pending')
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-yellow-100 text-yellow-800">
                                Waiting for opponent
                            </span>
                            @elseif($game->status === 'in_progress')
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800">
                                In Progress
                            </span>
                            @elseif($game->status === 'completed')
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                                Completed
                            </span>
                            @endif

                            @if($game->status === 'completed')
                                <div class="mt-2">
                                    @if($game->winner_id === auth()->id())
                                        <p class="text-green-600 font-semibold">You Won! 🎉</p>
                                    @elseif($game->result === 'draw')
                                        <p class="text-yellow-600 font-semibold">It's a Draw! 🤝</p>
                                    @else
                                        <p class="text-red-600 font-semibold">You Lost 😔</p>
                                    @endif
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Player 1 Board -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center justify-between mb-4">
                            <div class="flex items-center">
                                @if($game->player1->avatar)
                                    <img class="h-10 w-10 rounded-full object-cover" src="{{ Storage::url($game->player1->avatar) }}" alt="{{ $game->player1->name }}">
                                @else
                                    <div class="h-10 w-10 rounded-full bg-indigo-500 flex items-center justify-center text-white">
                                        {{ strtoupper(substr($game->player1->name, 0, 1)) }}
                                    </div>
                                @endif
                                <div class="ml-3">
                                    <h3 class="text-lg font-medium text-gray-900">{{ $game->player1->name }}</h3>
                                    <p class="text-sm text-gray-500">{{ $game->getPlayerAttempts($game->player1) }}/{{ $game->max_attempts }} attempts</p>
                                </div>
                            </div>
                            @if($currentPlayer && $currentPlayer->id === $game->player1->id)
                                <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium bg-green-100 text-green-800">
                                Current Turn
                            </span>
                            @endif
                        </div>

                        <!-- Player 1 Wordle Board -->
                        <div class="wordle-board" data-player="player1">
                            @for($row = 0; $row < $game->max_attempts; $row++)
                                <div class="grid grid-cols-5 gap-1 mb-1">
                                    @for($col = 0; $col < 5; $col++)
                                        @php
                                            $move = $player1Moves->where('attempt_number', $row + 1)->first();
                                            $letter = '';
                                            $status = '';

                                            if ($move && isset($move->guessed_word[$col])) {
                                                $letter = $move->guessed_word[$col];
                                                $status = $move->result[$col] ?? '';
                                            }
                                        @endphp
                                        <div class="w-12 h-12 border-2 border-gray-300 flex items-center justify-center font-bold text-lg
                                        @if($status === 'correct') bg-green-500 text-white border-green-500
                                        @elseif($status === 'wrong_position') bg-yellow-500 text-white border-yellow-500
                                        @elseif($status === 'incorrect') bg-gray-500 text-white border-gray-500
                                        @endif">
                                            {{ $letter }}
                                        </div>
                                    @endfor
                                </div>
                            @endfor
                        </div>
                    </div>
                </div>

                <!-- Player 2 Board -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center justify-between mb-4">
                            <div class="flex items-center">
                                @if($game->player2->avatar)
                                    <img class="h-10 w-10 rounded-full object-cover" src="{{ Storage::url($game->player2->avatar) }}" alt="{{ $game->player2->name }}">
                                @else
                                    <div class="h-10 w-10 rounded-full bg-indigo-500 flex items-center justify-center text-white">
                                        {{ strtoupper(substr($game->player2->name, 0, 1)) }}
                                    </div>
                                @endif
                                <div class="ml-3">
                                    <h3 class="text-lg font-medium text-gray-900">{{ $game->player2->name }}</h3>
                                    <p class="text-sm text-gray-500">{{ $game->getPlayerAttempts($game->player2) }}/{{ $game->max_attempts }} attempts</p>
                                </div>
                            </div>
                            @if($currentPlayer && $currentPlayer->id === $game->player2->id)
                                <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium bg-green-100 text-green-800">
                                Current Turn
                            </span>
                            @endif
                        </div>

                        <!-- Player 2 Wordle Board -->
                        <div class="wordle-board" data-player="player2">
                            @for($row = 0; $row < $game->max_attempts; $row++)
                                <div class="grid grid-cols-5 gap-1 mb-1">
                                    @for($col = 0; $col < 5; $col++)
                                        @php
                                            $move = $player2Moves->where('attempt_number', $row + 1)->first();
                                            $letter = '';
                                            $status = '';

                                            if ($move && isset($move->guessed_word[$col])) {
                                                $letter = $move->guessed_word[$col];
                                                $status = $move->result[$col] ?? '';
                                            }
                                        @endphp
                                        <div class="w-12 h-12 border-2 border-gray-300 flex items-center justify-center font-bold text-lg
                                        @if($status === 'correct') bg-green-500 text-white border-green-500
                                        @elseif($status === 'wrong_position') bg-yellow-500 text-white border-yellow-500
                                        @elseif($status === 'incorrect') bg-gray-500 text-white border-gray-500
                                        @endif">
                                            {{ $letter }}
                                        </div>
                                    @endfor
                                </div>
                            @endfor
                        </div>
                    </div>
                </div>
            </div>

            <!-- Game Input (only show if it's the user's turn) -->
            @if($game->canPlayerMakeMove(auth()->user()) && $game->status === 'in_progress')
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mt-6">
                    <div class="p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Your Turn</h3>
                        <form id="guess-form" class="flex space-x-4">
                            @csrf
                            <input type="text"
                                   id="guess-input"
                                   placeholder="Enter 5-letter word"
                                   maxlength="5"
                                   class="flex-1 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 uppercase text-center text-lg font-bold"
                                   pattern="[A-Za-z]{5}"
                                   required>
                            <button type="submit"
                                    id="submit-guess"
                                    class="px-6 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 disabled:opacity-50 disabled:cursor-not-allowed">
                                Submit Guess
                            </button>
                        </form>
                        <p class="mt-2 text-sm text-gray-500">Enter a valid 5-letter word and press submit</p>
                        <div id="guess-error" class="mt-2 text-sm text-red-600 hidden"></div>
                    </div>
                </div>
            @elseif($game->status === 'pending')
                <div class="bg-yellow-50 border border-yellow-200 rounded-md p-4 mt-6">
                    <div class="flex">
                        <svg class="h-5 w-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                        </svg>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-yellow-800">Game Pending</h3>
                            <div class="mt-2 text-sm text-yellow-700">
                                @if($game->player2_id === auth()->id())
                                    <p>You have been invited to play this game.</p>
                                    <div class="mt-3 flex space-x-3">
                                        <form action="{{ route('games.accept', $game) }}" method="POST" class="inline">
                                            @csrf
                                            <button type="submit" class="px-3 py-1 bg-green-600 text-white text-sm font-medium rounded hover:bg-green-700">
                                                Accept
                                            </button>
                                        </form>
                                        <form action="{{ route('games.decline', $game) }}" method="POST" class="inline">
                                            @csrf
                                            <button type="submit" class="px-3 py-1 bg-red-600 text-white text-sm font-medium rounded hover:bg-red-700">
                                                Decline
                                            </button>
                                        </form>
                                    </div>
                                @else
                                    <p>Waiting for {{ $game->player2->name }} to accept the game invitation.</p>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @elseif($game->status === 'in_progress')
                <div class="bg-blue-50 border border-blue-200 rounded-md p-4 mt-6">
                    <div class="flex">
                        <svg class="h-5 w-5 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                        </svg>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-blue-800">Waiting for opponent</h3>
                            <p class="mt-1 text-sm text-blue-700">It's {{ $currentPlayer->name }}'s turn to make a move.</p>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Game Result -->
            @if($game->status === 'completed')
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mt-6">
                    <div class="p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Game Result</h3>
                        <div class="bg-gray-50 rounded-lg p-4">
                            <div class="text-center">
                                <p class="text-2xl font-bold mb-2">The word was:</p>
                                <p class="text-4xl font-bold font-mono text-indigo-600">{{ $game->word->word }}</p>
                                @if($game->result === 'draw')
                                    <p class="text-lg text-gray-600 mt-4">Both players used all their attempts - it's a draw!</p>
                                @elseif($game->winner)
                                    <p class="text-lg text-gray-600 mt-4">
                                        {{ $game->winner->name }} won the game!
                                    </p>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>

    @if($game->canPlayerMakeMove(auth()->user()) && $game->status === 'in_progress')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const form = document.getElementById('guess-form');
                const input = document.getElementById('guess-input');
                const submitBtn = document.getElementById('submit-guess');
                const errorDiv = document.getElementById('guess-error');

                // Auto-uppercase input
                input.addEventListener('input', function() {
                    this.value = this.value.toUpperCase();
                });

                // Form submission
                form.addEventListener('submit', function(e) {
                    e.preventDefault();

                    const guess = input.value.trim();

                    if (guess.length !== 5) {
                        showError('Please enter exactly 5 letters');
                        return;
                    }

                    if (!/^[A-Z]+$/.test(guess)) {
                        showError('Please enter only letters');
                        return;
                    }

                    submitGuess(guess);
                });

                function showError(message) {
                    errorDiv.textContent = message;
                    errorDiv.classList.remove('hidden');
                    setTimeout(() => {
                        errorDiv.classList.add('hidden');
                    }, 3000);
                }

                function submitGuess(guess) {
                    submitBtn.disabled = true;
                    submitBtn.textContent = 'Submitting...';

                    fetch(`{{ route('games.moves.store', $game) }}`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('[name="_token"]').value
                        },
                        body: JSON.stringify({
                            guessed_word: guess
                        })
                    })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                // Reload the page to show the updated game state
                                window.location.reload();
                            } else {
                                showError(data.message || 'Failed to submit guess. Please try again.');
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            showError('Network error. Please try again.');
                        })
                        .finally(() => {
                            submitBtn.disabled = false;
                            submitBtn.textContent = 'Submit Guess';
                        });
                }
            });
        </script>
    @endif
@endsection<?php
