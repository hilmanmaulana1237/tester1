<x-filament-panels::page>
    <div class="space-y-6">
        {{-- Stats Cards --}}
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <x-filament::card>
                <div class="flex items-center gap-3">
                    <div class="p-3 bg-blue-100 dark:bg-blue-900 rounded-lg">
                        <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Total Chats</p>
                        <p class="text-2xl font-bold">{{ \App\Models\UserTask::whereHas('messages')->count() }}</p>
                    </div>
                </div>
            </x-filament::card>

            <x-filament::card>
                <div class="flex items-center gap-3">
                    <div class="p-3 bg-red-100 dark:bg-red-900 rounded-lg">
                        <svg class="w-6 h-6 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Unread Messages</p>
                        <p class="text-2xl font-bold">
                            {{ \App\Models\TaskMessage::where('sender_type', 'user')->where('is_read', false)->count() }}
                        </p>
                    </div>
                </div>
            </x-filament::card>

            <x-filament::card>
                <div class="flex items-center gap-3">
                    <div class="p-3 bg-green-100 dark:bg-green-900 rounded-lg">
                        <svg class="w-6 h-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Active Today</p>
                        <p class="text-2xl font-bold">
                            {{ \App\Models\UserTask::whereHas('messages', fn($q) => $q->whereDate('created_at', today()))->count() }}
                        </p>
                    </div>
                </div>
            </x-filament::card>

            <x-filament::card>
                <div class="flex items-center gap-3">
                    <div class="p-3 bg-purple-100 dark:bg-purple-900 rounded-lg">
                        <svg class="w-6 h-6 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8h2a2 2 0 012 2v6a2 2 0 01-2 2h-2v4l-4-4H9a1.994 1.994 0 01-1.414-.586m0 0L11 14h4a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2v4l.586-.586z" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Total Messages</p>
                        <p class="text-2xl font-bold">{{ \App\Models\TaskMessage::count() }}</p>
                    </div>
                </div>
            </x-filament::card>
        </div>

        {{-- Table --}}
        {{ $this->table }}
    </div>
</x-filament-panels::page>
