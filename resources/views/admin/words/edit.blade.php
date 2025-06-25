@extends('layouts.app')

@section('title', 'Edit Word - ' . $word->word)

@section('content')
    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <div class="flex justify-between items-center mb-6">
                        <div>
                            <h1 class="text-2xl font-bold text-gray-900">Edit Word</h1>
                            <p class="text-sm text-gray-500 mt-1">Current word: <span class="font-mono text-lg font-bold">{{ strtoupper($word->word) }}</span></p>
                        </div>
                        <div class="flex space-x-3">
                            <a href="{{ route('admin.words.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">
                                Back to Words
                            </a>
                        </div>
                    </div>

                    <!-- Usage Warning -->
                    @if($word->games_count > 0)
                        <div class="mb-6 p-4 bg-yellow-50 border border-yellow-200 rounded-md">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <svg class="h-5 w-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <h3 class="text-sm font-medium text-yellow-800">Word in Use</h3>
                                    <div class="mt-2 text-sm text-yellow-700">
                                        <p>This word has been used in {{ $word->games_count }} game{{ $word->games_count !== 1 ? 's' : '' }}. Changing the word itself is not recommended as it may affect game history.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif

                    <form action="{{ route('admin.words.update', $word) }}" method="POST" class="space-y-6">
                        @csrf
                        @method('PUT')

                        <!-- Word -->
                        <div>
                            <label for="word" class="block text-sm font-medium text-gray-700">Word</label>
                            <input type="text" name="word" id="word" value="{{ old('word', $word->word) }}" required
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm @error('word') border-red-300 @enderror"
                                   pattern="[A-Za-z]+"
                                   title="Only letters are allowed"
                                {{ $word->games_count > 0 ? 'readonly' : '' }}>
                            @if($word->games_count > 0)
                                <p class="mt-1 text-xs text-yellow-600">Word cannot be changed because it has been used in games</p>
                            @else
                                <p class="mt-1 text-xs text-gray-500">Only letters are allowed, no spaces or special characters</p>
                            @endif
                            @error('word')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                            <div class="mt-2">
                                <span class="text-sm text-gray-500">Preview: </span>
                                <span id="word-display" class="font-mono text-lg font-bold text-gray-900">{{ strtoupper($word->word) }}</span>
                                <span id="word-length" class="ml-2 text-xs text-gray-500">({{ strlen($word->word) }} letters)</span>
                            </div>
                        </div>

                        <!-- Category -->
                        <div>
                            <label for="category" class="block text-sm font-medium text-gray-700">Category</label>
                            <select name="category" id="category" required
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm @error('category') border-red-300 @enderror">
                                <option value="">Select a category</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category }}" {{ old('category', $word->category) === $category ? 'selected' : '' }}>
                                        {{ ucfirst($category) }}
                                    </option>
                                @endforeach
                                @if(!in_array($word->category, $categories))
                                    <option value="{{ $word->category }}" selected>{{ ucfirst($word->category) }} (current)</option>
                                @endif
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
                                           {{ old('difficulty', $word->difficulty) === 'easy' ? 'checked' : '' }}
                                           class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300">
                                    <label for="difficulty_easy" class="ml-3 block text-sm font-medium text-gray-700">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 mr-2">Easy</span>
                                        3-4 letters, common words
                                    </label>
                                </div>
                                <div class="flex items-center">
                                    <input id="difficulty_medium" name="difficulty" type="radio" value="medium"
                                           {{ old('difficulty', $word->difficulty) === 'medium' ? 'checked' : '' }}
                                           class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300">
                                    <label for="difficulty_medium" class="ml-3 block text-sm font-medium text-gray-700">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 mr-2">Medium</span>
                                        5-6 letters, moderate difficulty
                                    </label>
                                </div>
                                <div class="flex items-center">
                                    <input id="difficulty_hard" name="difficulty" type="radio" value="hard"
                                           {{ old('difficulty', $word->difficulty) === 'hard' ? 'checked' : '' }}
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

                        <!-- Status -->
                        <div>
                            <div class="flex items-center justify-between">
                                <div>
                                    <label for="is_active" class="text-sm font-medium text-gray-700">Active Status</label>
                                    <p class="text-sm text-gray-500">Active words can be used in games</p>
                                </div>
                                <input type="checkbox" name="is_active" id="is_active" value="1"
                                       {{ old('is_active', $word->is_active) ? 'checked' : '' }}
                                       class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300 rounded">
                            </div>
                        </div>

                        <!-- Word Statistics -->
                        <div class="border-t border-gray-200 pt-6">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Word Statistics</h3>
                            <div class="bg-gray-50 rounded-lg p-4">
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500">Times Used in Games</dt>
                                        <dd class="mt-1 text-2xl font-semibold text-gray-900">{{ $word->games_count }}</dd>
                                    </div>
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500">Date Added</dt>
                                        <dd class="mt-1 text-sm text-gray-900">{{ $word->created_at->format('F j, Y') }}</dd>
                                    </div>
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500">Last Modified</dt>
                                        <dd class="mt-1 text-sm text-gray-900">{{ $word->updated_at->format('F j, Y g:i A') }}</dd>
                                    </div>
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500">Current Status</dt>
                                        <dd class="mt-1">
                                            @if($word->is_active)
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">Active</span>
                                            @else
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">Inactive</span>
                                            @endif
                                        </dd>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="flex justify-end space-x-3 pt-6 border-t border-gray-200">
                            <a href="{{ route('admin.words.index') }}" class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50">
                                Cancel
                            </a>
                            <button type="submit" class="px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700">
                                Update Word
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
            const wordDisplay = document.getElementById('word-display');
            const wordLength = document.getElementById('word-length');
            const categorySelect = document.getElementById('category');
            const customCategoryDiv = document.getElementById('custom-category-div');

            // Word input handling (only if not readonly)
            if (!wordInput.readOnly) {
                wordInput.addEventListener('input', function() {
                    const word = this.value.toUpperCase();
                    wordDisplay.textContent = word;
                    wordLength.textContent = `(${word.length} letters)`;
                });
            }

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

            // Form validation
            document.querySelector('form').addEventListener('submit', function(e) {
                const word = wordInput.value.trim();

                if (!wordInput.readOnly && word && !/^[A-Za-z]+$/.test(word)) {
                    e.preventDefault();
                    alert('Word must contain only letters');
                    return;
                }
            });
        });
    </script>
@endsection
