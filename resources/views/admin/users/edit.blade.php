@extends('layouts.app')

@section('title', 'Edit User - ' . $user->name)

@section('content')
    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <div class="flex justify-between items-center mb-6">
                        <h1 class="text-2xl font-bold text-gray-900">Edit User: {{ $user->name }}</h1>
                        <div class="flex space-x-3">
                            <a href="{{ route('admin.users.show', $user) }}" class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">
                                View User
                            </a>
                            <a href="{{ route('admin.users.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">
                                Back to Users
                            </a>
                        </div>
                    </div>

                    <form action="{{ route('admin.users.update', $user) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                        @csrf
                        @method('PUT')

                        <!-- Current Avatar Display -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Current Profile Picture</label>
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
                                    <p class="mt-1 text-xs text-gray-500">PNG, JPG, GIF up to 2MB (leave empty to keep current)</p>
                                    @if($user->avatar)
                                        <label class="mt-2 flex items-center">
                                            <input type="checkbox" name="remove_avatar" value="1" class="rounded border-gray-300 text-red-600 shadow-sm focus:border-red-300 focus:ring focus:ring-red-200">
                                            <span class="ml-2 text-sm text-red-600">Remove current avatar</span>
                                        </label>
                                    @endif
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
                            <p class="mt-1 text-xs text-gray-500">Username must be unique and contain only letters, numbers, and underscores</p>
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

                        <!-- Password Change -->
                        <div class="border-t border-gray-200 pt-6">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Change Password</h3>
                            <p class="text-sm text-gray-600 mb-4">Leave password fields empty to keep current password</p>

                            <div class="space-y-4">
                                <div>
                                    <label for="password" class="block text-sm font-medium text-gray-700">New Password</label>
                                    <input type="password" name="password" id="password"
                                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm @error('password') border-red-300 @enderror">
                                    <p class="mt-1 text-xs text-gray-500">Password must be at least 8 characters long</p>
                                    @error('password')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="password_confirmation" class="block text-sm font-medium text-gray-700">Confirm New Password</label>
                                    <input type="password" name="password_confirmation" id="password_confirmation"
                                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                </div>
                            </div>
                        </div>

                        <!-- User Settings -->
                        <div class="border-t border-gray-200 pt-6">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">User Settings</h3>

                            <!-- Email Verification -->
                            <div class="flex items-center justify-between mb-4">
                                <div>
                                    <label for="email_verified" class="text-sm font-medium text-gray-700">Email Verified</label>
                                    <p class="text-sm text-gray-500">
                                        @if($user->email_verified_at)
                                            Currently verified on {{ $user->email_verified_at->format('M j, Y') }}
                                        @else
                                            Email is not verified
                                        @endif
                                    </p>
                                </div>
                                <input type="checkbox" name="email_verified" id="email_verified" value="1"
                                       {{ $user->email_verified_at ? 'checked' : '' }}
                                       class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300 rounded">
                            </div>

                            <!-- Admin Status -->
                            <div class="flex items-center justify-between">
                                <div>
                                    <label for="is_admin" class="text-sm font-medium text-gray-700">Administrator</label>
                                    <p class="text-sm text-gray-500">Grant administrator privileges to this user</p>
                                    @if($user->id === auth()->id())
                                        <p class="text-xs text-yellow-600">You cannot remove your own admin privileges</p>
                                    @endif
                                </div>
                                <input type="checkbox" name="is_admin" id="is_admin" value="1"
                                       {{ $user->is_admin ? 'checked' : '' }}
                                       {{ $user->id === auth()->id() ? 'disabled' : '' }}
                                       class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300 rounded">
                            </div>
                        </div>

                        <!-- Account Statistics (Read-only) -->
                        <div class="border-t border-gray-200 pt-6">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Account Statistics</h3>
                            <div class="bg-gray-50 rounded-lg p-4">
                                <div class="grid grid-cols-2 gap-4">
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
                                </div>
                                <div class="mt-4 text-center">
                                    <p class="text-sm text-gray-500">
                                        Total Games: {{ $user->games_won + $user->games_lost + $user->games_drawn }}
                                        • Member since {{ $user->created_at->format('F j, Y') }}
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div class="flex justify-end space-x-3 pt-6 border-t border-gray-200">
                            <a href="{{ route('admin.users.show', $user) }}" class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50">
                                Cancel
                            </a>
                            <button type="submit" class="px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700">
                                Update User
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
