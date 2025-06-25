@extends('layouts.app')

@section('title', 'System Logs - Admin')

@section('content')
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <div class="flex justify-between items-center mb-6">
                        <h1 class="text-2xl font-bold text-gray-900">System Logs</h1>
                        <div class="flex space-x-3">
                            <button onclick="location.reload()" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                                </svg>
                                Refresh
                            </button>
                            <a href="{{ route('admin.system-info') }}" class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">
                                System Info
                            </a>
                        </div>
                    </div>

                    <!-- Log File Selector -->
                    <div class="mb-6">
                        <div class="flex items-center space-x-4">
                            <label for="log-file" class="text-sm font-medium text-gray-700">Log File:</label>
                            <select id="log-file" onchange="changeLogFile()" class="rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                @foreach($logFiles as $file)
                                    <option value="{{ $file }}" {{ $selectedLogFile === $file ? 'selected' : '' }}>
                                        {{ $file }}
                                        @if(file_exists(storage_path('logs/' . $file)))
                                            ({{ number_format(filesize(storage_path('logs/' . $file)) / 1024, 1) }} KB)
                                        @endif
                                    </option>
                                @endforeach
                            </select>

                            @if($selectedLogFile && file_exists(storage_path('logs/' . $selectedLogFile)))
                                <span class="text-sm text-gray-500">
                                Last modified: {{ date('Y-m-d H:i:s', filemtime(storage_path('logs/' . $selectedLogFile))) }}
                            </span>
                            @endif
                        </div>
                    </div>

                    <!-- Log Level Filter -->
                    <div class="mb-6">
                        <div class="flex items-center space-x-4">
                            <label class="text-sm font-medium text-gray-700">Filter by Level:</label>
                            <div class="flex space-x-2">
                                @foreach(['emergency', 'alert', 'critical', 'error', 'warning', 'notice', 'info', 'debug'] as $level)
                                    <button onclick="filterByLevel('{{ $level }}')"
                                            class="log-level-btn px-3 py-1 text-xs font-medium rounded-full border
                                               @php
                                                   $colors = [
                                                       'emergency' => 'bg-red-600 text-white border-red-600',
                                                       'alert' => 'bg-red-500 text-white border-red-500',
                                                       'critical' => 'bg-red-400 text-white border-red-400',
                                                       'error' => 'bg-red-300 text-red-800 border-red-300',
                                                       'warning' => 'bg-yellow-300 text-yellow-800 border-yellow-300',
                                                       'notice' => 'bg-blue-300 text-blue-800 border-blue-300',
                                                       'info' => 'bg-green-300 text-green-800 border-green-300',
                                                       'debug' => 'bg-gray-300 text-gray-800 border-gray-300'
                                                   ];
                                                   echo $colors[$level] ?? 'bg-gray-100 text-gray-800 border-gray-300';
                                               @endphp">
                                        {{ ucfirst($level) }}
                                    </button>
                                @endforeach
                                <button onclick="showAllLevels()" class="px-3 py-1 text-xs font-medium rounded-full border bg-indigo-100 text-indigo-800 border-indigo-300">
                                    Show All
                                </button>
                            </div>
                        </div>
                    </div>

                    @if($logContent)
                        <!-- Log Entries -->
                        <div class="bg-gray-50 rounded-lg p-4">
                            <div class="flex justify-between items-center mb-4">
                                <h3 class="text-lg font-medium text-gray-900">Log Entries</h3>
                                <div class="flex items-center space-x-4">
                                    <span class="text-sm text-gray-500">Total entries: <span id="total-entries">{{ count($logEntries) }}</span></span>
                                    <div class="flex space-x-2">
                                        <button onclick="scrollToTop()" class="text-xs px-2 py-1 bg-gray-200 rounded hover:bg-gray-300">Top</button>
                                        <button onclick="scrollToBottom()" class="text-xs px-2 py-1 bg-gray-200 rounded hover:bg-gray-300">Bottom</button>
                                    </div>
                                </div>
                            </div>

                            <div id="log-container" class="bg-black text-green-400 font-mono text-sm p-4 rounded-lg h-96 overflow-y-auto">
                                @if(count($logEntries) > 0)
                                    @foreach($logEntries as $entry)
                                        <div class="log-entry mb-2 pb-2 border-b border-gray-800 {{ strtolower($entry['level']) }}-entry" data-level="{{ strtolower($entry['level']) }}">
                                            <div class="flex items-start space-x-3">
                                                <span class="text-gray-400 text-xs shrink-0">{{ $entry['date'] ?? 'Unknown' }}</span>
                                                <span class="log-level
                                                @php
                                                    $levelColors = [
                                                        'emergency' => 'text-red-500',
                                                        'alert' => 'text-red-400',
                                                        'critical' => 'text-red-300',
                                                        'error' => 'text-red-200',
                                                        'warning' => 'text-yellow-400',
                                                        'notice' => 'text-blue-400',
                                                        'info' => 'text-green-400',
                                                        'debug' => 'text-gray-400'
                                                    ];
                                                    echo $levelColors[strtolower($entry['level'])] ?? 'text-gray-400';
                                                @endphp
                                                font-bold uppercase text-xs shrink-0">
                                                {{ $entry['level'] ?? 'UNKNOWN' }}
                                            </span>
                                                <span class="text-green-300 break-all">{{ $entry['message'] ?? 'No message' }}</span>
                                            </div>
                                            @if(!empty($entry['context']))
                                                <div class="mt-1 ml-20 text-gray-500 text-xs">
                                                    Context: {{ $entry['context'] }}
                                                </div>
                                            @endif
                                            @if(!empty($entry['extra']))
                                                <div class="mt-1 ml-20 text-gray-500 text-xs">
                                                    Extra: {{ $entry['extra'] }}
                                                </div>
                                            @endif
                                        </div>
                                    @endforeach
                                @else
                                    <div class="text-center text-gray-500 py-8">
                                        <p>No log entries found.</p>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <!-- Raw Log Content (Collapsible) -->
                        <div class="mt-6">
                            <button onclick="toggleRawLog()" class="text-sm text-indigo-600 hover:text-indigo-500 font-medium">
                                <span id="raw-log-toggle">Show Raw Log Content</span>
                            </button>
                            <div id="raw-log-content" class="hidden mt-4 bg-gray-900 text-gray-300 font-mono text-xs p-4 rounded-lg h-64 overflow-auto">
                                <pre>{{ $logContent }}</pre>
                            </div>
                        </div>
                    @else
                        <div class="text-center py-12">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-gray-900">No log file selected or file not found</h3>
                            <p class="mt-1 text-sm text-gray-500">
                                @if($selectedLogFile)
                                    The selected log file "{{ $selectedLogFile }}" could not be read or does not exist.
                                @else
                                    Please select a log file to view its contents.
                                @endif
                            </p>
                        </div>
                    @endif

                    <!-- Log Management Actions -->
                    <div class="mt-8 border-t border-gray-200 pt-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Log Management</h3>
                        <div class="flex flex-wrap gap-3">
                            @if($selectedLogFile && file_exists(storage_path('logs/' . $selectedLogFile)))
                                <a href="{{ route('admin.download-log', ['file' => $selectedLogFile]) }}"
                                   class="inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                    </svg>
                                    Download Log
                                </a>

                                <form action="{{ route('admin.clear-log') }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to clear this log file? This action cannot be undone.')">
                                    @csrf
                                    <input type="hidden" name="file" value="{{ $selectedLogFile }}">
                                    <button type="submit" class="inline-flex items-center px-3 py-2 border border-red-300 shadow-sm text-sm leading-4 font-medium rounded-md text-red-700 bg-white hover:bg-red-50">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                        </svg>
                                        Clear Log
                                    </button>
                                </form>
                            @endif

                            <form action="{{ route('admin.clear-all-logs') }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to clear ALL log files? This action cannot be undone.')">
                                @csrf
                                <button type="submit" class="inline-flex items-center px-3 py-2 border border-red-300 shadow-sm text-sm leading-4 font-medium rounded-md text-red-700 bg-white hover:bg-red-50">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                    </svg>
                                    Clear All Logs
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function changeLogFile() {
            const select = document.getElementById('log-file');
            const selectedFile = select.value;
            if (selectedFile) {
                window.location.href = `{{ route('admin.logs') }}?file=${selectedFile}`;
            }
        }

        function filterByLevel(level) {
            const entries = document.querySelectorAll('.log-entry');
            let visibleCount = 0;

            entries.forEach(entry => {
                if (entry.dataset.level === level) {
                    entry.style.display = 'block';
                    visibleCount++;
                } else {
                    entry.style.display = 'none';
                }
            });

            document.getElementById('total-entries').textContent = visibleCount;

            // Update button states
            document.querySelectorAll('.log-level-btn').forEach(btn => {
                btn.classList.remove('ring-2', 'ring-indigo-500');
            });
            event.target.classList.add('ring-2', 'ring-indigo-500');
        }

        function showAllLevels() {
            const entries = document.querySelectorAll('.log-entry');
            entries.forEach(entry => {
                entry.style.display = 'block';
            });

            document.getElementById('total-entries').textContent = entries.length;

            // Reset button states
            document.querySelectorAll('.log-level-btn').forEach(btn => {
                btn.classList.remove('ring-2', 'ring-indigo-500');
            });
            event.target.classList.add('ring-2', 'ring-indigo-500');
        }

        function scrollToTop() {
            document.getElementById('log-container').scrollTop = 0;
        }

        function scrollToBottom() {
            const container = document.getElementById('log-container');
            container.scrollTop = container.scrollHeight;
        }

        function toggleRawLog() {
            const content = document.getElementById('raw-log-content');
            const toggle = document.getElementById('raw-log-toggle');

            if (content.classList.contains('hidden')) {
                content.classList.remove('hidden');
                toggle.textContent = 'Hide Raw Log Content';
            } else {
                content.classList.add('hidden');
                toggle.textContent = 'Show Raw Log Content';
            }
        }

        // Auto-refresh every 30 seconds
        let autoRefresh = setInterval(() => {
            if (document.hidden) return; // Don't refresh if tab is not active

            // Only refresh if user hasn't interacted recently
            const lastActivity = localStorage.getItem('lastLogActivity');
            if (!lastActivity || Date.now() - parseInt(lastActivity) > 30000) {
                location.reload();
            }
        }, 30000);

        // Track user activity
        document.addEventListener('click', () => {
            localStorage.setItem('lastLogActivity', Date.now().toString());
        });

        // Scroll to bottom on page load for real-time feel
        window.addEventListener('load', () => {
            setTimeout(() => {
                scrollToBottom();
            }, 100);
        });
    </script>
@endsection
