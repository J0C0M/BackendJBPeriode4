@extends('layouts.app')

@section('title', 'Create New Game')

@section('content')
    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <h1 class="text-2xl font-bold text-gray-900 mb-6">Create New Game</h1>

                    <form action="{{ route('games.store') }}" method="POST" class="space-y-6">
                        @csrf

                        <!-- Game Type -->
                        <div>
                            <label class="text-base font-medium text-gray-900">Game Type</label>
                            <p class="text-sm leading-5 text-gray-500">Choose how you want to find an opponent</p>
                            <fieldset class="mt-4">
                                <legend class="sr-only">Game type</legend>
                                <div class="space-y-4">
                                    <div class="flex items-center">
                                        <input id="random" name="game_type" type="radio" value="random" class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300" {{ old('game_type', 'random') == 'random' ? 'checked' : '' }}>
                                        <label for="random" class="ml-3 block text-sm font-medium text-gray-700">
                                            Random Opponent
                                        </label>
                                    </div>
                                    <div class="text-sm text-gray-500 ml-7">
                                        Get matched with a random player online
                                    </div>

                                    <div class="flex items-center">
                                        <input id="friend" name="game_type" type="radio" value="friend" class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300" {{ old('game_type') == 'friend' ? 'checked' : '' }}>
                                        <label for="friend" class="ml-3 block text-sm font-medium text-gray-700">
                                            Play with Friend
                                        </label>
                                    </div>
                                    <div class="text-sm text-gray-500 ml-7">
                                        Choose a specific friend to play against
                                    </div>
                                </div>
                            </fieldset>
                            @error('game_type')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Friend Selection -->
                        <div id="friend-selection" class="hidden">
                            <label for="opponent_id" class="block text-sm font-medium text-gray-700">Select Friend</label>
                            <select id="opponent_id" name="opponent_id" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm @error('opponent_id') border-red-300 @enderror">
                                <option value="">Choose a friend...</option>
                                @foreach($friends as $friend)
                                    <option value="{{ $friend->id }}" {{ old('opponent_id') == $friend->id ? 'selected' : '' }}>
                                        {{ $friend->name }} ({{ '@' . $friend->username }})
                                    </option>
                                @endforeach
                            </select>
                            @if($friends->count() === 0)
                                <p class="mt-2 text-sm text-gray-500">
                                    You don't have any friends yet.
                                    <a href="{{ route('friends.create') }}" class="text-indigo-600 hover:text-indigo-500">Add some friends</a>
                                    to play with them.
                                </p>
                            @endif
                            @error('opponent_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Game Settings -->
                        <div>
                            <label for="max_attempts" class="block text-sm font-medium text-gray-700">Maximum Attempts</label>
                            <select id="max_attempts" name="max_attempts" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                <option value="6" {{ old('max_attempts', 6) == 6 ? 'selected' : '' }}>6 attempts (Classic)</option>
                                <option value="4" {{ old('max_attempts') == 4 ? 'selected' : '' }}>4 attempts (Hard)</option>
                                <option value="8" {{ old('max_attempts') == 8 ? 'selected' : '' }}>8 attempts (Easy)</option>
                                <option value="10" {{ old('max_attempts') == 10 ? 'selected' : '' }}>10 attempts (Very Easy)</option>
                            </select>
                            <p class="mt-1 text-sm text-gray-500">Choose how many attempts each player gets to guess the word</p>
                            @error('max_attempts')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Submit Buttons -->
                        <div class="flex justify-end space-x-3">
                            <a href="{{ route('games.index') }}" class="px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                Cancel
                            </a>
                            <button type="submit" class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                Create Game
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const randomRadio = document.getElementById('random');
            const friendRadio = document.getElementById('friend');
            const friendSelection = document.getElementById('friend-selection');

            function toggleFriendSelection() {
                if (friendRadio.checked) {
                    friendSelection.classList.remove('hidden');
                } else {
                    friendSelection.classList.add('hidden');
                }
            }

            randomRadio.addEventListener('change', toggleFriendSelection);
            friendRadio.addEventListener('change', toggleFriendSelection);

            // Initial state
            toggleFriendSelection();
        });
    </script>
@endsection<?php
