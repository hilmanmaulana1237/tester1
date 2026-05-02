<x-filament-panels::page>
    <div class="space-y-6">
        <!-- Category Info -->
        <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white">
                        {{ $record->name }}
                    </h2>
                    @if($record->description)
                        <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                            {{ $record->description }}
                        </p>
                    @endif
                </div>
                <div class="text-right">
                    <div class="text-2xl font-bold text-blue-600 dark:text-blue-400">
                        {{ $record->activeTasks()->count() }}
                    </div>
                    <div class="text-sm text-gray-600 dark:text-gray-400">
                        Active Tasks
                    </div>
                </div>
            </div>
            
            @if($record->expired_at)
                <div class="mt-4 flex items-center text-sm">
                    <svg class="w-4 h-4 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <span class="text-gray-600 dark:text-gray-400">
                        Expires: {{ $record->expired_at->format('M d, Y H:i') }}
                    </span>
                    @if($record->isExpired())
                        <span class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200">
                            Expired
                        </span>
                    @endif
                </div>
            @endif
        </div>

        <!-- Tasks Table -->
        <div class="bg-white dark:bg-gray-800 shadow rounded-lg">
            {{ $this->table }}
        </div>
    </div>
</x-filament-panels::page>
