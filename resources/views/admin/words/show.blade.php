@extends('layouts.app')

@section('title', 'Word Details')

@section('content')
<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8 flex items-center">
            <a href="{{ route('admin.words.index') }}" class="text-gray-400 hover:text-gray-600 mr-4">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                </svg>
            </a>
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Word Details</h1>
                <p class="mt-2 text-gray-600">View and manage word information</p>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm border border-gray-200">
            <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
                <h2 class="text-lg font-medium text-gray-900">Word: <span class="font-mono text-xl">{{ strtoupper($word->word) }}</span></h2>
                <a href="{{ route('admin.words.edit', $word) }}" class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700">Edit</a>
            </div>
            <div class="p-6 space-y-4">
                <div>
                    <span class="text-sm font-medium text-gray-500">Definition:</span>
                    <p class="text-gray-900">{{ $word->definition ?? 'No definition provided.' }}</p>
                </div>
                <div class="flex space-x-4">
                    <div>
                        <span class="text-sm font-medium text-gray-500">Status:</span>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $word->status === 'active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                            {{ ucfirst($word->status) }}
                        </span>
                    </div>
                    <div>
                        <span class="text-sm font-medium text-gray-500">Difficulty:</span>
                        <span class="text-gray-900">{{ $word->difficulty_score ?? 0 }}/5</span>
                    </div>
                    <div>
                        <span class="text-sm font-medium text-gray-500">Length:</span>
                        <span class="text-gray-900">{{ strlen($word->word) }} letters</span>
                    </div>
                </div>
                <div>
                    <span class="text-sm font-medium text-gray-500">Categories:</span>
                    <span class="text-gray-900">{{ $word->categories ?? 'None' }}</span>
                </div>
                <div>
                    <span class="text-sm font-medium text-gray-500">Hints:</span>
                    <span class="text-gray-900">{{ $word->hints ?? 'None' }}</span>
                </div>
                <div>
                    <span class="text-sm font-medium text-gray-500">Usage Count:</span>
                    <span class="text-gray-900">{{ $word->usage_count ?? 0 }}</span>
                </div>
                <div>
                    <span class="text-sm font-medium text-gray-500">Last Used:</span>
                    <span class="text-gray-900">{{ $word->last_used_at ? $word->last_used_at->format('M d, Y') : 'Never' }}</span>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 