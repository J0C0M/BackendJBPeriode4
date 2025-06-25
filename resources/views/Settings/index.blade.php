@extends('layouts.app')

@section('title', 'Settings')

@section('content')
    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <h1 class="text-2xl font-bold text-gray-900 mb-6">Account Settings</h1>

                    <div class="space-y-8">
                        <!-- Profile Information -->
                        <div>
                            <h2 class="text-lg font-medium text-gray-900 mb-4">Profile Information</h2>
                            <form action="{{ route('settings.profile') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                                @csrf

                                <!-- Avatar -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Profile Picture</label>
                                    <div class="flex items-center space-x-6">
                                        @if($user->avatar)
                                            <img class="h-20 w-20 rounded-full object-cover" src="{{ Storage::url($user->avatar) }}" alt="{{ $user->name }}">
                                        @else
                                            <div class="h-20 w-20 rounded-full bg-indigo-500 flex items-center justify-center text-white text-2xl">
                                                {{ strtoupper(substr($user->name, 0, 1)) }}
                                            </div>
                                        @endif
                                        <div>
                                            <input type="file" name="avatar" id="avatar" accept="image/*" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-medium file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                                            <p class="mt-1 text-xs text-gray-500">PNG, JPG, GIF up to 2MB</p>
                                        </div>
                                    </div>
                                    @error('avatar')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Name -->
                                <div>
                                    <label for="name" class="block text-sm font-medium text-gray-700">Full Name</label>
                                    <input type="text" name="name" id="name" value="{{ old('name', $user->name) }}" required
                                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm @error('name') border-red-300 @enderror">
                                    @error('name')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Username -->
                                <div>
                                    <label for="username" class="block text-sm font-medium text-gray-700">Username</label>
                                    <input type="text" name="username" id="username" value="{{ old('username', $user->username) }}" required
                                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm @error('username') border-red-300 @enderror">
                                    @error('username')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Email -->
                                <div>
                                    <label for="email" class="block text-sm font-medium text-gray-700">Email Address</label>
                                    <input type="email" name="email" id="email" value="{{ old('email', $user->email) }}" required
                                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm @error('email') border-red-300 @enderror">
                                    @error('email')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div class="flex justify-end">
                                    <button type="submit" class="px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700">
                                        Update Profile
                                    </button>
                                </div>
                            </form>
                        </div>

                        <div class="border-t border-gray-200"></div>

                        <!-- Game Settings -->
                        <div>
                            <h2 class="text-lg font-medium text-gray-900 mb-4">Game Preferences</h2>
                            <form action="{{ route('settings.update') }}" method="POST" class="space-y-6">
                                @csrf

                                <!-- Notifications -->
                                <div>
                                    <label class="text-base font-medium text-gray-900">Email Notifications</label>
                                    <p class="text-sm leading-5 text-gray-500">Choose what email notifications you'd like to receive</p>
                                    <fieldset class="mt-4">
                                        <legend class="sr-only">Email notifications</legend>
                                        <div class="space-y-4">
                                            <div class="flex items-start">
                                                <div class="flex items-center h-5">
                                                    <input id="game_invites" name="notifications[]" type="checkbox" value="game_invites"
                                                           class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300 rounded"
                                                        {{ in_array('game_invites', $user->settings['notifications'] ?? []) ? 'checked' : '' }}>
                                                </div>
                                                <div class="ml-3 text-sm">
                                                    <label for="game_invites" class="font-medium text-gray-700">Game Invitations</label>
                                                    <p class="text-gray-500">Get notified when someone invites you to a game</p>
                                                </div>
                                            </div>
                                            <div class="flex items-start">
                                                <div class="flex items-center h-5">
                                                    <input id="game_updates" name="notifications[]" type="checkbox" value="game_updates"
                                                           class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300 rounded"
                                                        {{ in_array('game_updates', $user->settings['notifications'] ?? []) ? 'checked' : '' }}>
                                                </div>
                                                <div class="ml-3 text-sm">
                                                    <label for="game_updates" class="font-medium text-gray-700">Game Updates</label>
                                                    <p class="text-gray-500">Get notified when it's your turn to play</p>
                                                </div>
                                            </div>
                                            <div class="flex items-start">
                                                <div class="flex items-center h-5">
                                                    <input id="friend_requests" name="notifications[]" type="checkbox" value="friend_requests"
                                                           class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300 rounded"
                                                        {{ in_array('friend_requests', $user->settings['notifications'] ?? []) ? 'checked' : '' }}>
                                                </div>
                                                <div class="ml-3 text-sm">
                                                    <label for="friend_requests" class="font-medium text-gray-700">Friend Requests</label>
                                                    <p class="text-gray-500">Get notified when someone sends you a friend request</p>
                                                </div>
                                            </div>
                                        </div>
                                    </fieldset>
                                </div>

                                <!-- Privacy Settings -->
                                <div>
                                    <label class="text-base font-medium text-gray-900">Privacy Settings</label>
                                    <div class="mt-4 space-y-4">
                                        <div class="flex items-center justify-between">
                                            <div class="flex-1">
                                                <label for="profile_public" class="text-sm font-medium text-gray-700">Public Profile</label>
                                                <p class="text-sm text-gray-500">Allow other users to view your profile and game statistics</p>
                                            </div>
                                            <input type="checkbox" name="profile_public" id="profile_public"
                                                   class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300 rounded"
                                                {{ ($user->settings['profile_public'] ?? true) ? 'checked' : '' }}>
                                        </div>
                                        <div class="flex items-center justify-between">
                                            <div class="flex-1">
                                                <label for="allow_random_games" class="text-sm font-medium text-gray-700">Random Game Matching</label>
                                                <p class="text-sm text-gray-500">Allow the system to match you with random players</p>
                                            </div>
                                            <input type="checkbox" name="allow_random_games" id="allow_random_games"
                                                   class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300 rounded"
                                                {{ ($user->settings['allow_random_games'] ?? true) ? 'checked' : '' }}>
                                        </div>
                                    </div>
                                </div>

                                <div class="flex justify-end">
                                    <button type="submit" class="px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700">
                                        Save Preferences
                                    </button>
                                </div>
                            </form>
                        </div>

                        <div class="border-t border-gray-200"></div>

                        <!-- Change Password -->
                        <div>
                            <h2 class="text-lg font-medium text-gray-900 mb-4">Change Password</h2>
                            <form action="{{ route('settings.password') }}" method="POST" class="space-y-6">
                                @csrf

                                <div>
                                    <label for="current_password" class="block text-sm font-medium text-gray-700">Current Password</label>
                                    <input type="password" name="current_password" id="current_password" required
                                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm @error('current_password') border-red-300 @enderror">
                                    @error('current_password')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="password" class="block text-sm font-medium text-gray-700">New Password</label>
                                    <input type="password" name="password" id="password" required
                                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm @error('password') border-red-300 @enderror">
                                    @error('password')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="password_confirmation" class="block text-sm font-medium text-gray-700">Confirm New Password</label>
                                    <input type="password" name="password_confirmation" id="password_confirmation" required
                                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                </div>

                                <div class="flex justify-end">
                                    <button type="submit" class="px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700">
                                        Change Password
                                    </button>
                                </div>
                            </form>
                        </div>

                        <div class="border-t border-gray-200"></div>

                        <!-- Account Statistics -->
                        <div>
                            <h2 class="text-lg font-medium text-gray-900 mb-4">Account Statistics</h2>
                            <div class="bg-gray-50 rounded-lg p-6">
                                <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
                                    <div class="text-center">
                                        <p class="text-2xl font-bold text-gray-900">{{ $user->games_won }}</p>
                                        <p class="text-sm text-gray-500">Games Won</p>
                                    </div>
                                    <div class="text-center">
                                        <p class="text-2xl font-bold text-gray-900">{{ $user->games_lost }}</p>
                                        <p class="text-sm text-gray-500">Games Lost</p>
                                    </div>
                                    <div class="text-center">
                                        <p class="text-2xl font-bold text-gray-900">{{ $user->games_drawn }}</p>
                                        <p class="text-sm text-gray-500">Games Drawn</p>
                                    </div>
                                    <div class="text-center">
                                        <p class="text-2xl font-bold text-gray-900">{{ number_format($user->getWinRateAttribute(), 1) }}%</p>
                                        <p class="text-sm text-gray-500">Win Rate</p>
                                    </div>
                                </div>
                                <div class="mt-4 text-center">
                                    <p class="text-sm text-gray-500">
                                        Member since {{ $user->created_at->format('F j, Y') }}
                                        @if($user->last_login_at)
                                            • Last login {{ $user->last_login_at->diffForHumans() }}
                                        @endif
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
