<x-filament-widgets::widget>
    <x-filament::section>
        <x-slot name="heading">
            <div class="flex items-center gap-2">
                <span class="text-base">🔔</span>
                <span class="font-semibold">Aktivitas Terbaru</span>
            </div>
        </x-slot>
        <div class="space-y-2 max-h-80 overflow-y-auto">
            @forelse($this->getActivities() as $activity)
                <div class="flex items-start gap-2 p-2 rounded-lg bg-gray-50 dark:bg-white/5">
                    <div @class([
                        'flex-shrink-0 w-6 h-6 rounded-full flex items-center justify-center text-xs',
                        'bg-primary-50 text-primary-600 dark:bg-primary-400/10 dark:text-primary-400' => $activity['color'] === 'primary',
                        'bg-success-50 text-success-600 dark:bg-success-400/10 dark:text-success-400' => $activity['color'] === 'success',
                        'bg-warning-50 text-warning-600 dark:bg-warning-400/10 dark:text-warning-400' => $activity['color'] === 'warning',
                        'bg-danger-50 text-danger-600 dark:bg-danger-400/10 dark:text-danger-400' => $activity['color'] === 'danger',
                        'bg-info-50 text-info-600 dark:bg-info-400/10 dark:text-info-400' => $activity['color'] === 'info',
                    ])>
                        @php
                            $emoji = match($activity['color']) {
                                'primary' => '🔔',
                                'success' => '✅',
                                'warning' => '⚠️',
                                'danger' => '❌',
                                'info' => 'ℹ️',
                                default => '🔔',
                            };
                        @endphp
                        <span aria-hidden="true">{{ $emoji }}</span>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-xs text-gray-700 dark:text-gray-300 line-clamp-1">
                            {{ $activity['message'] }}
                        </p>
                        <p class="text-[10px] text-gray-500 dark:text-gray-500">
                            {{ \Carbon\Carbon::parse($activity['time'])->diffForHumans() }}
                        </p>
                    </div>
                </div>
            @empty
                <div class="text-center py-6 text-gray-400">
                    <div class="mx-auto mb-2 text-2xl opacity-50">📭</div>
                    <p class="text-xs">Belum ada aktivitas</p>
                </div>
            @endforelse
        </div>
    </x-filament::section>
</x-filament-widgets::widget>
