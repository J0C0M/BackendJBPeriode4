@extends('layouts.app')

@section('title', 'User Details - ' . $user->name)

@section('content')
    <div class="py-12">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">
            <!-- Header -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 bg-white border-b border-gray-200">
                    <div class="flex justify-between items-center">
                        <div class="flex items-center">
                            @if($user->avatar)
                                <img class="h-16 w-16 rounded-full object-cover" src="{{ Storage::url($user->avatar) }}" alt="{{ $user->name }}">
                            @else
                                <div class="h-16 w-16 rounded-full bg-indigo-500 flex items-center justify-center text-white text-2xl">
                                    {{ strtoupper(substr($user->name, 0, 1)) }}
                                </div>
                            @endif
                            <div class="ml-4">
                                <h1 class="text-2xl font-bold text-gray-900">{{ $user->name }}</h1>
                                <p class="text-sm text-gray-500">{{ '@' . $user->username }}</p>
                                <p class="text-xs text-gray-400">User ID: {{ $user->id }}</p>
                            </div>
                        </div>
                        <div class="flex space-x-3">
                            <a href="{{ route('admin.users.edit', $user) }}" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700">
                                Edit User
                            </a>
                            <a href="{{ route('admin.users.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">
                                Back to Users
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- User Information -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- Basic Info -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6 bg-white border-b border-gray-200">
                            <h2 class="text-lg font-medium text-gray-900 mb-4">Basic Information</h2>
                            <dl class="grid grid-cols-1 gap-x-4 gap-y-6 sm:grid-cols-2">
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Full Name</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ $user->name }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Username</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ '@' . $user->username }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Email</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ $user->email }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Email Status</dt>
                                    <dd class="mt-1">
                                        @if($user->email_verified_at)
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            Verified on {{ $user->email_verified_at->format('M j, Y') }}
                                        </span>
                                        @else
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                            Unverified
                                        </span>
                                        @endif
                                    </dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Account Type</dt>
                                    <dd class="mt-1">
                                        @if($user->is_admin)
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                                            Administrator
                                        </span>
                                        @else
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                            Regular User
                                        </span>
                                        @endif
                                    </dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Member Since</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ $user->created_at->format('F j, Y') }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Last Login</dt>
                                    <dd class="mt-1 text-sm text-gray-900">
                                        @if($user->last_login_at)
                                            {{ $user->last_login_at->format('F j, Y \a\t g:i A') }}
                                            <span class="text-gray-500">({{ $user->last_login_at->diffForHumans() }})</span>
                                        @else
                                            Never logged in
                                        @endif
                                    </dd>
                                </div>
                            </dl>
                        </div>
                    </div>

                    <!-- Recent Games -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6 bg-white border-b border-gray-200">
                            <h2 class="text-lg font-medium text-gray-900 mb-4">Recent Games</h2>
                            @if($recentGames->count() > 0)
                                <div class="overflow-x-auto">
                                    <table class="min-w-full divide-y divide-gray-200">
                                        <thead class="bg-gray-50">
                                        <tr>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Opponent</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Result</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Word</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                                        </tr>
                                        </thead>
                                        <tbody class="bg-white divide-y divide-gray-200">
                                        @foreach($recentGames as $game)
                                            @php $opponent = $game->getOpponent($user); @endphp
                                            <tr>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                    {{ $opponent->name }}
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    @if($game->status === 'completed')
                                                        @if($game->winner_id === $user->id)
                                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">Won</span>
                                                        @elseif($game->result === 'draw')
                                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">Draw</span>
                                                        @else
                                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">Lost</span>
                                                        @endif
                                                    @else
                                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">{{ ucfirst($game->status) }}</span>
                                                    @endif
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                    @if($game->status === 'completed')
                                                        <span class="font-mono">{{ $game->word->word }}</span>
                                                    @else
                                                        <span class="text-gray-500">Hidden</span>
                                                    @endif
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                    {{ $game->created_at->format('M j, Y') }}
                                                </td>
                                            </tr>
                                        @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <p class="text-gray-500">No games played yet.</p>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Statistics & Actions -->
                <div class="space-y-6">
                    <!-- Statistics -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6 bg-white border-b border-gray-200">
                            <h2 class="text-lg font-medium text-gray-900 mb-4">Statistics</h2>
                            <dl class="space-y-4">
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Games Won</dt>
                                    <dd class="mt-1 text-2xl font-semibold text-green-600">{{ $user->games_won }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Games Lost</dt>
                                    <dd class="mt-1 text-2xl font-semibold text-red-600">{{ $user->games_lost }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Games Drawn</dt>
                                    <dd class="mt-1 text-2xl font-semibold text-yellow-600">{{ $user->games_drawn }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Win Rate</dt>
                                    <dd class="mt-1 text-2xl font-semibold text-indigo-600">{{ number_format($user->getWinRateAttribute(), 1) }}%</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Total Games</dt>
                                    <dd class="mt-1 text-2xl font-semibold text-gray-900">{{ $user->games_won + $user->games_lost + $user->games_drawn }}</dd>
                                </div>
                            </dl>
                        </div>
                    </div>

                    <!-- Admin Actions -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6 bg-white border-b border-gray-200">
                            <h2 class="text-lg font-medium text-gray-900 mb-4">Admin Actions</h2>
                            <div class="space-y-3">
                                @if(!$user->email_verified_at)
                                    <form action="{{ route('admin.users.verify-email', $user) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="w-full px-4 py-2 bg-green-600 text-white text-sm font-medium rounded hover:bg-green-700">
                                            Verify Email
                                        </button>
                                    </form>
                                @endif

                                @if(!$user->is_admin)
                                    <form action="{{ route('admin.users.make-admin', $user) }}" method="POST" onsubmit="return confirm('Are you sure you want to make this user an admin?')">
                                        @csrf
                                        <button type="submit" class="w-full px-4 py-2 bg-purple-600 text-white text-sm font-medium rounded hover:bg-purple-700">
                                            Make Admin
                                        </button>
                                    </form>
                                @elseif($user->id !== auth()->id())
                                    <form action="{{ route('admin.users.remove-admin', $user) }}" method="POST" onsubmit="return confirm('Are you sure you want to remove admin privileges?')">
                                        @csrf
                                        <button type="submit" class="w-full px-4 py-2 bg-yellow-600 text-white text-sm font-medium rounded hover:bg-yellow-700">
                                            Remove Admin
                                        </button>
                                    </form>
                                @endif

                                <form action="{{ route('admin.users.reset-password', $user) }}" method="POST" onsubmit="return confirm('Are you sure you want to reset this user\'s password? They will receive an email with reset instructions.')">
                                    @csrf
                                    <button type="submit" class="w-full px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded hover:bg-blue-700">
                                        Reset Password
                                    </button>
                                </form>

                                @if($user->id !== auth()->id())
                                    <form action="{{ route('admin.users.destroy', $user) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this user? This action cannot be undone and will delete all their games and data.')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="w-full px-4 py-2 bg-red-600 text-white text-sm font-medium rounded hover:bg-red-700">
                                            Delete User
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
