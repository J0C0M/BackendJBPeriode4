@extends('layouts.app')

@section('title', 'Add Word')

@section('content')
<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
            <div class="flex items-center">
                <a href="{{ route('admin.words.index') }}" class="text-gray-400 hover:text-gray-600 mr-4">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                    </svg>
                </a>
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Add Word</h1>
                    <p class="mt-2 text-gray-600">Add a new word to the Wordle game</p>
                </div>
            </div>
        </div>

        <!-- Add Word Form -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-medium text-gray-900">Word Information</h2>
            </div>
            
            <form action="{{ route('admin.words.store') }}" method="POST" class="p-6 space-y-6">
                @csrf
                
                <!-- Word -->
                <div>
                    <label for="word" class="block text-sm font-medium text-gray-700">Word *</label>
                    <input type="text" id="word" name="word" value="{{ old('word') }}" required
                           placeholder="Enter the word (e.g., HELLO)" 
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-center text-lg font-mono uppercase tracking-wider">
                    @error('word')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    <p class="mt-1 text-xs text-gray-500">Enter the word in any case - it will be converted to uppercase</p>
                </div>

                <!-- Definition -->
                <div>
                    <label for="definition" class="block text-sm font-medium text-gray-700">Definition</label>
                    <textarea id="definition" name="definition" rows="3" 
                              placeholder="Enter the definition of the word (optional)"
                              class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">{{ old('definition') }}</textarea>
                    @error('definition')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Difficulty Score -->
                <div>
                    <label for="difficulty_score" class="block text-sm font-medium text-gray-700">Difficulty Score</label>
                    <div class="mt-1">
                        <div class="flex items-center space-x-4">
                            <input type="range" id="difficulty_score" name="difficulty_score" 
                                   min="1" max="5" value="{{ old('difficulty_score', 3) }}"
                                   class="flex-1 h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer slider">
                            <span id="difficulty-display" class="text-lg font-semibold text-gray-900 w-8 text-center">{{ old('difficulty_score', 3) }}/5</span>
                        </div>
                        <div class="flex justify-between text-xs text-gray-500 mt-1">
                            <span>Easy</span>
                            <span>Hard</span>
                        </div>
                    </div>
                    @error('difficulty_score')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Status -->
                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700">Status *</label>
                    <select id="status" name="status" required
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="">Select Status</option>
                        <option value="active" {{ old('status') == 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                    </select>
                    @error('status')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Categories -->
                <div>
                    <label for="categories" class="block text-sm font-medium text-gray-700">Categories</label>
                    <input type="text" id="categories" name="categories" value="{{ old('categories') }}" 
                           placeholder="Enter categories separated by commas (e.g., animals, nature, food)"
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    @error('categories')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    <p class="mt-1 text-xs text-gray-500">Optional: Add categories to help organize words</p>
                </div>

                <!-- Hints -->
                <div>
                    <label for="hints" class="block text-sm font-medium text-gray-700">Hints</label>
                    <textarea id="hints" name="hints" rows="2" 
                              placeholder="Enter hints for this word (optional)"
                              class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">{{ old('hints') }}</textarea>
                    @error('hints')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    <p class="mt-1 text-xs text-gray-500">Optional: Provide hints that can be shown to players</p>
                </div>

                <!-- Word Preview -->
                <div class="bg-gray-50 rounded-lg p-4">
                    <h3 class="text-sm font-medium text-gray-700 mb-2">Word Preview</h3>
                    <div class="flex justify-center space-x-2">
                        @for($i = 0; $i < 5; $i++)
                        <div id="letter-{{ $i }}" class="w-12 h-12 border-2 border-gray-300 rounded flex items-center justify-center text-lg font-bold text-gray-400 bg-white">
                            ?
                        </div>
                        @endfor
                    </div>
                    <p class="text-xs text-gray-500 mt-2 text-center">This is how the word will appear in the game</p>
                </div>

                <!-- Form Actions -->
                <div class="flex justify-end space-x-3 pt-6 border-t border-gray-200">
                    <a href="{{ route('admin.words.index') }}" 
                       class="bg-gray-300 text-gray-700 px-4 py-2 rounded-md hover:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2">
                        Cancel
                    </a>
                    <button type="submit" 
                            class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                        Add Word
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// Word input handling
document.getElementById('word').addEventListener('input', function() {
    const word = this.value.toUpperCase().replace(/[^A-Z]/g, '');
    this.value = word;
    updateWordPreview(word);
});

// Difficulty slider handling
document.getElementById('difficulty_score').addEventListener('input', function() {
    document.getElementById('difficulty-display').textContent = this.value + '/5';
});

// Update word preview
function updateWordPreview(word) {
    const letters = word.split('');
    
    for (let i = 0; i < 5; i++) {
        const letterElement = document.getElementById(`letter-${i}`);
        if (i < letters.length) {
            letterElement.textContent = letters[i];
            letterElement.classList.remove('text-gray-400');
            letterElement.classList.add('text-gray-900');
        } else {
            letterElement.textContent = '?';
            letterElement.classList.remove('text-gray-900');
            letterElement.classList.add('text-gray-400');
        }
    }
}

// Initialize preview on page load
document.addEventListener('DOMContentLoaded', function() {
    const word = document.getElementById('word').value;
    if (word) {
        updateWordPreview(word);
    }
    
    const difficulty = document.getElementById('difficulty_score').value;
    document.getElementById('difficulty-display').textContent = difficulty + '/5';
});
</script>

<style>
.slider::-webkit-slider-thumb {
    appearance: none;
    height: 20px;
    width: 20px;
    border-radius: 50%;
    background: #3B82F6;
    cursor: pointer;
}

.slider::-moz-range-thumb {
    height: 20px;
    width: 20px;
    border-radius: 50%;
    background: #3B82F6;
    cursor: pointer;
    border: none;
}
</style>
@endsection 