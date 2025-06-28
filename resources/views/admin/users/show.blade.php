@extends('layouts.app')

@section('title', 'User Details')

@section('content')
<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <a href="{{ route('admin.users.index') }}" class="text-gray-400 hover:text-gray-600 mr-4">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                        </svg>
                    </a>
                    <div>
                        <h1 class="text-3xl font-bold text-gray-900">User Details</h1>
                        <p class="mt-2 text-gray-600">View and manage user information</p>
                    </div>
                </div>
                <div class="flex items-center space-x-3">
                    <a href="{{ route('admin.users.edit', $user) }}" class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                        Edit User
                    </a>
                    <button onclick="resetPassword({{ $user->id }})" class="bg-yellow-600 text-white px-4 py-2 rounded-md hover:bg-yellow-700 focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:ring-offset-2">
                        Reset Password
                    </button>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- User Information -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Basic Information -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h2 class="text-lg font-medium text-gray-900">Basic Information</h2>
                    </div>
                    <div class="p-6">
                        <div class="flex items-center space-x-6 mb-6">
                            <img class="h-20 w-20 rounded-full object-cover border-2 border-gray-200" 
                                 src="{{ $user->avatar_url ?? 'https://ui-avatars.com/api/?name=' . urlencode($user->name) . '&color=7C3AED&background=EBF4FF' }}" 
                                 alt="{{ $user->name }}">
                            <div>
                                <h3 class="text-xl font-semibold text-gray-900">{{ $user->name }}</h3>
                                <p class="text-gray-500">@{{ $user->username }}</p>
                                <div class="flex items-center mt-2 space-x-4">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $user->role === 'admin' ? 'bg-purple-100 text-purple-800' : 'bg-gray-100 text-gray-800' }}">
                                        {{ ucfirst($user->role) }}
                                    </span>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                        {{ $user->status === 'active' ? 'bg-green-100 text-green-800' : 
                                           ($user->status === 'inactive' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                                        {{ ucfirst($user->status) }}
                                    </span>
                                </div>
                            </div>
                        </div>
                        
                        <dl class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Email Address</dt>
                                <dd class="text-sm text-gray-900">{{ $user->email }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Email Verified</dt>
                                <dd class="text-sm text-gray-900">
                                    @if($user->email_verified_at)
                                        <span class="text-green-600">✓ Verified</span>
                                    @else
                                        <span class="text-red-600">✗ Not verified</span>
                                    @endif
                                </dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Member Since</dt>
                                <dd class="text-sm text-gray-900">{{ $user->created_at->format('M d, Y H:i') }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Last Login</dt>
                                <dd class="text-sm text-gray-900">{{ $user->last_login_at ? $user->last_login_at->format('M d, Y H:i') : 'Never' }}</dd>
                            </div>
                            @if($user->bio)
                            <div class="sm:col-span-2">
                                <dt class="text-sm font-medium text-gray-500">Bio</dt>
                                <dd class="text-sm text-gray-900">{{ $user->bio }}</dd>
                            </div>
                            @endif
                        </dl>
                    </div>
                </div>

                <!-- Game Statistics -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h2 class="text-lg font-medium text-gray-900">Game Statistics</h2>
                    </div>
                    <div class="p-6">
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
                            <div class="text-center">
                                <div class="text-2xl font-bold text-blue-600">{{ $user->games()->count() }}</div>
                                <div class="text-sm text-gray-500">Total Games</div>
                            </div>
                            <div class="text-center">
                                <div class="text-2xl font-bold text-green-600">{{ $user->games()->where('status', 'completed')->count() }}</div>
                                <div class="text-sm text-gray-500">Completed</div>
                            </div>
                            <div class="text-center">
                                <div class="text-2xl font-bold text-yellow-600">{{ $user->games()->where('status', 'active')->count() }}</div>
                                <div class="text-sm text-gray-500">Active</div>
                            </div>
                            <div class="text-center">
                                <div class="text-2xl font-bold text-purple-600">{{ $user->friends()->count() }}</div>
                                <div class="text-sm text-gray-500">Friends</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Recent Games -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                    <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
                        <h2 class="text-lg font-medium text-gray-900">Recent Games</h2>
                        <a href="{{ route('admin.users.game-history', $user) }}" class="text-blue-600 hover:text-blue-900 text-sm">View All</a>
                    </div>
                    <div class="p-6">
                        @if($user->games()->count() > 0)
                            <div class="space-y-4">
                                @foreach($user->games()->latest()->take(5)->get() as $game)
                                <div class="flex items-center justify-between p-4 border border-gray-200 rounded-lg">
                                    <div class="flex items-center space-x-4">
                                        <div class="w-10 h-10 bg-gray-100 rounded-full flex items-center justify-center">
                                            <span class="text-sm font-medium text-gray-600">{{ strtoupper($game->word) }}</span>
                                        </div>
                                        <div>
                                            <p class="text-sm font-medium text-gray-900">Word: {{ $game->word }}</p>
                                            <p class="text-xs text-gray-500">{{ $game->created_at->format('M d, Y H:i') }}</p>
                                        </div>
                                    </div>
                                    <div class="flex items-center space-x-2">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                            {{ $game->status === 'completed' ? 'bg-green-100 text-green-800' : 
                                               ($game->status === 'active' ? 'bg-yellow-100 text-yellow-800' : 'bg-gray-100 text-gray-800') }}">
                                            {{ ucfirst($game->status) }}
                                        </span>
                                        <a href="{{ route('games.show', $game) }}" class="text-blue-600 hover:text-blue-900 text-sm">View</a>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-8">
                                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <h3 class="mt-2 text-sm font-medium text-gray-900">No games yet</h3>
                                <p class="mt-1 text-sm text-gray-500">This user hasn't played any games.</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="space-y-6">
                <!-- Quick Actions -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h2 class="text-lg font-medium text-gray-900">Quick Actions</h2>
                    </div>
                    <div class="p-6 space-y-3">
                        <a href="{{ route('admin.users.game-history', $user) }}" class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 rounded-md">
                            View Game History
                        </a>
                        <a href="{{ route('admin.users.friendships', $user) }}" class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 rounded-md">
                            View Friendships
                        </a>
                        <button onclick="resetPassword({{ $user->id }})" class="block w-full text-left px-4 py-2 text-sm text-yellow-700 hover:bg-yellow-50 rounded-md">
                            Reset Password
                        </button>
                        @if($user->status === 'active')
                            <button onclick="deactivateUser({{ $user->id }})" class="block w-full text-left px-4 py-2 text-sm text-yellow-700 hover:bg-yellow-50 rounded-md">
                                Deactivate User
                            </button>
                        @else
                            <button onclick="activateUser({{ $user->id }})" class="block w-full text-left px-4 py-2 text-sm text-green-700 hover:bg-green-50 rounded-md">
                                Activate User
                            </button>
                        @endif
                        <button onclick="deleteUser({{ $user->id }})" class="block w-full text-left px-4 py-2 text-sm text-red-700 hover:bg-red-50 rounded-md">
                            Delete User
                        </button>
                    </div>
                </div>

                <!-- Account Status -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h2 class="text-lg font-medium text-gray-900">Account Status</h2>
                    </div>
                    <div class="p-6 space-y-4">
                        <div class="flex items-center justify-between">
                            <span class="text-sm font-medium text-gray-700">Account Status</span>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                {{ $user->status === 'active' ? 'bg-green-100 text-green-800' : 
                                   ($user->status === 'inactive' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                                {{ ucfirst($user->status) }}
                            </span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-sm font-medium text-gray-700">Email Verified</span>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $user->email_verified_at ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                {{ $user->email_verified_at ? 'Yes' : 'No' }}
                            </span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-sm font-medium text-gray-700">Role</span>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $user->role === 'admin' ? 'bg-purple-100 text-purple-800' : 'bg-gray-100 text-gray-800' }}">
                                {{ ucfirst($user->role) }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Reset Password Modal -->
<div id="reset-password-modal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3 text-center">
            <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-yellow-100">
                <svg class="h-6 w-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                </svg>
            </div>
            <h3 class="text-lg font-medium text-gray-900 mt-4">Reset Password</h3>
            <div class="mt-2 px-7 py-3">
                <p class="text-sm text-gray-500">Are you sure you want to reset this user's password? They will receive an email with a new password.</p>
            </div>
            <div class="flex justify-center space-x-4 mt-4">
                <button onclick="closeResetModal()" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400">
                    Cancel
                </button>
                <form id="reset-password-form" method="POST" class="inline">
                    @csrf
                    <button type="submit" class="px-4 py-2 bg-yellow-600 text-white rounded-md hover:bg-yellow-700">
                        Reset Password
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Delete User Modal -->
<div id="delete-user-modal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3 text-center">
            <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100">
                <svg class="h-6 w-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                </svg>
            </div>
            <h3 class="text-lg font-medium text-gray-900 mt-4">Delete User</h3>
            <div class="mt-2 px-7 py-3">
                <p class="text-sm text-gray-500">Are you sure you want to delete this user? This action cannot be undone.</p>
            </div>
            <div class="flex justify-center space-x-4 mt-4">
                <button onclick="closeDeleteModal()" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400">
                    Cancel
                </button>
                <form id="delete-user-form" method="POST" class="inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700">
                        Delete User
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
// Reset password functionality
function resetPassword(userId) {
    document.getElementById('reset-password-form').action = `/admin/users/${userId}/reset-password`;
    document.getElementById('reset-password-modal').classList.remove('hidden');
}

function closeResetModal() {
    document.getElementById('reset-password-modal').classList.add('hidden');
}

// Delete user functionality
function deleteUser(userId) {
    document.getElementById('delete-user-form').action = `/admin/users/${userId}`;
    document.getElementById('delete-user-modal').classList.remove('hidden');
}

function closeDeleteModal() {
    document.getElementById('delete-user-modal').classList.add('hidden');
}

// Activate/Deactivate user functionality
function activateUser(userId) {
    if (confirm('Are you sure you want to activate this user?')) {
        // Implementation for activating user
        alert('User activated successfully');
        location.reload();
    }
}

function deactivateUser(userId) {
    if (confirm('Are you sure you want to deactivate this user?')) {
        // Implementation for deactivating user
        alert('User deactivated successfully');
        location.reload();
    }
}

// Close modals when clicking outside
document.getElementById('reset-password-modal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeResetModal();
    }
});

document.getElementById('delete-user-modal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeDeleteModal();
    }
});
</script>
@endsection 