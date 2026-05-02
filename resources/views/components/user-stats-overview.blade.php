@php
    // Cache user stats for 5 minutes
    $userStats = \App\Services\CacheService::remember(
        \App\Services\CacheService::userKey(auth()->id(), 'dashboard_stats'),
        function() {
            return [
                'today_earnings' => \App\Models\UserTask::where('user_id', auth()->id())
                    ->where('payment_status', 'success')
                    ->whereDate('payment_verified_at', today())
                    ->sum('payment_amount'),
                'total_earnings' => \App\Models\UserTask::where('user_id', auth()->id())
                    ->where('payment_status', 'success')
                    ->sum('payment_amount'),
                'active_tasks' => \App\Models\UserTask::where('user_id', auth()->id())
                    ->active()
                    ->where(function($q) {
                        $q->whereNull('deadline_at')->orWhere('deadline_at', '>', now());
                    })
                    ->whereHas('task', function($q) { $q->active(); })
                    ->count(),
                'completed_today' => \App\Models\UserTask::where('user_id', auth()->id())
                    ->where('status', 'completed')
                    ->whereDate('completed_at', today())
                    ->count(),
                'pending_verification' => \App\Models\UserTask::where('user_id', auth()->id())
                    ->whereIn('status', ['pending_verification_1', 'pending_verification_2'])
                    ->where(function($q) {
                        $q->whereNull('deadline_at')->orWhere('deadline_at', '>', now());
                    })
                    ->whereHas('task', function($q) { $q->active(); })
                    ->count(),
            ];
        },
        5 // Cache for 5 minutes
    );
@endphp

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
    <!-- Today's Earnings -->
    <div class="relative overflow-hidden rounded-xl bg-gradient-to-br from-yellow-50 to-amber-50 border border-yellow-200/50 p-6 shadow-sm hover:shadow-md transition-all duration-300">
        <div class="absolute top-0 right-0 -mt-4 -mr-4 h-24 w-24 rounded-full bg-yellow-400/10"></div>
        <div class="relative">
            <div class="flex items-center justify-between mb-3">
                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-yellow-500/10">
                    <svg class="w-6 h-6 text-yellow-600" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M8.433 7.418c.155-.103.346-.196.567-.267v1.698a2.305 2.305 0 01-.567-.267C8.07 8.34 8 8.114 8 8c0-.114.07-.34.433-.582zM11 12.849v-1.698c.22.071.412.164.567.267.364.243.433.468.433.582 0 .114-.07.34-.433.582a2.305 2.305 0 01-.567.267z"></path>
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-13a1 1 0 10-2 0v.092a4.535 4.535 0 00-1.676.662C6.602 6.234 6 7.009 6 8c0 .99.602 1.765 1.324 2.246.48.32 1.054.545 1.676.662v1.941c-.391-.127-.68-.317-.843-.504a1 1 0 10-1.51 1.31c.562.649 1.413 1.076 2.353 1.253V15a1 1 0 102 0v-.092a4.535 4.535 0 001.676-.662C13.398 13.766 14 12.991 14 12c0-.99-.602-1.765-1.324-2.246A4.535 4.535 0 0011 9.092V7.151c.391.127.68.317.843.504a1 1 0 101.511-1.31c-.563-.649-1.413-1.076-2.354-1.253V5z" clip-rule="evenodd"></path>
                    </svg>
                </div>
                <span class="inline-flex items-center gap-1 rounded-full bg-yellow-100 px-2 py-1 text-xs font-medium text-yellow-700">
                    <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M12 7a1 1 0 110-2h5a1 1 0 011 1v5a1 1 0 11-2 0V8.414l-4.293 4.293a1 1 0 01-1.414 0L8 10.414l-4.293 4.293a1 1 0 01-1.414-1.414l5-5a1 1 0 011.414 0L11 10.586 14.586 7H12z" clip-rule="evenodd"></path>
                    </svg>
                    Today
                </span>
            </div>
            <h3 class="text-2xl font-bold text-zinc-900 mb-1">
                Rp {{ number_format($userStats['today_earnings'], 0, ',', '.') }}
            </h3>
            <p class="text-sm text-zinc-600">Earnings Today</p>
        </div>
    </div>

    <!-- Total Earnings -->
    <div class="relative overflow-hidden rounded-xl bg-gradient-to-br from-green-50 to-emerald-50 border border-green-200/50 p-6 shadow-sm hover:shadow-md transition-all duration-300">
        <div class="absolute top-0 right-0 -mt-4 -mr-4 h-24 w-24 rounded-full bg-green-400/10"></div>
        <div class="relative">
            <div class="flex items-center justify-between mb-3">
                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-green-500/10">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <span class="inline-flex items-center gap-1 rounded-full bg-green-100 px-2 py-1 text-xs font-medium text-green-700">
                    All Time
                </span>
            </div>
            <h3 class="text-2xl font-bold text-zinc-900 mb-1">
                Rp {{ number_format($userStats['total_earnings'], 0, ',', '.') }}
            </h3>
            <p class="text-sm text-zinc-600">Total Earnings</p>
        </div>
    </div>

    <!-- Active Tasks -->
    <div class="relative overflow-hidden rounded-xl bg-gradient-to-br from-blue-50 to-indigo-50 border border-blue-200/50 p-6 shadow-sm hover:shadow-md transition-all duration-300">
        <div class="absolute top-0 right-0 -mt-4 -mr-4 h-24 w-24 rounded-full bg-blue-400/10"></div>
        <div class="relative">
            <div class="flex items-center justify-between mb-3">
                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-blue-500/10">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                    </svg>
                </div>
                <span class="inline-flex items-center gap-1 rounded-full bg-blue-100 px-2 py-1 text-xs font-medium text-blue-700">
                    Active
                </span>
            </div>
            <h3 class="text-2xl font-bold text-zinc-900 mb-1">
                {{ $userStats['active_tasks'] }}
            </h3>
            <p class="text-sm text-zinc-600">Active Tasks</p>
        </div>
    </div>

    <!-- Completed Today -->
    <div class="relative overflow-hidden rounded-xl bg-gradient-to-br from-purple-50 to-violet-50 border border-purple-200/50 p-6 shadow-sm hover:shadow-md transition-all duration-300">
        <div class="absolute top-0 right-0 -mt-4 -mr-4 h-24 w-24 rounded-full bg-purple-400/10"></div>
        <div class="relative">
            <div class="flex items-center justify-between mb-3">
                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-purple-500/10">
                    <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <span class="inline-flex items-center gap-1 rounded-full bg-purple-100 px-2 py-1 text-xs font-medium text-purple-700">
                    @if($userStats['pending_verification'] > 0)
                        {{ $userStats['pending_verification'] }} Pending
                    @else
                        Done
                    @endif
                </span>
            </div>
            <h3 class="text-2xl font-bold text-zinc-900 mb-1">
                {{ $userStats['completed_today'] }}
            </h3>
            <p class="text-sm text-zinc-600">Completed Today</p>
        </div>
    </div>
</div>
