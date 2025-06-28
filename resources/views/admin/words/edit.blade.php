@extends('layouts.app')

@section('title', 'Edit Word')

@section('content')
<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8 flex items-center">
            <a href="{{ route('admin.words.show', $word) }}" class="text-gray-400 hover:text-gray-600 mr-4">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                </svg>
            </a>
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Edit Word</h1>
                <p class="mt-2 text-gray-600">Update word information</p>
            </div>
        </div>

        <!-- Edit Word Form -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-medium text-gray-900">Word Information</h2>
            </div>
            <form action="{{ route('admin.words.update', $word) }}" method="POST" class="p-6 space-y-6">
                @csrf
                @method('PUT')
                <!-- Word -->
                <div>
                    <label for="word" class="block text-sm font-medium text-gray-700">Word *</label>
                    <input type="text" id="word" name="word" value="{{ old('word', $word->word) }}" required
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-center text-lg font-mono uppercase tracking-wider">
                    @error('word')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <!-- Definition -->
                <div>
                    <label for="definition" class="block text-sm font-medium text-gray-700">Definition</label>
                    <textarea id="definition" name="definition" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">{{ old('definition', $word->definition) }}</textarea>
                    @error('definition')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <!-- Difficulty Score -->
                <div>
                    <label for="difficulty_score" class="block text-sm font-medium text-gray-700">Difficulty Score</label>
                    <div class="mt-1 flex items-center space-x-4">
                        <input type="range" id="difficulty_score" name="difficulty_score" min="1" max="5" value="{{ old('difficulty_score', $word->difficulty_score ?? 3) }}" class="flex-1 h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer slider">
                        <span id="difficulty-display" class="text-lg font-semibold text-gray-900 w-8 text-center">{{ old('difficulty_score', $word->difficulty_score ?? 3) }}/5</span>
                    </div>
                    <div class="flex justify-between text-xs text-gray-500 mt-1">
                        <span>Easy</span>
                        <span>Hard</span>
                    </div>
                    @error('difficulty_score')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <!-- Status -->
                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700">Status *</label>
                    <select id="status" name="status" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="active" {{ old('status', $word->status) == 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ old('status', $word->status) == 'inactive' ? 'selected' : '' }}>Inactive</option>
                    </select>
                    @error('status')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <!-- Categories -->
                <div>
                    <label for="categories" class="block text-sm font-medium text-gray-700">Categories</label>
                    <input type="text" id="categories" name="categories" value="{{ old('categories', $word->categories) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    @error('categories')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <!-- Hints -->
                <div>
                    <label for="hints" class="block text-sm font-medium text-gray-700">Hints</label>
                    <textarea id="hints" name="hints" rows="2" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">{{ old('hints', $word->hints) }}</textarea>
                    @error('hints')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <!-- Form Actions -->
                <div class="flex justify-end space-x-3 pt-6 border-t border-gray-200">
                    <a href="{{ route('admin.words.show', $word) }}" class="bg-gray-300 text-gray-700 px-4 py-2 rounded-md hover:bg-gray-400">Cancel</a>
                    <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700">Update Word</button>
                </div>
            </form>
        </div>
    </div>
</div>
<script>
document.getElementById('difficulty_score').addEventListener('input', function() {
    document.getElementById('difficulty-display').textContent = this.value + '/5';
});
</script>
@endsection 