@extends('layouts.app')

@section('title', 'Manage Words - Admin')

@section('content')
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <div class="flex justify-between items-center mb-6">
                        <h1 class="text-2xl font-bold text-gray-900">Manage Words</h1>
                        <div class="flex space-x-4">
                            <!-- Search Form -->
                            <form method="GET" action="{{ route('admin.words.index') }}" class="flex">
                                <input type="text"
                                       name="search"
                                       placeholder="Search words..."
                                       value="{{ request('search') }}"
                                       class="rounded-l-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                <button type="submit" class="px-4 py-2 bg-indigo-600 border border-l-0 border-indigo-600 rounded-r-md font-semibold text-white hover:bg-indigo-700">
                                    Search
                                </button>
                            </form>

                            <a href="{{ route('admin.words.create') }}" class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                </svg>
                                Add Word
                            </a>

                            <button onclick="document.getElementById('bulk-import-modal').classList.remove('hidden')" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10"></path>
                                </svg>
                                Bulk Import
                            </button>
                        </div>
                    </div>

                    <!-- Statistics -->
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                        <div class="bg-blue-50 rounded-lg p-4">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <svg class="h-6 w-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10m0 0V6a2 2 0 00-2-2H9a2 2 0 00-2 2v2m0 0v8a2 2 0 002 2h8a2 2 0 002-2V8M9 12h6"></path>
                                    </svg>
                                </div>
                                <div class="ml-4">
                                    <p class="text-sm font-medium text-blue-900">Total Words</p>
                                    <p class="text-2xl font-semibold text-blue-600">{{ $totalWords }}</p>
                                </div>
                            </div>
                        </div>

                        <div class="bg-green-50 rounded-lg p-4">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <svg class="h-6 w-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </div>
                                <div class="ml-4">
                                    <p class="text-sm font-medium text-green-900">Active Words</p>
                                    <p class="text-2xl font-semibold text-green-600">{{ $activeWords }}</p>
                                </div>
                            </div>
                        </div>

                        <div class="bg-yellow-50 rounded-lg p-4">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <svg class="h-6 w-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </div>
                                <div class="ml-4">
                                    <p class="text-sm font-medium text-yellow-900">Used in Games</p>
                                    <p class="text-2xl font-semibold text-yellow-600">{{ $wordsUsedInGames }}</p>
                                </div>
                            </div>
                        </div>

                        <div class="bg-purple-50 rounded-lg p-4">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <svg class="h-6 w-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                                    </svg>
                                </div>
                                <div class="ml-4">
                                    <p class="text-sm font-medium text-purple-900">Categories</p>
                                    <p class="text-2xl font-semibold text-purple-600">{{ $categories->count() }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Filter Tabs -->
                    <div class="border-b border-gray-200 mb-6">
                        <nav class="-mb-px flex space-x-8" aria-label="Tabs">
                            <a href="{{ route('admin.words.index') }}"
                               class="py-2 px-1 border-b-2 font-medium text-sm {{ !request('status') ? 'border-indigo-500 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                                All Words
                            </a>
                            <a href="{{ route('admin.words.index', ['status' => 'active']) }}"
                               class="py-2 px-1 border-b-2 font-medium text-sm {{ request('status') === 'active' ? 'border-indigo-500 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                                Active
                            </a>
                            <a href="{{ route('admin.words.index', ['status' => 'inactive']) }}"
                               class="py-2 px-1 border-b-2 font-medium text-sm {{ request('status') === 'inactive' ? 'border-indigo-500 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                                Inactive
                            </a>
                        </nav>
                    </div>

                    @if($words->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        <input type="checkbox" id="select-all" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200">
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Word</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Category</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Difficulty</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Used in Games</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Added</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($words as $word)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <input type="checkbox" name="selected_words[]" value="{{ $word->id }}" class="word-checkbox rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200">
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-medium text-gray-900 font-mono text-lg">{{ strtoupper($word->word) }}</div>
                                            <div class="text-sm text-gray-500">{{ strlen($word->word) }} letters</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                                {{ $word->category }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            @php
                                                $difficultyColors = [
                                                    'easy' => 'bg-green-100 text-green-800',
                                                    'medium' => 'bg-yellow-100 text-yellow-800',
                                                    'hard' => 'bg-red-100 text-red-800'
                                                ];
                                            @endphp
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $difficultyColors[$word->difficulty] ?? 'bg-gray-100 text-gray-800' }}">
                                                {{ ucfirst($word->difficulty) }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            @if($word->is_active)
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">Active</span>
                                            @else
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">Inactive</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $word->games_count }} times
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $word->created_at->format('M j, Y') }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                            <div class="flex space-x-2">
                                                <a href="{{ route('admin.words.edit', $word) }}" class="text-blue-600 hover:text-blue-900">Edit</a>

                                                @if($word->is_active)
                                                    <form action="{{ route('admin.words.toggle-status', $word) }}" method="POST" class="inline">
                                                        @csrf
                                                        <button type="submit" class="text-yellow-600 hover:text-yellow-900">Deactivate</button>
                                                    </form>
                                                @else
                                                    <form action="{{ route('admin.words.toggle-status', $word) }}" method="POST" class="inline">
                                                        @csrf
                                                        <button type="submit" class="text-green-600 hover:text-green-900">Activate</button>
                                                    </form>
                                                @endif

                                                @if($word->games_count === 0)
                                                    <form action="{{ route('admin.words.destroy', $word) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this word?')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="text-red-600 hover:text-red-900">Delete</button>
                                                    </form>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Bulk Actions -->
                        <div id="bulk-actions" class="hidden mt-4 p-4 bg-gray-50 rounded-lg">
                            <div class="flex items-center justify-between">
                                <span id="selected-count" class="text-sm text-gray-700">0 words selected</span>
                                <div class="flex space-x-2">
                                    <button onclick="bulkAction('activate')" class="px-3 py-1 bg-green-600 text-white text-sm rounded hover:bg-green-700">
                                        Activate Selected
                                    </button>
                                    <button onclick="bulkAction('deactivate')" class="px-3 py-1 bg-yellow-600 text-white text-sm rounded hover:bg-yellow-700">
                                        Deactivate Selected
                                    </button>
                                    <button onclick="bulkAction('delete')" class="px-3 py-1 bg-red-600 text-white text-sm rounded hover:bg-red-700">
                                        Delete Selected
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div class="mt-6">
                            {{ $words->appends(request()->query())->links() }}
                        </div>
                    @else
                        <div class="text-center py-12">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10m0 0V6a2 2 0 00-2-2H9a2 2 0 00-2 2v2m0 0v8a2 2 0 002 2h8a2 2 0 002-2V8M9 12h6"></path>
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-gray-900">No words found</h3>
                            <p class="mt-1 text-sm text-gray-500">
                                @if(request('search'))
                                    No words match your search criteria.
                                @else
                                    Get started by adding your first word.
                                @endif
                            </p>
                            <div class="mt-6">
                                <a href="{{ route('admin.words.create') }}" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700">
                                    Add Word
                                </a>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Bulk Import Modal -->
    <div id="bulk-import-modal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
        <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-1/2 shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Bulk Import Words</h3>
                <form action="{{ route('admin.words.bulk-import') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Upload CSV File</label>
                            <input type="file" name="file" accept=".csv" required class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-medium file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                            <p class="mt-1 text-xs text-gray-500">CSV format: word,category,difficulty,is_active</p>
                        </div>
                        <div class="flex justify-end space-x-3">
                            <button type="button" onclick="document.getElementById('bulk-import-modal').classList.add('hidden')" class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-md hover:bg-gray-200">
                                Cancel
                            </button>
                            <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-indigo-600 rounded-md hover:bg-indigo-700">
                                Import
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const selectAll = document.getElementById('select-all');
            const wordCheckboxes = document.querySelectorAll('.word-checkbox');
            const bulkActions = document.getElementById('bulk-actions');
            const selectedCount = document.getElementById('selected-count');

            selectAll.addEventListener('change', function() {
                wordCheckboxes.forEach(checkbox => {
                    checkbox.checked = this.checked;
                });
                updateBulkActions();
            });

            wordCheckboxes.forEach(checkbox => {
                checkbox.addEventListener('change', updateBulkActions);
            });

            function updateBulkActions() {
                const checked = document.querySelectorAll('.word-checkbox:checked');
                if (checked.length > 0) {
                    bulkActions.classList.remove('hidden');
                    selectedCount.textContent = `${checked.length} word${checked.length !== 1 ? 's' : ''} selected`;
                } else {
                    bulkActions.classList.add('hidden');
                }
            }

            window.bulkAction = function(action) {
                const checked = Array.from(document.querySelectorAll('.word-checkbox:checked')).map(cb => cb.value);
                if (checked.length === 0) return;

                if (action === 'delete' && !confirm('Are you sure you want to delete the selected words?')) return;

                // Create form and submit
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = `{{ route('admin.words.bulk-action') }}`;

                const csrfToken = document.createElement('input');
                csrfToken.type = 'hidden';
                csrfToken.name = '_token';
                csrfToken.value = '{{ csrf_token() }}';
                form.appendChild(csrfToken);

                const actionInput = document.createElement('input');
                actionInput.type = 'hidden';
                actionInput.name = 'action';
                actionInput.value = action;
                form.appendChild(actionInput);

                checked.forEach(id => {
                    const input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = 'word_ids[]';
                    input.value = id;
                    form.appendChild(input);
                });

                document.body.appendChild(form);
                form.submit();
            };
        });
    </script>
@endsection
