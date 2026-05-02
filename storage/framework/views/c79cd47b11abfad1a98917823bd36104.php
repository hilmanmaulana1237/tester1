<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames((['activeTaskId' => null]));

foreach ($attributes->all() as $__key => $__value) {
    if (in_array($__key, $__propNames)) {
        $$__key = $$__key ?? $__value;
    } else {
        $__newAttributes[$__key] = $__value;
    }
}

$attributes = new \Illuminate\View\ComponentAttributeBag($__newAttributes);

unset($__propNames);
unset($__newAttributes);

foreach (array_filter((['activeTaskId' => null]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<?php
    // Get user's task that NEEDS immediate action (STATUS_TAKEN only)
    // Tasks pending verification don't need to be shown here
    $hasActiveTask = false;
    $activeTask = null;
    if (auth()->check()) {
        $activeTask = \App\Models\UserTask::where('user_id', auth()->id())
            ->where('status', \App\Models\UserTask::STATUS_TAKEN) // Only STATUS_TAKEN (not pending verification)
            ->where(function ($q) {
                $q->whereNull('deadline_at')->orWhere('deadline_at', '>', now());
            })
            ->whereHas('task', fn($q) => $q->active())
            ->with('task')
            ->orderBy('taken_at', 'desc') // Get the most recently taken task
            ->first();
        $hasActiveTask = $activeTask !== null;
    }

    // Get counts for badges
    $availableTasksCount = \App\Services\CacheService::remember(
        'available_tasks_count',
        function () {
            return \App\Models\Task::active()->neverTaken()->count();
        },
        5
    );

    $myActiveTasksCount = 0;
    if (auth()->check()) {
        $myActiveTasksCount = \App\Services\CacheService::remember(
            \App\Services\CacheService::userKey(auth()->id(), 'my_active_tasks_count'),
            function () {
                return \App\Models\UserTask::where('user_id', auth()->id())
                    ->active()
                    ->where(function ($q) {
                        $q->whereNull('deadline_at')->orWhere('deadline_at', '>', now());
                    })
                    ->whereHas('task', function ($q) {
                        $q->active();
                    })
                    ->count();
            },
            5
        );
    }
?>

<!-- Bottom Navigation Bar - Modern Purple Theme -->
<nav class="fixed z-50 w-full max-w-md -translate-x-1/2 bottom-4 left-1/2 px-4">
    <div class="bg-white/95 backdrop-blur-xl border border-violet-100 rounded-2xl shadow-xl shadow-violet-500/10">
        <div class="grid h-16 grid-cols-5">
            <!-- Home -->
            <a href="<?php echo e(route('dashboard')); ?>" wire:navigate
                x-data="{ pressed: false }"
                @mousedown="pressed = true" @mouseup="pressed = false" @mouseleave="pressed = false"
                @touchstart.passive="pressed = true" @touchend.passive="pressed = false"
                :class="pressed ? 'scale-90' : 'scale-100'"
                class="inline-flex flex-col items-center justify-center gap-0.5 transition-all duration-150 rounded-l-2xl relative <?php echo e(request()->routeIs('dashboard') ? 'text-violet-600' : 'text-zinc-400 hover:text-violet-500'); ?>">
                <div class="absolute bottom-1.5 left-1/2 -translate-x-1/2 h-1 rounded-full transition-all duration-300 <?php echo e(request()->routeIs('dashboard') ? 'w-5 bg-violet-500' : 'w-0 bg-transparent'); ?>"></div>
                <svg class="w-6 h-6" fill="<?php echo e(request()->routeIs('dashboard') ? 'currentColor' : 'none'); ?>" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="<?php echo e(request()->routeIs('dashboard') ? '0' : '1.5'); ?>" d="m4 12 8-8 8 8M6 10.5V19a1 1 0 0 0 1 1h3v-3a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v3h3a1 1 0 0 0 1-1v-8.5" />
                </svg>
                <span class="text-[10px] font-medium leading-none">Home</span>
            </a>

            <?php
                // Check if user has a task they're actively working on (STATUS_TAKEN)
                // They should not be able to browse if they just took a task
                $hasTaskInProgress = false;
                $taskInProgress = null;
                if (auth()->check()) {
                    $taskInProgress = \App\Models\UserTask::where('user_id', auth()->id())
                        ->where('status', \App\Models\UserTask::STATUS_TAKEN)
                        ->where(function ($q) {
                            $q->whereNull('deadline_at')->orWhere('deadline_at', '>', now());
                        })
                        ->with('task')
                        ->first();
                    $hasTaskInProgress = $taskInProgress !== null;
                }
            ?>

            <!-- Browse Tasks -->
            <?php if($hasTaskInProgress): ?>
                <button x-data="{ pressed: false }"
                    @mousedown="pressed = true" @mouseup="pressed = false" @mouseleave="pressed = false"
                    @touchstart.passive="pressed = true" @touchend.passive="pressed = false"
                    :class="pressed ? 'scale-90' : 'scale-100'"
                    @click="$dispatch('show-toast', { message: 'Selesaikan task sebelum mengambil yang baru.', type: 'warning' })"
                    class="inline-flex flex-col items-center justify-center gap-0.5 transition-all duration-150 text-zinc-300 cursor-not-allowed relative">
                    <div class="relative">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" />
                        </svg>
                        <svg class="w-3 h-3 absolute -top-1 -right-1 text-orange-500" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <span class="text-[10px] font-medium leading-none">Cari</span>
                </button>
            <?php else: ?>
                <a href="<?php echo e(route('user.dashboard')); ?>" wire:navigate
                    x-data="{ pressed: false }"
                    @mousedown="pressed = true" @mouseup="pressed = false" @mouseleave="pressed = false"
                    @touchstart.passive="pressed = true" @touchend.passive="pressed = false"
                    :class="pressed ? 'scale-90' : 'scale-100'"
                    class="inline-flex flex-col items-center justify-center gap-0.5 transition-all duration-150 relative <?php echo e(request()->routeIs('user.dashboard') ? 'text-violet-600' : 'text-zinc-400 hover:text-violet-500'); ?>">
                    <div class="absolute bottom-1.5 left-1/2 -translate-x-1/2 h-1 rounded-full transition-all duration-300 <?php echo e(request()->routeIs('user.dashboard') ? 'w-5 bg-violet-500' : 'w-0 bg-transparent'); ?>"></div>
                    <div class="relative">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" />
                        </svg>
                        <?php if($availableTasksCount > 0): ?>
                            <span class="absolute -top-1.5 -right-2 min-w-[16px] h-4 flex items-center justify-center px-1 text-[9px] font-bold text-white bg-violet-500 rounded-full">
                                <?php echo e($availableTasksCount > 99 ? '99+' : $availableTasksCount); ?>

                            </span>
                        <?php endif; ?>
                    </div>
                    <span class="text-[10px] font-medium leading-none">Cari</span>
                </a>
            <?php endif; ?>

            <!-- Center Action Button -->
            <div class="flex items-center justify-center">
                <?php if($hasActiveTask): ?>
                    <a href="<?php echo e(route('user.task.work', $activeTask->task_id)); ?>" wire:navigate
                        x-data="{ pressed: false }"
                        @mousedown="pressed = true" @mouseup="pressed = false" @mouseleave="pressed = false"
                        @touchstart.passive="pressed = true" @touchend.passive="pressed = false"
                        :class="pressed ? 'scale-90' : 'scale-100 hover:scale-105'"
                        class="relative inline-flex items-center justify-center w-14 h-14 -mt-6 text-white transition-all duration-150 bg-gradient-to-br from-orange-400 to-orange-600 rounded-2xl shadow-lg shadow-orange-500/40"
                        title="Lanjutkan: <?php echo e($activeTask->task->title); ?>">
                        <svg class="w-7 h-7" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M8 5v14l11-7z" />
                        </svg>
                        <span class="absolute -top-1 -right-1 w-3 h-3 bg-green-400 border-2 border-white rounded-full animate-pulse"></span>
                    </a>
                <?php else: ?>
                    <a href="<?php echo e(route('user.my-tasks')); ?>" wire:navigate
                        x-data="{ pressed: false }"
                        @mousedown="pressed = true" @mouseup="pressed = false" @mouseleave="pressed = false"
                        @touchstart.passive="pressed = true" @touchend.passive="pressed = false"
                        :class="pressed ? 'scale-90' : 'scale-100 hover:scale-105'"
                        class="inline-flex items-center justify-center w-14 h-14 -mt-6 text-white transition-all duration-150 bg-gradient-to-br from-violet-500 to-purple-600 rounded-2xl shadow-lg shadow-violet-500/40"
                        title="Tugas Saya">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                        </svg>
                    </a>
                <?php endif; ?>
            </div>

            <!-- My Tasks -->
            <a href="<?php echo e(route('user.my-tasks')); ?>" wire:navigate
                x-data="{ pressed: false }"
                @mousedown="pressed = true" @mouseup="pressed = false" @mouseleave="pressed = false"
                @touchstart.passive="pressed = true" @touchend.passive="pressed = false"
                :class="pressed ? 'scale-90' : 'scale-100'"
                class="inline-flex flex-col items-center justify-center gap-0.5 transition-all duration-150 relative <?php echo e(request()->routeIs('user.my-tasks') ? 'text-violet-600' : 'text-zinc-400 hover:text-violet-500'); ?>">
                <div class="absolute bottom-1.5 left-1/2 -translate-x-1/2 h-1 rounded-full transition-all duration-300 <?php echo e(request()->routeIs('user.my-tasks') ? 'w-5 bg-violet-500' : 'w-0 bg-transparent'); ?>"></div>
                <div class="relative">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                    </svg>
                    <?php if($myActiveTasksCount > 0): ?>
                        <span class="absolute -top-1.5 -right-2 min-w-[16px] h-4 flex items-center justify-center px-1 text-[9px] font-bold text-white bg-violet-500 rounded-full">
                            <?php echo e($myActiveTasksCount > 99 ? '99+' : $myActiveTasksCount); ?>

                        </span>
                    <?php endif; ?>
                </div>
                <span class="text-[10px] font-medium leading-none">Tugas Saya</span>
            </a>

            <!-- More Menu -->
            <div x-data="{ open: false, pressed: false }" class="relative">
                <button @click="open = !open"
                    @mousedown="pressed = true" @mouseup="pressed = false" @mouseleave="pressed = false"
                    @touchstart.passive="pressed = true" @touchend.passive="pressed = false"
                    :class="pressed ? 'scale-90' : 'scale-100'"
                    class="inline-flex flex-col items-center justify-center gap-0.5 w-full h-full transition-all duration-150 rounded-r-2xl relative <?php echo e(request()->routeIs('settings.*') || request()->routeIs('pages.*') ? 'text-violet-600' : 'text-zinc-400 hover:text-violet-500'); ?>">
                    <div class="absolute bottom-1.5 left-1/2 -translate-x-1/2 h-1 rounded-full transition-all duration-300 <?php echo e(request()->routeIs('settings.*') || request()->routeIs('pages.*') ? 'w-5 bg-violet-500' : 'w-0 bg-transparent'); ?>"></div>
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                    </svg>
                    <span class="text-[10px] font-medium leading-none">Menu</span>
                </button>

                <!-- Dropdown Menu -->
                <div x-show="open" x-transition:enter="transition ease-out duration-200"
                    x-transition:enter-start="opacity-0 translate-y-2"
                    x-transition:enter-end="opacity-100 translate-y-0"
                    x-transition:leave="transition ease-in duration-150"
                    x-transition:leave-start="opacity-100 translate-y-0"
                    x-transition:leave-end="opacity-0 translate-y-2" @click.away="open = false"
                    class="absolute bottom-full right-0 mb-3 w-56 bg-white rounded-xl shadow-xl shadow-violet-500/10 border border-violet-100 overflow-hidden">

                    <div class="p-2 border-b border-zinc-100">
                        <p class="px-3 py-1 text-xs font-semibold text-zinc-400 uppercase tracking-wide">Menu</p>
                    </div>

                    <!-- History -->
                    <a href="<?php echo e(route('user.history')); ?>" wire:navigate @click="open = false"
                        class="flex items-center gap-3 px-4 py-3 text-sm text-zinc-700 hover:bg-violet-50 hover:text-violet-600 transition-colors">
                        <svg class="w-5 h-5 text-zinc-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                d="M12 8v4l3 3m6-3a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                        </svg>
                        Riwayat
                    </a>

                    <div class="h-px bg-zinc-100 mx-3"></div>

                    <!-- Guides Section -->
                    <div class="p-2 border-b border-zinc-100">
                        <p class="px-3 py-1 text-xs font-semibold text-zinc-400 uppercase tracking-wide">Panduan & Tips
                        </p>
                    </div>

                    <?php if(Route::has('pages.panduan-task')): ?>
                        <a href="<?php echo e(route('pages.panduan-task')); ?>" wire:navigate @click="open = false"
                            class="flex items-center gap-3 px-4 py-2.5 text-sm text-zinc-700 hover:bg-violet-50 hover:text-violet-600 transition-colors">
                            <svg class="w-5 h-5 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                    d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25" />
                            </svg>
                            Panduan Task
                        </a>
                    <?php endif; ?>

                    <?php if(Route::has('pages.tips-sukses')): ?>
                        <a href="<?php echo e(route('pages.tips-sukses')); ?>" wire:navigate @click="open = false"
                            class="flex items-center gap-3 px-4 py-2.5 text-sm text-zinc-700 hover:bg-violet-50 hover:text-violet-600 transition-colors">
                            <svg class="w-5 h-5 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                    d="M12 18v-5.25m0 0a6.01 6.01 0 001.5-.189m-1.5.189a6.01 6.01 0 01-1.5-.189m3.75 7.478a12.06 12.06 0 01-4.5 0m3.75 2.383a14.406 14.406 0 01-3 0M14.25 18v-.192c0-.983.658-1.823 1.508-2.316a7.5 7.5 0 10-7.517 0c.85.493 1.509 1.333 1.509 2.316V18" />
                            </svg>
                            Tips Sukses
                        </a>
                    <?php endif; ?>

                    <?php if(Route::has('pages.faq')): ?>
                        <a href="<?php echo e(route('pages.faq')); ?>" wire:navigate @click="open = false"
                            class="flex items-center gap-3 px-4 py-2.5 text-sm text-zinc-700 hover:bg-violet-50 hover:text-violet-600 transition-colors">
                            <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                    d="M9.879 7.519c1.171-1.025 3.071-1.025 4.242 0 1.172 1.025 1.172 2.687 0 3.712-.203.179-.43.326-.67.442-.745.361-1.45.999-1.45 1.827v.75M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9 5.25h.008v.008H12v-.008z" />
                            </svg>
                            FAQ
                        </a>
                    <?php endif; ?>

                    <?php if(Route::has('pages.tutorial-page')): ?>
                        <a href="<?php echo e(route('pages.tutorial-page')); ?>" wire:navigate @click="open = false"
                            class="flex items-center gap-3 px-4 py-2.5 text-sm text-zinc-700 hover:bg-violet-50 hover:text-violet-600 transition-colors">
                            <svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                    d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                    d="M15.91 11.672a.375.375 0 010 .656l-5.603 3.113a.375.375 0 01-.557-.328V8.887c0-.286.307-.466.557-.327l5.603 3.112z" />
                            </svg>
                            Tutorial
                        </a>
                    <?php endif; ?>

                    <div class="h-px bg-zinc-100 mx-3"></div>

                    <!-- Profile & Settings -->
                    <a href="<?php echo e(route('settings.profile')); ?>" wire:navigate @click="open = false"
                        class="flex items-center gap-3 px-4 py-3 text-sm text-zinc-700 hover:bg-violet-50 hover:text-violet-600 transition-colors">
                        <svg class="w-5 h-5 text-zinc-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                d="M9.594 3.94c.09-.542.56-.94 1.11-.94h2.593c.55 0 1.02.398 1.11.94l.213 1.281c.063.374.313.686.645.87.074.04.147.083.22.127.324.196.72.257 1.075.124l1.217-.456a1.125 1.125 0 011.37.49l1.296 2.247a1.125 1.125 0 01-.26 1.431l-1.003.827c-.293.24-.438.613-.431.992a6.759 6.759 0 010 .255c-.007.378.138.75.43.99l1.005.828c.424.35.534.954.26 1.43l-1.298 2.247a1.125 1.125 0 01-1.369.491l-1.217-.456c-.355-.133-.75-.072-1.076.124a6.57 6.57 0 01-.22.128c-.331.183-.581.495-.644.869l-.213 1.28c-.09.543-.56.941-1.11.941h-2.594c-.55 0-1.02-.398-1.11-.94l-.213-1.281c-.062-.374-.312-.686-.644-.87a6.52 6.52 0 01-.22-.127c-.325-.196-.72-.257-1.076-.124l-1.217.456a1.125 1.125 0 01-1.369-.49l-1.297-2.247a1.125 1.125 0 01.26-1.431l1.004-.827c.292-.24.437-.613.43-.992a6.932 6.932 0 010-.255c.007-.378-.138-.75-.43-.99l-1.004-.828a1.125 1.125 0 01-.26-1.43l1.297-2.247a1.125 1.125 0 011.37-.491l1.216.456c.356.133.751.072 1.076-.124.072-.044.146-.087.22-.128.332-.183.582-.495.644-.869l.214-1.281z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                        Pengaturan
                    </a>

                    <!-- Logout -->
                    <form method="POST" action="<?php echo e(route('logout')); ?>">
                        <?php echo csrf_field(); ?>
                        <button type="submit"
                            class="flex items-center gap-3 w-full px-4 py-3 text-sm text-red-600 hover:bg-red-50 transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                    d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15M12 9l-3 3m0 0l3 3m-3-3h12.75" />
                            </svg>
                            Keluar
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</nav>

<!-- Active Task Floating Indicator -->
<?php if($hasActiveTask && !request()->routeIs('user.task.work')): ?>
    <div x-data="{
                show: true,
                taskId: <?php echo e($activeTask->task_id); ?>,
                init() {
                    // Check if user dismissed this task indicator recently
                    const dismissed = localStorage.getItem('dismissed_task_' + this.taskId);
                    if (dismissed) {
                        const dismissedTime = parseInt(dismissed);
                        const now = Date.now();
                        const threeHours = 3 * 60 * 60 * 1000; // 3 hours in milliseconds

                        if (now - dismissedTime < threeHours) {
                            this.show = false;
                        }
                    }
                },
                dismiss() {
                    this.show = false;
                    localStorage.setItem('dismissed_task_' + this.taskId, Date.now().toString());
                }
            }" x-show="show" x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0"
        x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0"
        x-transition:leave-end="opacity-0 translate-y-4" class="fixed z-40 left-1/2 -translate-x-1/2 bottom-24">
        <div class="relative">
            <a href="<?php echo e(route('user.task.work', $activeTask->task_id)); ?>" wire:navigate
                class="flex items-center gap-2 px-4 py-2 pr-10 bg-gradient-to-r from-orange-500 to-amber-500 text-white text-sm font-medium rounded-full shadow-lg shadow-orange-500/30 hover:shadow-orange-500/50 transition-all">
                <span class="relative flex h-2 w-2">
                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-white opacity-75"></span>
                    <span class="relative inline-flex rounded-full h-2 w-2 bg-white"></span>
                </span>
                Lanjutkan: <?php echo e(Str::limit($activeTask->task->title, 20)); ?>

            </a>

            <!-- Dismiss Button -->
            <button @click.prevent="dismiss()"
                class="absolute top-1/2 -translate-y-1/2 right-2 p-1 hover:bg-white/20 rounded-full transition-colors"
                title="Tutup untuk 3 jam">
                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
    </div>
<?php endif; ?>
```<?php /**PATH C:\laragon\www\baru\resources\views/components/bottom-navigation.blade.php ENDPATH**/ ?>