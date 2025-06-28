@extends('layouts.app')

@section('title', 'Comments')

@section('content')
<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900">Comments</h1>
            <p class="mt-2 text-gray-600">View and manage your comments</p>
        </div>

        <!-- Filter Tabs -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 mb-8">
            <div class="border-b border-gray-200">
                <nav class="flex space-x-8 px-6" aria-label="Tabs">
                    <button id="my-comments-tab" class="tab-button active border-b-2 border-blue-500 py-4 px-1 text-sm font-medium text-blue-600">
                        My Comments
                    </button>
                    <button id="game-comments-tab" class="tab-button border-b-2 border-transparent py-4 px-1 text-sm font-medium text-gray-500 hover:text-gray-700">
                        Game Comments
                    </button>
                    <button id="recent-comments-tab" class="tab-button border-b-2 border-transparent py-4 px-1 text-sm font-medium text-gray-500 hover:text-gray-700">
                        Recent Activity
                    </button>
                </nav>
            </div>

            <!-- My Comments Tab -->
            <div id="my-comments-content" class="tab-content active p-6">
                <div id="my-comments-list" class="space-y-4">
                    <!-- Comments will be loaded here -->
                    <div class="text-center py-8">
                        <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600 mx-auto"></div>
                        <p class="mt-2 text-sm text-gray-500">Loading comments...</p>
                    </div>
                </div>
            </div>

            <!-- Game Comments Tab -->
            <div id="game-comments-content" class="tab-content hidden p-6">
                <div class="mb-4">
                    <label for="game-filter" class="block text-sm font-medium text-gray-700">Filter by Game</label>
                    <select id="game-filter" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="">All Games</option>
                        <!-- Game options will be loaded here -->
                    </select>
                </div>
                <div id="game-comments-list" class="space-y-4">
                    <!-- Game comments will be loaded here -->
                </div>
            </div>

            <!-- Recent Comments Tab -->
            <div id="recent-comments-content" class="tab-content hidden p-6">
                <div id="recent-comments-list" class="space-y-4">
                    <!-- Recent comments will be loaded here -->
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Comment Modal for Editing -->
<div id="edit-comment-modal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Edit Comment</h3>
            <form id="edit-comment-form">
                <input type="hidden" id="edit-comment-id">
                <div class="mb-4">
                    <label for="edit-comment-content" class="block text-sm font-medium text-gray-700">Comment</label>
                    <textarea id="edit-comment-content" rows="3" 
                              class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"></textarea>
                </div>
                <div class="flex justify-end space-x-3">
                    <button type="button" onclick="closeEditModal()" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400">
                        Cancel
                    </button>
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                        Update
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const tabs = document.querySelectorAll('.tab-button');
    const contents = document.querySelectorAll('.tab-content');

    // Tab functionality
    tabs.forEach(tab => {
        tab.addEventListener('click', () => {
            const target = tab.id.replace('-tab', '-content');
            
            // Remove active classes
            tabs.forEach(t => {
                t.classList.remove('active', 'border-blue-500', 'text-blue-600');
                t.classList.add('border-transparent', 'text-gray-500');
            });
            contents.forEach(c => c.classList.add('hidden'));
            
            // Add active classes
            tab.classList.add('active', 'border-blue-500', 'text-blue-600');
            tab.classList.remove('border-transparent', 'text-gray-500');
            document.getElementById(target).classList.remove('hidden');

            // Load content based on active tab
            if (target === 'my-comments-content') {
                loadMyComments();
            } else if (target === 'game-comments-content') {
                loadGameComments();
            } else if (target === 'recent-comments-content') {
                loadRecentComments();
            }
        });
    });

    // Load initial content
    loadMyComments();
    loadGamesForFilter();
});

// Load user's comments
function loadMyComments() {
    const container = document.getElementById('my-comments-list');
    
    fetch(`{{ route('comments.user', auth()->user()) }}`)
    .then(response => response.json())
    .then(data => {
        if (data.comments && data.comments.length > 0) {
            container.innerHTML = data.comments.map(comment => createCommentHTML(comment, true)).join('');
        } else {
            container.innerHTML = `
                <div class="text-center py-8">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">No comments yet</h3>
                    <p class="mt-1 text-sm text-gray-500">Start commenting on games to see them here.</p>
                </div>
            `;
        }
    })
    .catch(error => {
        console.error('Error loading comments:', error);
        container.innerHTML = `
            <div class="text-center py-8">
                <p class="text-sm text-red-500">Error loading comments. Please try again.</p>
            </div>
        `;
    });
}

// Load game comments
function loadGameComments() {
    const container = document.getElementById('game-comments-list');
    const gameFilter = document.getElementById('game-filter').value;
    
    let url = `{{ route('comments.get') }}`;
    if (gameFilter) {
        url += `?game_id=${gameFilter}`;
    }
    
    fetch(url)
    .then(response => response.json())
    .then(data => {
        if (data.comments && data.comments.length > 0) {
            container.innerHTML = data.comments.map(comment => createCommentHTML(comment, false)).join('');
        } else {
            container.innerHTML = `
                <div class="text-center py-8">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">No comments found</h3>
                    <p class="mt-1 text-sm text-gray-500">No comments match your current filter.</p>
                </div>
            `;
        }
    })
    .catch(error => {
        console.error('Error loading game comments:', error);
        container.innerHTML = `
            <div class="text-center py-8">
                <p class="text-sm text-red-500">Error loading comments. Please try again.</p>
            </div>
        `;
    });
}

// Load recent comments
function loadRecentComments() {
    const container = document.getElementById('recent-comments-list');
    
    fetch(`{{ route('comments.get') }}?recent=true`)
    .then(response => response.json())
    .then(data => {
        if (data.comments && data.comments.length > 0) {
            container.innerHTML = data.comments.map(comment => createCommentHTML(comment, false)).join('');
        } else {
            container.innerHTML = `
                <div class="text-center py-8">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">No recent comments</h3>
                    <p class="mt-1 text-sm text-gray-500">No comments have been made recently.</p>
                </div>
            `;
        }
    })
    .catch(error => {
        console.error('Error loading recent comments:', error);
        container.innerHTML = `
            <div class="text-center py-8">
                <p class="text-sm text-red-500">Error loading comments. Please try again.</p>
            </div>
        `;
    });
}

// Load games for filter dropdown
function loadGamesForFilter() {
    const gameFilter = document.getElementById('game-filter');
    
    fetch('/api/games/user')
    .then(response => response.json())
    .then(data => {
        if (data.games && data.games.length > 0) {
            const options = data.games.map(game => 
                `<option value="${game.id}">${game.word} - ${game.created_at}</option>`
            ).join('');
            gameFilter.innerHTML = '<option value="">All Games</option>' + options;
        }
    })
    .catch(error => {
        console.error('Error loading games for filter:', error);
    });

    // Add event listener for filter change
    gameFilter.addEventListener('change', loadGameComments);
}

// Create comment HTML
function createCommentHTML(comment, isOwnComment) {
    return `
        <div class="bg-white border border-gray-200 rounded-lg p-4">
            <div class="flex items-start space-x-3">
                <img class="h-8 w-8 rounded-full object-cover" 
                     src="${comment.user.avatar_url || `https://ui-avatars.com/api/?name=${encodeURIComponent(comment.user.name)}&color=7C3AED&background=EBF4FF`}" 
                     alt="${comment.user.name}">
                <div class="flex-1 min-w-0">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-900">${comment.user.name}</p>
                            <p class="text-xs text-gray-500">${comment.created_at}</p>
                        </div>
                        ${isOwnComment ? `
                            <div class="flex items-center space-x-2">
                                <button onclick="editComment(${comment.id}, '${comment.content.replace(/'/g, "\\'")}')" 
                                        class="text-blue-600 hover:text-blue-900 text-sm">
                                    Edit
                                </button>
                                <button onclick="deleteComment(${comment.id})" 
                                        class="text-red-600 hover:text-red-900 text-sm">
                                    Delete
                                </button>
                            </div>
                        ` : ''}
                    </div>
                    <p class="text-sm text-gray-700 mt-1">${comment.content}</p>
                    ${comment.game ? `
                        <div class="mt-2">
                            <a href="/games/${comment.game.id}" class="text-xs text-blue-600 hover:text-blue-900">
                                View Game: ${comment.game.word}
                            </a>
                        </div>
                    ` : ''}
                </div>
            </div>
        </div>
    `;
}

// Edit comment
function editComment(commentId, content) {
    document.getElementById('edit-comment-id').value = commentId;
    document.getElementById('edit-comment-content').value = content;
    document.getElementById('edit-comment-modal').classList.remove('hidden');
}

// Close edit modal
function closeEditModal() {
    document.getElementById('edit-comment-modal').classList.add('hidden');
}

// Delete comment
function deleteComment(commentId) {
    if (confirm('Are you sure you want to delete this comment?')) {
        fetch(`/comments/${commentId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                loadMyComments();
            } else {
                alert('Error deleting comment');
            }
        })
        .catch(error => {
            console.error('Error deleting comment:', error);
            alert('Error deleting comment');
        });
    }
}

// Edit comment form submission
document.getElementById('edit-comment-form').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const commentId = document.getElementById('edit-comment-id').value;
    const content = document.getElementById('edit-comment-content').value;
    
    fetch(`/comments/${commentId}`, {
        method: 'PUT',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({ content: content })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            closeEditModal();
            loadMyComments();
        } else {
            alert('Error updating comment');
        }
    })
    .catch(error => {
        console.error('Error updating comment:', error);
        alert('Error updating comment');
    });
});

// Close modal when clicking outside
document.getElementById('edit-comment-modal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeEditModal();
    }
});
</script>
@endsection 