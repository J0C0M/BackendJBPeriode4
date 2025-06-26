@extends('layouts.app')

@section('title', 'Create New Game')

@section('content')
<div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="bg-white rounded-lg shadow-md p-8">
        <!-- Header -->
        <div class="text-center mb-8">
            <h1 class="text-3xl font-bold text-gray-900 mb-2">Create New Game</h1>
            <p class="text-gray-600">Challenge a friend to a Wordle game</p>
        </div>

        <form method="POST" action="{{ route('games.store') }}" class="space-y-6">
            @csrf
            
            <!-- Opponent Selection -->
            <div>
                <label for="opponent_id" class="block text-sm font-medium text-gray-700 mb-2">
                    Choose Opponent
                </label>
                
                <div class="space-y-3">
                    <!-- Friend Selection -->
                    <div class="border border-gray-300 rounded-md p-4">
                        <h3 class="text-sm font-medium text-gray-900 mb-3">Your Friends</h3>
                        
                        @if($friends->count() > 0)
                            <div class="space-y-2 max-h-48 overflow-y-auto">
                                @foreach($friends as $friend)
                                    <label class="flex items-center p-2 rounded-md hover:bg-gray-50 cursor-pointer">
                                        <input type="radio" 
                                               name="opponent_id" 
                                               value="{{ $friend->id }}" 
                                               class="h-4 w-4 text-green-600 focus:ring-green-500 border-gray-300"
                                               {{ old('opponent_id') == $friend->id ? 'checked' : '' }}>
                                        <div class="ml-3 flex items-center">
                                            @if($friend->avatar)
                                                <img class="h-8 w-8 rounded-full" src="{{ Storage::url($friend->avatar) }}" alt="{{ $friend->name }}">
                                            @else
                                                <div class="h-8 w-8 rounded-full bg-gray-500 flex items-center justify-center text-white font-semibold">
                                                    {{ substr($friend->name, 0, 1) }}
                                                </div>
                                            @endif
                                            <div class="ml-3">
                                                <div class="text-sm font-medium text-gray-900">{{ $friend->name }}</div>
                                                <div class="text-sm text-gray-500">@{{ $friend->username }}</div>
                                            </div>
                                        </div>
                                    </label>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-4">
                                <svg class="mx-auto h-8 w-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                                </svg>
                                <p class="mt-2 text-sm text-gray-500">You don't have any friends yet.</p>
                                <a href="{{ route('friends.index') }}" class="mt-2 inline-flex items-center text-sm text-green-600 hover:text-green-500">
                                    Add friends
                                    <svg class="ml-1 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                    </svg>
                                </a>
                            </div>
                        @endif
                    </div>
                    
                    <!-- Search for Users -->
                    <div class="border border-gray-300 rounded-md p-4">
                        <h3 class="text-sm font-medium text-gray-900 mb-3">Search for Players</h3>
                        
                        <div class="flex space-x-2">
                            <input type="text" 
                                   id="user-search" 
                                   placeholder="Search by username or name..."
                                   class="flex-1 rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 sm:text-sm">
                            <button type="button" 
                                    id="search-btn"
                                    class="inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                                Search
                            </button>
                        </div>
                        
                        <div id="search-results" class="mt-3 space-y-2 max-h-48 overflow-y-auto hidden">
                            <!-- Search results will be populated here -->
                        </div>
                    </div>
                </div>
                
                @error('opponent_id')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Game Settings -->
            <div class="border-t border-gray-200 pt-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Game Settings</h3>
                
                <div class="space-y-4">
                    <!-- Difficulty Level -->
                    <div>
                        <label for="difficulty" class="block text-sm font-medium text-gray-700 mb-2">
                            Difficulty Level
                        </label>
                        <select id="difficulty" 
                                name="difficulty" 
                                class="block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 sm:text-sm">
                            <option value="easy" {{ old('difficulty') == 'easy' ? 'selected' : '' }}>Easy</option>
                            <option value="medium" {{ old('difficulty') == 'medium' ? 'selected' : '' }}>Medium</option>
                            <option value="hard" {{ old('difficulty') == 'hard' ? 'selected' : '' }}>Hard</option>
                        </select>
                        <p class="mt-1 text-sm text-gray-500">
                            <span id="difficulty-description">Easy: Common 5-letter words</span>
                        </p>
                    </div>
                    
                    <!-- Max Attempts -->
                    <div>
                        <label for="max_attempts" class="block text-sm font-medium text-gray-700 mb-2">
                            Maximum Attempts
                        </label>
                        <select id="max_attempts" 
                                name="max_attempts" 
                                class="block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 sm:text-sm">
                            <option value="6" {{ old('max_attempts') == '6' ? 'selected' : '' }}>6 attempts (Classic)</option>
                            <option value="8" {{ old('max_attempts') == '8' ? 'selected' : '' }}>8 attempts (Extended)</option>
                            <option value="10" {{ old('max_attempts') == '10' ? 'selected' : '' }}>10 attempts (Easy Mode)</option>
                        </select>
                    </div>
                    
                    <!-- Game Mode -->
                    <div>
                        <label for="game_mode" class="block text-sm font-medium text-gray-700 mb-2">
                            Game Mode
                        </label>
                        <select id="game_mode" 
                                name="game_mode" 
                                class="block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 sm:text-sm">
                            <option value="turn_based" {{ old('game_mode') == 'turn_based' ? 'selected' : '' }}>Turn-based (Alternating turns)</option>
                            <option value="simultaneous" {{ old('game_mode') == 'simultaneous' ? 'selected' : '' }}>Simultaneous (Race to solve)</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Game Preview -->
            <div class="border-t border-gray-200 pt-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Game Preview</h3>
                
                <div class="bg-gray-50 rounded-lg p-4">
                    <div class="grid grid-cols-5 gap-2 mb-4">
                        @for($i = 0; $i < 5; $i++)
                            <div class="wordle-tile"></div>
                        @endfor
                    </div>
                    
                    <div class="text-sm text-gray-600">
                        <p><strong>Mode:</strong> <span id="preview-mode">Turn-based</span></p>
                        <p><strong>Difficulty:</strong> <span id="preview-difficulty">Easy</span></p>
                        <p><strong>Attempts:</strong> <span id="preview-attempts">6</span></p>
                    </div>
                </div>
            </div>

            <!-- Submit Button -->
            <div class="flex justify-end space-x-3 pt-6">
                <a href="{{ route('games.index') }}" 
                   class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                    Cancel
                </a>
                <button type="submit" 
                        class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                    Create Game
                </button>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const difficultySelect = document.getElementById('difficulty');
    const maxAttemptsSelect = document.getElementById('max_attempts');
    const gameModeSelect = document.getElementById('game_mode');
    const userSearch = document.getElementById('user-search');
    const searchBtn = document.getElementById('search-btn');
    const searchResults = document.getElementById('search-results');
    
    // Difficulty descriptions
    const difficultyDescriptions = {
        'easy': 'Easy: Common 5-letter words',
        'medium': 'Medium: Moderate difficulty words',
        'hard': 'Hard: Challenging and uncommon words'
    };
    
    // Update difficulty description
    difficultySelect.addEventListener('change', function() {
        const description = document.getElementById('difficulty-description');
        description.textContent = difficultyDescriptions[this.value];
        
        // Update preview
        document.getElementById('preview-difficulty').textContent = this.value.charAt(0).toUpperCase() + this.value.slice(1);
    });
    
    // Update preview when settings change
    maxAttemptsSelect.addEventListener('change', function() {
        document.getElementById('preview-attempts').textContent = this.value;
    });
    
    gameModeSelect.addEventListener('change', function() {
        const modeText = this.value === 'turn_based' ? 'Turn-based' : 'Simultaneous';
        document.getElementById('preview-mode').textContent = modeText;
    });
    
    // User search functionality
    searchBtn.addEventListener('click', function() {
        const query = userSearch.value.trim();
        if (query.length < 2) {
            alert('Please enter at least 2 characters to search');
            return;
        }
        
        // Show loading
        searchResults.innerHTML = '<div class="text-center py-4"><div class="animate-spin rounded-full h-6 w-6 border-b-2 border-green-600 mx-auto"></div></div>';
        searchResults.classList.remove('hidden');
        
        // Search for users
        fetch(`/users/search?q=${encodeURIComponent(query)}`)
            .then(response => response.json())
            .then(data => {
                if (data.success && data.users.length > 0) {
                    searchResults.innerHTML = data.users.map(user => `
                        <label class="flex items-center p-2 rounded-md hover:bg-gray-50 cursor-pointer">
                            <input type="radio" 
                                   name="opponent_id" 
                                   value="${user.id}" 
                                   class="h-4 w-4 text-green-600 focus:ring-green-500 border-gray-300">
                            <div class="ml-3 flex items-center">
                                <div class="h-8 w-8 rounded-full bg-gray-500 flex items-center justify-center text-white font-semibold">
                                    ${user.name.charAt(0)}
                                </div>
                                <div class="ml-3">
                                    <div class="text-sm font-medium text-gray-900">${user.name}</div>
                                    <div class="text-sm text-gray-500">@${user.username}</div>
                                </div>
                            </div>
                        </label>
                    `).join('');
                } else {
                    searchResults.innerHTML = '<div class="text-center py-4 text-sm text-gray-500">No users found</div>';
                }
            })
            .catch(error => {
                console.error('Error searching users:', error);
                searchResults.innerHTML = '<div class="text-center py-4 text-sm text-red-500">Error searching users</div>';
            });
    });
    
    // Search on Enter key
    userSearch.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            searchBtn.click();
        }
    });
    
    // Form validation
    const form = document.querySelector('form');
    form.addEventListener('submit', function(e) {
        const opponentId = document.querySelector('input[name="opponent_id"]:checked');
        if (!opponentId) {
            e.preventDefault();
            alert('Please select an opponent');
            return;
        }
    });
});
</script>
@endsection 