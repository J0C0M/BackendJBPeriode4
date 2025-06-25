@extends('layouts.app')

@section('title', 'Create User - Admin')

@section('content')
    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <div class="flex justify-between items-center mb-6">
                        <h1 class="text-2xl font-bold text-gray-900">Create New User</h1>
                        <a href="{{ route('admin.users.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">
                            Back to Users
                        </a>
                    </div>

                    <form action="{{ route('admin.users.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                        @csrf

                        <!-- Avatar -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Profile Picture</label>
                            <div class="flex items-center space-x-6">
                                <div id="avatar-preview" class="h-20 w-20 rounded-full bg-gray-200 flex items-center justify-center text-gray-400 text-2xl hidden">
                                    <span id="avatar-initial">?</span>
                                </div>
                                <div>
                                    <input type="file" name="avatar" id="avatar" accept="image/*" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-medium file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                                    <p class="mt-1 text-xs text-gray-500">PNG, JPG, GIF up to 2MB (optional)</p>
                                </div>
                            </div>
                            @error('avatar')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Name -->
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700">Full Name</label>
                            <input type="text" name="name" id="name" value="{{ old('name') }}" required
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm @error('name') border-red-300 @enderror">
                            @error('name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Username -->
                        <div>
                            <label for="username" class="block text-sm font-medium text-gray-700">Username</label>
                            <input type="text" name="username" id="username" value="{{ old('username') }}" required
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm @error('username') border-red-300 @enderror">
                            <p class="mt-1 text-xs text-gray-500">Username must be unique and contain only letters, numbers, and underscores</p>
                            @error('username')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Email -->
                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700">Email Address</label>
                            <input type="email" name="email" id="email" value="{{ old('email') }}" required
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm @error('email') border-red-300 @enderror">
                            @error('email')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Password -->
                        <div>
                            <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
                            <input type="password" name="password" id="password" required
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm @error('password') border-red-300 @enderror">
                            <p class="mt-1 text-xs text-gray-500">Password must be at least 8 characters long</p>
                            @error('password')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Confirm Password -->
                        <div>
                            <label for="password_confirmation" class="block text-sm font-medium text-gray-700">Confirm Password</label>
                            <input type="password" name="password_confirmation" id="password_confirmation" required
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                        </div>

                        <!-- User Settings -->
                        <div class="border-t border-gray-200 pt-6">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">User Settings</h3>

                            <!-- Email Verification -->
                            <div class="flex items-center justify-between mb-4">
                                <div>
                                    <label for="email_verified" class="text-sm font-medium text-gray-700">Email Verified</label>
                                    <p class="text-sm text-gray-500">Mark email as verified (user won't need to verify)</p>
                                </div>
                                <input type="checkbox" name="email_verified" id="email_verified" value="1" checked
                                       class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300 rounded">
                            </div>

                            <!-- Admin Status -->
                            <div class="flex items-center justify-between mb-4">
                                <div>
                                    <label for="is_admin" class="text-sm font-medium text-gray-700">Administrator</label>
                                    <p class="text-sm text-gray-500">Grant administrator privileges to this user</p>
                                </div>
                                <input type="checkbox" name="is_admin" id="is_admin" value="1"
                                       class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300 rounded">
                            </div>

                            <!-- Send Welcome Email -->
                            <div class="flex items-center justify-between">
                                <div>
                                    <label for="send_welcome_email" class="text-sm font-medium text-gray-700">Send Welcome Email</label>
                                    <p class="text-sm text-gray-500">Send a welcome email with login instructions</p>
                                </div>
                                <input type="checkbox" name="send_welcome_email" id="send_welcome_email" value="1" checked
                                       class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300 rounded">
                            </div>
                        </div>

                        <div class="flex justify-end space-x-3 pt-6 border-t border-gray-200">
                            <a href="{{ route('admin.users.index') }}" class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50">
                                Cancel
                            </a>
                            <button type="submit" class="px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700">
                                Create User
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const nameInput = document.getElementById('name');
            const avatarInput = document.getElementById('avatar');
            const avatarPreview = document.getElementById('avatar-preview');
            const avatarInitial = document.getElementById('avatar-initial');

            // Show avatar preview based on name
            nameInput.addEventListener('input', function() {
                if (this.value.trim()) {
                    avatarInitial.textContent = this.value.trim().charAt(0).toUpperCase();
                    avatarPreview.classList.remove('hidden');
                } else {
                    avatarPreview.classList.add('hidden');
                }
            });

            // Handle file upload preview
            avatarInput.addEventListener('change', function() {
                if (this.files && this.files[0]) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        avatarPreview.innerHTML = `<img src="${e.target.result}" class="h-20 w-20 rounded-full object-cover">`;
                        avatarPreview.classList.remove('hidden');
                    };
                    reader.readAsDataURL(this.files[0]);
                }
            });
        });
    </script>
@endsection
