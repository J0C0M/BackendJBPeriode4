@extends('layouts.app')

@section('title', 'Add Friends')

@section('content')
<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
            <div class="flex items-center">
                <a href="{{ route('friends.index') }}" class="text-gray-400 hover:text-gray-600 mr-4">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                    </svg>
                </a>
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Add Friends</h1>
                    <p class="mt-2 text-gray-600">Find and connect with other Wordle players</p>
                </div>
            </div>
        </div>

        <!-- Search Section -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 mb-8">
            <div class="p-6">
                <h2 class="text-lg font-medium text-gray-900 mb-4">Search for Users</h2>
                
                <form id="search-form" class="space-y-4">
                    <div class="flex space-x-4">
                        <div class="flex-1">
                            <label for="search" class="block text-sm font-medium text-gray-700">Search by name or username</label>
                            <input type="text" id="search" name="search" placeholder="Enter name or username..." 
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        </div>
                        <div class="flex items-end">
                            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                                Search
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Search Results -->
        <div id="search-results" class="bg-white rounded-lg shadow-sm border border-gray-200 hidden">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-medium text-gray-900">Search Results</h2>
            </div>
            <div id="results-list" class="divide-y divide-gray-200">
                <!-- Results will be populated here -->
            </div>
        </div>

        <!-- Suggested Users -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-medium text-gray-900">Suggested Users</h2>
                <p class="text-sm text-gray-500 mt-1">Popular players you might want to connect with</p>
            </div>
            
            <div id="suggested-users" class="divide-y divide-gray-200">
                <!-- Loading state -->
                <div class="px-6 py-4">
                    <div class="animate-pulse">
                        <div class="flex items-center space-x-4">
                            <div class="rounded-full bg-gray-200 h-10 w-10"></div>
                            <div class="flex-1 space-y-2">
                                <div class="h-4 bg-gray-200 rounded w-3/4"></div>
                                <div class="h-3 bg-gray-200 rounded w-1/2"></div>
                            </div>
                            <div class="h-8 bg-gray-200 rounded w-20"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchForm = document.getElementById('search-form');
    const searchInput = document.getElementById('search');
    const searchResults = document.getElementById('search-results');
    const resultsList = document.getElementById('results-list');
    const suggestedUsers = document.getElementById('suggested-users');

    // Load suggested users on page load
    loadSuggestedUsers();

    // Search form submission
    searchForm.addEventListener('submit', function(e) {
        e.preventDefault();
        const query = searchInput.value.trim();
        
        if (query.length < 2) {
            alert('Please enter at least 2 characters to search');
            return;
        }

        searchUsers(query);
    });

    // Search users function
    function searchUsers(query) {
        fetch(`{{ route('friends.search') }}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({ search: query })
        })
        .then(response => response.json())
        .then(data => {
            displaySearchResults(data.users);
        })
        .catch(error => {
            console.error('Error searching users:', error);
            alert('Error searching users. Please try again.');
        });
    }

    // Display search results
    function displaySearchResults(users) {
        if (users.length === 0) {
            resultsList.innerHTML = `
                <div class="px-6 py-8 text-center">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">No users found</h3>
                    <p class="mt-1 text-sm text-gray-500">Try searching with a different term.</p>
                </div>
            `;
        } else {
            resultsList.innerHTML = users.map(user => `
                <div class="px-6 py-4 flex items-center justify-between">
                    <div class="flex items-center">
                        ${user.avatar ? 
                            `<img class="h-10 w-10 rounded-full object-cover" src="/storage/${user.avatar}" alt="${user.name}">` :
                            `<div class="h-10 w-10 rounded-full bg-gray-500 flex items-center justify-center text-white font-semibold">${user.name.charAt(0)}</div>`
                        }
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-900">${user.name}</p>
                            <p class="text-sm text-gray-500">@${user.username}</p>
                        </div>
                    </div>
                    <div class="flex items-center space-x-2">
                        ${user.friendship_status === 'none' ? `
                            <button onclick="sendFriendRequest(${user.id})" class="bg-blue-600 text-white px-3 py-1 rounded-md text-sm hover:bg-blue-700">
                                Add Friend
                            </button>
                        ` : user.friendship_status === 'pending_sent' ? `
                            <span class="text-yellow-600 text-sm">Request Sent</span>
                        ` : user.friendship_status === 'pending_received' ? `
                            <div class="flex space-x-2">
                                <button onclick="acceptFriendRequest(${user.id})" class="bg-green-600 text-white px-3 py-1 rounded-md text-sm hover:bg-green-700">
                                    Accept
                                </button>
                                <button onclick="declineFriendRequest(${user.id})" class="bg-red-600 text-white px-3 py-1 rounded-md text-sm hover:bg-red-700">
                                    Decline
                                </button>
                            </div>
                        ` : user.friendship_status === 'friends' ? `
                            <span class="text-green-600 text-sm">Already Friends</span>
                        ` : ''}
                        <a href="/users/${user.id}" class="text-gray-600 hover:text-gray-900 px-3 py-1 rounded-md text-sm">
                            View Profile
                        </a>
                    </div>
                </div>
            `).join('');
        }
        
        searchResults.classList.remove('hidden');
    }

    // Load suggested users
    function loadSuggestedUsers() {
        // Load some random users as suggestions
        fetch(`{{ route('friends.search') }}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({ search: 'demo' }) // Search for 'demo' to get the demo users
        })
        .then(response => response.json())
        .then(data => {
            if (data.success && data.users && data.users.length > 0) {
                // Take first 5 users as suggestions
                const suggestedUsersList = data.users.slice(0, 5);
                suggestedUsers.innerHTML = suggestedUsersList.map(user => `
                    <div class="px-6 py-4 flex items-center justify-between">
                        <div class="flex items-center">
                            ${user.avatar ? 
                                `<img class="h-10 w-10 rounded-full object-cover" src="/storage/${user.avatar}" alt="${user.name}">` :
                                `<div class="h-10 w-10 rounded-full bg-gray-500 flex items-center justify-center text-white font-semibold">${user.name.charAt(0)}</div>`
                            }
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-900">${user.name}</p>
                                <p class="text-sm text-gray-500">@${user.username}</p>
                            </div>
                        </div>
                        <div class="flex items-center space-x-2">
                            ${user.friendship_status === 'none' ? `
                                <button onclick="sendFriendRequest(${user.id})" class="bg-blue-600 text-white px-3 py-1 rounded-md text-sm hover:bg-blue-700">
                                    Add Friend
                                </button>
                            ` : user.friendship_status === 'pending_sent' ? `
                                <span class="text-yellow-600 text-sm">Request Sent</span>
                            ` : user.friendship_status === 'friends' ? `
                                <span class="text-green-600 text-sm">Already Friends</span>
                            ` : ''}
                            <a href="/users/${user.id}" class="text-gray-600 hover:text-gray-900 px-3 py-1 rounded-md text-sm">
                                View Profile
                            </a>
                        </div>
                    </div>
                `).join('');
            } else {
                suggestedUsers.innerHTML = `
                    <div class="px-6 py-8 text-center">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900">No suggestions available</h3>
                        <p class="mt-1 text-sm text-gray-500">Try searching for users manually.</p>
                    </div>
                `;
            }
        })
        .catch(error => {
            console.error('Error loading suggested users:', error);
            suggestedUsers.innerHTML = `
                <div class="px-6 py-8 text-center">
                    <p class="text-sm text-gray-500">Error loading suggestions. Please try again.</p>
                </div>
            `;
        });
    }
});

// Friend request functions
function sendFriendRequest(userId) {
    fetch(`{{ route('friends.store') }}`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({ user_id: userId })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Friend request sent successfully!');
            location.reload();
        } else {
            alert(data.message || 'Error sending friend request');
        }
    })
    .catch(error => {
        console.error('Error sending friend request:', error);
        alert('Error sending friend request. Please try again.');
    });
}

function acceptFriendRequest(userId) {
    // Redirect to friends page where they can accept/decline requests
    window.location.href = `{{ route('friends.index') }}`;
}

function declineFriendRequest(userId) {
    // Redirect to friends page where they can accept/decline requests
    window.location.href = `{{ route('friends.index') }}`;
}
</script>
@endsection 