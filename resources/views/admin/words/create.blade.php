@extends('layouts.app')

@section('title', 'Add Word - Admin')

@section('content')
    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <div class="flex justify-between items-center mb-6">
                        <h1 class="text-2xl font-bold text-gray-900">Add New Word</h1>
                        <a href="{{ route('admin.words.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">
                            Back to Words
                        </a>
                    </div>

                    <form action="{{ route('admin.words.store') }}" method="POST" class="space-y-6">
                        @csrf

                        <!-- Word -->
                        <div>
                            <label for="word" class="block text-sm font-medium text-gray-700">Word</label>
                            <input type="text" name="word" id="word" value="{{ old('word') }}" required
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm @error('word') border-red-300 @enderror"
                                   placeholder="Enter the word (letters only)"
                                   pattern="[A-Za-z]+"
                                   title="Only letters are allowed">
                            <p class="mt-1 text-xs text-gray-500">Only letters are allowed, no spaces or special characters</p>
                            @error('word')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                            <div id="word-preview" class="mt-2 hidden">
                                <span class="text-sm text-gray-500">Preview: </span>
                                <span id="word-display" class="font-mono text-lg font-bold text-gray-900"></span>
                                <span id="word-length" class="ml-2 text-xs text-gray-500"></span>
                            </div>
                        </div>

                        <!-- Category -->
                        <div>
                            <label for="category" class="block text-sm font-medium text-gray-700">Category</label>
                            <select name="category" id="category" required
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm @error('category') border-red-300 @enderror">
                                <option value="">Select a category</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category }}" {{ old('category') === $category ? 'selected' : '' }}>
                                        {{ ucfirst($category) }}
                                    </option>
                                @endforeach
                                <option value="custom">Custom...</option>
                            </select>
                            @error('category')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Custom Category -->
                        <div id="custom-category-div" class="hidden">
                            <label for="custom_category" class="block text-sm font-medium text-gray-700">Custom Category</label>
                            <input type="text" name="custom_category" id="custom_category" value="{{ old('custom_category') }}"
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm @error('custom_category') border-red-300 @enderror"
                                   placeholder="Enter new category name">
                            @error('custom_category')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Difficulty -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-3">Difficulty Level</label>
                            <div class="space-y-3">
                                <div class="flex items-center">
                                    <input id="difficulty_easy" name="difficulty" type="radio" value="easy"
                                           {{ old('difficulty') === 'easy' ? 'checked' : '' }}
                                           class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300">
                                    <label for="difficulty_easy" class="ml-3 block text-sm font-medium text-gray-700">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 mr-2">Easy</span>
                                        3-4 letters, common words
                                    </label>
                                </div>
                                <div class="flex items-center">
                                    <input id="difficulty_medium" name="difficulty" type="radio" value="medium"
                                           {{ old('difficulty') === 'medium' ? 'checked' : '' }}
                                           class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300">
                                    <label for="difficulty_medium" class="ml-3 block text-sm font-medium text-gray-700">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 mr-2">Medium</span>
                                        5-6 letters, moderate difficulty
                                    </label>
                                </div>
                                <div class="flex items-center">
                                    <input id="difficulty_hard" name="difficulty" type="radio" value="hard"
                                           {{ old('difficulty') === 'hard' ? 'checked' : '' }}
                                           class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300">
                                    <label for="difficulty_hard" class="ml-3 block text-sm font-medium text-gray-700">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800 mr-2">Hard</span>
                                        7+ letters, challenging words
                                    </label>
                                </div>
                            </div>
                            @error('difficulty')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Auto-suggest difficulty based on word length -->
                        <div id="difficulty-suggestion" class="hidden p-3 bg-blue-50 border border-blue-200 rounded-md">
                            <p class="text-sm text-blue-800">
                                <svg class="inline w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                Suggested difficulty: <span id="suggested-difficulty" class="font-medium"></span>
                            </p>
                        </div>

                        <!-- Status -->
                        <div>
                            <div class="flex items-center justify-between">
                                <div>
                                    <label for="is_active" class="text-sm font-medium text-gray-700">Active Status</label>
                                    <p class="text-sm text-gray-500">Active words can be used in games</p>
                                </div>
                                <input type="checkbox" name="is_active" id="is_active" value="1"
                                       {{ old('is_active', '1') ? 'checked' : '' }}
                                       class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300 rounded">
                            </div>
                        </div>

                        <!-- Word Validation Info -->
                        <div class="bg-gray-50 rounded-lg p-4">
                            <h3 class="text-sm font-medium text-gray-900 mb-2">Word Requirements</h3>
                            <ul class="text-xs text-gray-600 space-y-1">
                                <li>• Must contain only letters (A-Z)</li>
                                <li>• Must be between 3 and 15 characters long</li>
                                <li>• Must be a valid English word</li>
                                <li>• Cannot already exist in the database</li>
                            </ul>
                        </div>

                        <!-- Bulk Add Option -->
                        <div class="border-t border-gray-200 pt-6">
                            <div class="flex items-center justify-between mb-4">
                                <div>
                                    <label for="bulk_mode" class="text-sm font-medium text-gray-700">Bulk Add Mode</label>
                                    <p class="text-sm text-gray-500">Add multiple words at once (one per line)</p>
                                </div>
                                <input type="checkbox" id="bulk_mode"
                                       class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300 rounded">
                            </div>

                            <div id="bulk-words-div" class="hidden">
                                <label for="bulk_words" class="block text-sm font-medium text-gray-700">Words (one per line)</label>
                                <textarea name="bulk_words" id="bulk_words" rows="6"
                                          class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                          placeholder="Enter multiple words, one per line..."></textarea>
                                <p class="mt-1 text-xs text-gray-500">All words will use the same category and difficulty settings above</p>
                            </div>
                        </div>

                        <div class="flex justify-end space-x-3 pt-6 border-t border-gray-200">
                            <a href="{{ route('admin.words.index') }}" class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50">
                                Cancel
                            </a>
                            <button type="submit" class="px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700">
                                <span id="submit-text">Add Word</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const wordInput = document.getElementById('word');
            const wordPreview = document.getElementById('word-preview');
            const wordDisplay = document.getElementById('word-display');
            const wordLength = document.getElementById('word-length');
            const categorySelect = document.getElementById('category');
            const customCategoryDiv = document.getElementById('custom-category-div');
            const bulkMode = document.getElementById('bulk_mode');
            const bulkWordsDiv = document.getElementById('bulk-words-div');
            const submitText = document.getElementById('submit-text');
            const difficultySuggestion = document.getElementById('difficulty-suggestion');
            const suggestedDifficulty = document.getElementById('suggested-difficulty');

            // Word input handling
            wordInput.addEventListener('input', function() {
                const word = this.value.toUpperCase();
                if (word.length > 0) {
                    wordDisplay.textContent = word;
                    wordLength.textContent = `(${word.length} letters)`;
                    wordPreview.classList.remove('hidden');

                    // Suggest difficulty based on length
                    suggestDifficulty(word.length);
                } else {
                    wordPreview.classList.add('hidden');
                    difficultySuggestion.classList.add('hidden');
                }
            });

            // Category handling
            categorySelect.addEventListener('change', function() {
                if (this.value === 'custom') {
                    customCategoryDiv.classList.remove('hidden');
                    document.getElementById('custom_category').required = true;
                } else {
                    customCategoryDiv.classList.add('hidden');
                    document.getElementById('custom_category').required = false;
                }
            });

            // Bulk mode handling
            bulkMode.addEventListener('change', function() {
                if (this.checked) {
                    bulkWordsDiv.classList.remove('hidden');
                    wordInput.required = false;
                    submitText.textContent = 'Add Words';
                } else {
                    bulkWordsDiv.classList.add('hidden');
                    wordInput.required = true;
                    submitText.textContent = 'Add Word';
                }
            });

            function suggestDifficulty(length) {
                let suggestion = '';
                let suggestionClass = '';

                if (length <= 4) {
                    suggestion = 'Easy';
                    suggestionClass = 'bg-green-100 text-green-800';
                } else if (length <= 6) {
                    suggestion = 'Medium';
                    suggestionClass = 'bg-yellow-100 text-yellow-800';
                } else {
                    suggestion = 'Hard';
                    suggestionClass = 'bg-red-100 text-red-800';
                }

                suggestedDifficulty.textContent = suggestion;
                suggestedDifficulty.className = `font-medium px-2 py-1 rounded text-xs ${suggestionClass}`;
                difficultySuggestion.classList.remove('hidden');
            }

            // Form validation
            document.querySelector('form').addEventListener('submit', function(e) {
                const isBulkMode = bulkMode.checked;
                const singleWord = wordInput.value.trim();
                const bulkWords = document.getElementById('bulk_words').value.trim();

                if (!isBulkMode && !singleWord) {
                    e.preventDefault();
                    alert('Please enter a word');
                    return;
                }

                if (isBulkMode && !bulkWords) {
                    e.preventDefault();
                    alert('Please enter words for bulk adding');
                    return;
                }

                // Validate single word format
                if (!isBulkMode && singleWord) {
                    if (!/^[A-Za-z]+$/.test(singleWord)) {
                        e.preventDefault();
                        alert('Word must contain only letters');
                        return;
                    }
                }

                // Validate bulk words format
                if (isBulkMode && bulkWords) {
                    const words = bulkWords.split('\n').filter(w => w.trim());
                    for (let word of words) {
                        if (!/^[A-Za-z]+$/.test(word.trim())) {
                            e.preventDefault();
                            alert(`Invalid word format: "${word.trim()}". All words must contain only letters.`);
                            return;
                        }
                    }
                }
            });
        });
    </script>
@endsection
