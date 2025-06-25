<?php
@extends('layouts.app')

@section('title', 'Add Friends')

@section('content')
    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <div class="flex justify-between items-center mb-6">
                        <h1 class="text-2xl font-bold text-gray-900">Add Friends</h1>
                        <a href="{{ route('friends.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">
                            Back to Friends
                        </a>
                    </div>

                    <!-- Search Form -->
                    <div class="mb-8">
                        <label for="search" class="block text-sm font-medium text-gray-700 mb-2">Search for users</label>
                        <div class="flex">
                            <input type="text"
                                   id="search"
                                   placeholder="Search by username, email, or name..."
                                   class="flex-1 rounded-l-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                   minlength="2">
                            <button type="button"
                                    id="search-btn"
                                    class="px-4 py-2 bg-indigo-600 border border-l-0 border-indigo-600 rounded-r-md font-semibold text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                Search
                            </button>
                        </div>
                        <p class="mt-1 text-sm text-gray-500">Enter at least 2 characters to search for users</p>
                    </div>

                    <!-- Loading State -->
                    <div id="loading" class="hidden text-center py-8">
                        <svg class="animate-spin -ml-1 mr-3 h-8 w-8 text-indigo-600 mx-auto" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <p class="text-gray-500 mt-2">Searching...</p>
                    </div>

                    <!-- Search Results -->
                    <div id="search-results" class="hidden">
                        <h2 class="text-lg font-medium text-gray-900 mb-4">Search Results</h2>
                        <div id="results-container" class="space-y-4">
                            <!-- Results will be populated here -->
                        </div>
                    </div>

                    <!-- No Results Message -->
                    <div id="no-results" class="hidden text-center py-8">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900">No users found</h3>
                        <p class="mt-1 text-sm text-gray-500">Try searching with different keywords.</p>
                    </div>

                    <!-- Error Message -->
                    <div id="error-message" class="hidden bg-red-50 border border-red-200 rounded-md p-4">
                        <div class="flex">
                            <svg class="h-5 w-5 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                            </svg>
                            <div class="ml-3">
                                <h3 class="text-sm font-medium text-red-800">Search Error</h3>
                                <p class="mt-1 text-sm text-red-700" id="error-text"></p>
                            </div>
                        </div>
                    </div>

                    <!-- Tips -->
                    <div class="mt-8 bg-blue-50 border border-blue-200 rounded-md p-4">
                        <div class="flex">
                            <svg class="h-5 w-5 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                            </svg>
                            <div class="ml-3">
                                <h3 class="text-sm font-medium text-blue-800">Tips for finding friends</h3>
                                <div class="mt-2 text-sm text-blue-700">
                                    <ul class="list-disc list-inside space-y-1">
                                        <li>Search by their username, email address, or display name</li>
                                        <li>Make sure you spell their information correctly</li>
                                        <li>Ask your friends for their exact username to make finding them easier</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const searchInput = document.getElementById('search');
            const searchBtn = document.getElementById('search-btn');
            const loading = document.getElementById('loading');
            const searchResults = document.getElementById('search-results');
            const resultsContainer = document.getElementById('results-container');
            const noResults = document.getElementById('no-results');
            const errorMessage = document.getElementById('error-message');
            const errorText = document.getElementById('error-text');

            let searchTimeout;

            // Search on button click
            searchBtn.addEventListener('click', performSearch);

            // Search on Enter key
            searchInput.addEventListener('keypress', function(e) {
                if (e.key === 'Enter') {
                    performSearch();
                }
            });

            // Auto-search with debounce
            searchInput.addEventListener('input', function() {
                clearTimeout(searchTimeout);
                const query = this.value.trim();

                if (query.length >= 2) {
                    searchTimeout = setTimeout(performSearch, 500);
                } else {
                    hideAllSections();
                }
            });

            function performSearch() {
                const query = searchInput.value.trim();

                if (query.length < 2) {
                    showError('Please enter at least 2 characters to search.');
                    return;
                }

                hideAllSections();
                loading.classList.remove('hidden');

                fetch(`{{ route('friends.search') }}?query=${encodeURIComponent(query)}`, {
                    method: 'GET',
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                    .then(response => response.json())
                    .then(data => {
                        loading.classList.add('hidden');

                        if (data.length > 0) {
                            displayResults(data);
                        } else {
                            noResults.classList.remove('hidden');
                        }
                    })
                    .catch(error => {
                        loading.classList.add('hidden');
                        showError('Failed to search for users. Please try again.');
                        console.error('Search error:', error);
                    });
            }

            function displayResults(users) {
                resultsContainer.innerHTML = '';

                users.forEach(user => {
                    const userCard = createUserCard(user);
                    resultsContainer.appendChild(userCard);
                });

                searchResults.classList.remove('hidden');
            }

            function createUserCard(user) {
                const card = document.createElement('div');
                card.className = 'flex items-center justify-between p-4 border border-gray-200 rounded-lg hover:bg-gray-50';

                card.innerHTML = `
            <div class="flex items-center">
                ${user.avatar
                    ? `<img class="h-12 w-12 rounded-full object-cover" src="/storage/${user.avatar}" alt="${user.name}">`
                    : `<div class="h-12 w-12 rounded-full bg-indigo-500 flex items-center justify-center text-white text-lg">${user.name.charAt(0).toUpperCase()}</div>`
                }
                <div class="ml-4">
                    <h3 class="text-sm font-medium text-gray-900">${user.name}</h3>
                    <p class="text-sm text-gray-500">@${user.username}</p>
                    <div class="text-xs text-gray-400 mt-1">
                        ${user.games_won} wins • ${user.win_rate}% win rate
                    </div>
                </div>
            </div>
            <div class="flex space-x-2">
                ${getFriendshipButton(user)}
            </div>
        `;

                return card;
            }

            function getFriendshipButton(user) {
                switch (user.friendship_status) {
                    case 'friends':
                        return '<span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">Already Friends</span>';
                    case 'request_sent':
                        return '<span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">Request Sent</span>';
                    case 'request_received':
                        return '<span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">Request Received</span>';
                    case 'none':
                        return `<button onclick="sendFriendRequest(${user.id})" class="px-3 py-1 bg-indigo-600 text-white text-xs font-medium rounded hover:bg-indigo-700">Add Friend</button>`;
                    default:
                        return '';
                }
            }

            function hideAllSections() {
                loading.classList.add('hidden');
                searchResults.classList.add('hidden');
                noResults.classList.add('hidden');
                errorMessage.classList.add('hidden');
            }

            function showError(message) {
                hideAllSections();
                errorText.textContent = message;
                errorMessage.classList.remove('hidden');
            }

            // Global function for sending friend requests
            window.sendFriendRequest = function(userId) {
                fetch(`{{ route('friends.store') }}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({
                        user_id: userId
                    })
                })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            // Refresh the search results to update the button
                            performSearch();
                            // Show success message
                            showSuccess('Friend request sent successfully!');
                        } else {
                            showError(data.message || 'Failed to send friend request.');
                        }
                    })
                    .catch(error => {
                        showError('Failed to send friend request. Please try again.');
                        console.error('Friend request error:', error);
                    });
            };

            function showSuccess(message) {
                // Create and show a temporary success message
                const successDiv = document.createElement('div');
                successDiv.className = 'fixed top-4 right-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded z-50';
                successDiv.textContent = message;
                document.body.appendChild(successDiv);

                setTimeout(() => {
                    document.body.removeChild(successDiv);
                }, 3000);
            }
        });
    </script>
@endsection
