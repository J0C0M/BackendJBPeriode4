@extends('layouts.app')

@section('title', 'System Information - Admin')

@section('content')
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <div class="flex justify-between items-center mb-6">
                        <h1 class="text-2xl font-bold text-gray-900">System Information</h1>
                        <a href="{{ route('admin.dashboard') }}" class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">
                            Back to Dashboard
                        </a>
                    </div>

                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                        <!-- Application Information -->
                        <div class="bg-white border border-gray-200 rounded-lg p-6">
                            <h2 class="text-lg font-medium text-gray-900 mb-4 flex items-center">
                                <svg class="w-5 h-5 mr-2 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                                </svg>
                                Application
                            </h2>
                            <dl class="space-y-3">
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Application Name</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ config('app.name') }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Laravel Version</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ app()->version() }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Environment</dt>
                                    <dd class="mt-1">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ app()->environment('production') ? 'bg-red-100 text-red-800' : 'bg-green-100 text-green-800' }}">
                                        {{ ucfirst(app()->environment()) }}
                                    </span>
                                    </dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Debug Mode</dt>
                                    <dd class="mt-1">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ config('app.debug') ? 'bg-yellow-100 text-yellow-800' : 'bg-green-100 text-green-800' }}">
                                        {{ config('app.debug') ? 'Enabled' : 'Disabled' }}
                                    </span>
                                    </dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">URL</dt>
                                    <dd class="mt-1 text-sm text-gray-900 break-all">{{ config('app.url') }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Timezone</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ config('app.timezone') }}</dd>
                                </div>
                            </dl>
                        </div>

                        <!-- Server Information -->
                        <div class="bg-white border border-gray-200 rounded-lg p-6">
                            <h2 class="text-lg font-medium text-gray-900 mb-4 flex items-center">
                                <svg class="w-5 h-5 mr-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14M5 12a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v4a2 2 0 01-2 2M5 12a2 2 0 00-2 2v4a2 2 0 002 2h14a2 2 0 002-2v-4a2 2 0 00-2-2m-2-4h.01M17 16h.01"></path>
                                </svg>
                                Server
                            </h2>
                            <dl class="space-y-3">
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">PHP Version</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ PHP_VERSION }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Server Software</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ $_SERVER['SERVER_SOFTWARE'] ?? 'Unknown' }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Operating System</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ PHP_OS }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Memory Limit</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ ini_get('memory_limit') }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Max Execution Time</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ ini_get('max_execution_time') }}s</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Upload Max Size</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ ini_get('upload_max_filesize') }}</dd>
                                </div>
                            </dl>
                        </div>

                        <!-- Database Information -->
                        <div class="bg-white border border-gray-200 rounded-lg p-6">
                            <h2 class="text-lg font-medium text-gray-900 mb-4 flex items-center">
                                <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4m0 5c0 2.21-3.582 4-8 4s-8-1.79-8-4"></path>
                                </svg>
                                Database
                            </h2>
                            <dl class="space-y-3">
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Connection</dt>
                                    <dd class="mt-1">
                                        @try
                                        @php \DB::connection()->getPdo(); @endphp
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">Connected</span>
                                        @catch(\Exception $e)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">Failed</span>
                                        @endtry
                                    </dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Driver</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ config('database.default') }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Database Name</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ config('database.connections.' . config('database.default') . '.database') }}</dd>
                                </div>
                                @try
                                @php
                                    $version = \DB::select('SELECT sqlite_version() as version')[0]->version ?? 'Unknown';
                                @endphp
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Version</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ $version }}</dd>
                                </div>
                                @catch(\Exception $e)
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Version</dt>
                                    <dd class="mt-1 text-sm text-gray-900">Unable to determine</dd>
                                </div>
                                @endtry
                            </dl>
                        </div>

                        <!-- Cache & Session -->
                        <div class="bg-white border border-gray-200 rounded-lg p-6">
                            <h2 class="text-lg font-medium text-gray-900 mb-4 flex items-center">
                                <svg class="w-5 h-5 mr-2 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                                </svg>
                                Cache & Session
                            </h2>
                            <dl class="space-y-3">
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Cache Driver</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ config('cache.default') }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Session Driver</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ config('session.driver') }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Queue Driver</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ config('queue.default') }}</dd>
                                </div>
                            </dl>
                        </div>

                        <!-- Application Statistics -->
                        <div class="bg-white border border-gray-200 rounded-lg p-6">
                            <h2 class="text-lg font-medium text-gray-900 mb-4 flex items-center">
                                <svg class="w-5 h-5 mr-2 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2h-2a2 2 0 00-2-2z"></path>
                                </svg>
                                Statistics
                            </h2>
                            <dl class="space-y-3">
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Total Users</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ $stats['total_users'] ?? 0 }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Total Games</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ $stats['total_games'] ?? 0 }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Active Games</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ $stats['active_games'] ?? 0 }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Total Words</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ $stats['total_words'] ?? 0 }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Active Words</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ $stats['active_words'] ?? 0 }}</dd>
                                </div>
                            </dl>
                        </div>

                        <!-- Disk Space -->
                        <div class="bg-white border border-gray-200 rounded-lg p-6">
                            <h2 class="text-lg font-medium text-gray-900 mb-4 flex items-center">
                                <svg class="w-5 h-5 mr-2 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2h-2a2 2 0 00-2-2z"></path>
                                </svg>
                                Storage
                            </h2>
                            <dl class="space-y-3">
                                @php
                                    function formatBytes($bytes, $precision = 2) {
                                        $units = array('B', 'KB', 'MB', 'GB', 'TB');
                                        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
                                            $bytes /= 1024;
                                        }
                                        return round($bytes, $precision) . ' ' . $units[$i];
                                    }

                                    $totalSpace = disk_total_space(base_path());
                                    $freeSpace = disk_free_space(base_path());
                                    $usedSpace = $totalSpace - $freeSpace;
                                    $usagePercentage = ($usedSpace / $totalSpace) * 100;
                                @endphp
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Total Space</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ formatBytes($totalSpace) }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Used Space</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ formatBytes($usedSpace) }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Free Space</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ formatBytes($freeSpace) }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Usage</dt>
                                    <dd class="mt-1">
                                        <div class="flex items-center">
                                            <div class="w-full bg-gray-200 rounded-full h-2 mr-2">
                                                <div class="bg-{{ $usagePercentage > 90 ? 'red' : ($usagePercentage > 75 ? 'yellow' : 'green') }}-600 h-2 rounded-full" style="width: {{ $usagePercentage }}%"></div>
                                            </div>
                                            <span class="text-sm text-gray-900">{{ number_format($usagePercentage, 1) }}%</span>
                                        </div>
                                    </dd>
                                </div>
                            </dl>
                        </div>
                    </div>

                    <!-- System Health Checks -->
                    <div class="mt-8">
                        <h2 class="text-lg font-medium text-gray-900 mb-4">System Health Checks</h2>
                        <div class="bg-gray-50 rounded-lg p-6">
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                                @php
                                    $checks = [
                                        'PHP Extensions' => [
                                            'PDO' => extension_loaded('pdo'),
                                            'SQLite' => extension_loaded('pdo_sqlite'),
                                            'OpenSSL' => extension_loaded('openssl'),
                                            'Tokenizer' => extension_loaded('tokenizer'),
                                            'XML' => extension_loaded('xml'),
                                            'Ctype' => extension_loaded('ctype'),
                                            'JSON' => extension_loaded('json'),
                                            'BCMath' => extension_loaded('bcmath'),
                                            'Fileinfo' => extension_loaded('fileinfo'),
                                        ],
                                        'Directory Permissions' => [
                                            'storage/' => is_writable(storage_path()),
                                            'bootstrap/cache/' => is_writable(base_path('bootstrap/cache')),
                                            'public/' => is_writable(public_path()),
                                        ],
                                        'Configuration' => [
                                            'App Key Set' => !empty(config('app.key')),
                                            'Environment Set' => !empty(config('app.env')),
                                            'Database Configured' => !empty(config('database.connections.' . config('database.default') . '.database')),
                                        ]
                                    ];
                                @endphp

                                @foreach($checks as $category => $items)
                                    <div>
                                        <h3 class="text-sm font-medium text-gray-900 mb-3">{{ $category }}</h3>
                                        <div class="space-y-2">
                                            @foreach($items as $check => $status)
                                                <div class="flex items-center justify-between">
                                                    <span class="text-xs text-gray-600">{{ $check }}</span>
                                                    @if($status)
                                                        <svg class="h-4 w-4 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                                        </svg>
                                                    @else
                                                        <svg class="h-4 w-4 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                                        </svg>
                                                    @endif
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <!-- Quick Actions -->
                    <div class="mt-8">
                        <h2 class="text-lg font-medium text-gray-900 mb-4">Quick Actions</h2>
                        <div class="flex flex-wrap gap-3">
                            <form action="{{ route('admin.clear-cache') }}" method="POST" class="inline">
                                @csrf
                                <button type="submit" class="inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                                    </svg>
                                    Clear Cache
                                </button>
                            </form>

                            <button onclick="location.reload()" class="inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                                </svg>
                                Refresh Info
                            </button>

                            <a href="{{ route('admin.logs') }}" class="inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                                View Logs
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
