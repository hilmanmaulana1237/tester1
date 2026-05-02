<div class="min-h-screen bg-gradient-to-br from-white via-white to-white" x-data="{ 
    showFilters: false
}">
    <div class="flex h-full w-full flex-1 flex-col gap-6 p-4 md:p-6">
        <!-- Header Section -->
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h1 class="text-3xl md:text-4xl font-bold text-zinc-900 flex items-center gap-3">
                    <svg class="w-10 h-10 text-violet-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                    </svg>
                    Tugas Aktif Saya
                </h1>
                <p class="text-zinc-600 mt-1">Lanjutkan pekerjaan yang sedang berlangsung</p>
            </div>
            <div class="flex flex-wrap gap-2">
                <button type="button" @click="showFilters = !showFilters" 
                    class="px-4 py-2 rounded-lg bg-white text-zinc-700 border border-zinc-200 hover:bg-zinc-50 transition-all duration-300 flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                    </svg>
                    Filter
                </button>
                <a href="<?php echo e(route('user.dashboard')); ?>" wire:navigate class="px-4 py-2 rounded-lg bg-violet-600 text-white hover:bg-purple-700 transition-all duration-300 flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Cari Tugas Baru
                </a>
            </div>
        </div>

        <!-- Stats Overview -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <!-- Total Active -->
            <div class="bg-white rounded-xl p-4 border border-zinc-200 hover:shadow-lg transition-all duration-300">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-sm text-zinc-600">Tugas Aktif</span>
                    <svg class="w-5 h-5 text-violet-600" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z"></path>
                        <path fill-rule="evenodd" d="M4 5a2 2 0 012-2 3 3 0 003 3h2a3 3 0 003-3 2 2 0 012 2v11a2 2 0 01-2 2H6a2 2 0 01-2-2V5zm3 4a1 1 0 000 2h.01a1 1 0 100-2H7zm3 0a1 1 0 000 2h3a1 1 0 100-2h-3zm-3 4a1 1 0 100 2h.01a1 1 0 100-2H7zm3 0a1 1 0 100 2h3a1 1 0 100-2h-3z" clip-rule="evenodd"></path>
                    </svg>
                </div>
                <p class="text-2xl font-bold text-zinc-900"><?php echo e($stats['total_active']); ?></p>
            </div>

            <!-- In Progress -->
            <div class="bg-white rounded-xl p-4 border border-zinc-200 hover:shadow-lg transition-all duration-300">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-sm text-zinc-600">Dikerjakan</span>
                    <svg class="w-5 h-5 text-orange-600" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"></path>
                    </svg>
                </div>
                <p class="text-2xl font-bold text-orange-600"><?php echo e($stats['in_progress']); ?></p>
            </div>

            <!-- Pending Verification -->
            <div class="bg-white rounded-xl p-4 border border-zinc-200 hover:shadow-lg transition-all duration-300">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-sm text-zinc-600">Direview</span>
                    <svg class="w-5 h-5 text-purple-600" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                    </svg>
                </div>
                <p class="text-2xl font-bold text-purple-600"><?php echo e($stats['pending_verification']); ?></p>
            </div>

            <!-- Near Deadline -->
            <div class="bg-white rounded-xl p-4 border border-zinc-200 hover:shadow-lg transition-all duration-300">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-sm text-zinc-600">Mendesak</span>
                    <svg class="w-5 h-5 text-red-600" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                    </svg>
                </div>
                <p class="text-2xl font-bold text-red-600"><?php echo e($stats['near_deadline']); ?></p>
            </div>
        </div>

        <!-- Filters Panel (Collapsible) -->
        <div x-show="showFilters" x-cloak
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 -translate-y-2"
             x-transition:enter-end="opacity-100 translate-y-0"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100 translate-y-0"
             x-transition:leave-end="opacity-0 -translate-y-2"
             class="bg-white rounded-xl p-4 border border-zinc-200">
            <div class="flex flex-col md:flex-row gap-4">
                <!-- Status Filter -->
                <div class="flex-1">
                    <label class="block text-sm font-medium text-zinc-700 mb-2">Filter Status</label>
                    <div class="flex flex-wrap gap-2">
                        <button wire:click="updateFilter('all')" 
                            wire:loading.class="opacity-50 cursor-wait"
                            wire:target="updateFilter('all')"
                            class="px-4 py-2 rounded-lg text-sm transition-all <?php echo e($filterStatus === 'all' ? 'bg-violet-600 text-white' : 'bg-zinc-100 text-zinc-700 hover:bg-zinc-200'); ?>">
                            <span wire:loading.remove wire:target="updateFilter('all')">Semua</span>
                            <span wire:loading wire:target="updateFilter('all')" style="display: none;">⏳</span>
                        </button>
                        <button wire:click="updateFilter('taken')" 
                            wire:loading.class="opacity-50 cursor-wait"
                            wire:target="updateFilter('taken')"
                            class="px-4 py-2 rounded-lg text-sm transition-all <?php echo e($filterStatus === 'taken' ? 'bg-orange-600 text-white' : 'bg-zinc-100 text-zinc-700 hover:bg-zinc-200'); ?>">
                            <span wire:loading.remove wire:target="updateFilter('taken')">Dikerjakan</span>
                            <span wire:loading wire:target="updateFilter('taken')" style="display: none;">⏳</span>
                        </button>
                        <button wire:click="updateFilter('pending')" 
                            wire:loading.class="opacity-50 cursor-wait"
                            wire:target="updateFilter('pending')"
                            class="px-4 py-2 rounded-lg text-sm transition-all <?php echo e($filterStatus === 'pending' ? 'bg-purple-600 text-white' : 'bg-zinc-100 text-zinc-700 hover:bg-zinc-200'); ?>">
                            <span wire:loading.remove wire:target="updateFilter('pending')">Direview</span>
                            <span wire:loading wire:target="updateFilter('pending')" style="display: none;">⏳</span>
                        </button>
                        <button wire:click="updateFilter('near_deadline')" 
                            wire:loading.class="opacity-50 cursor-wait"
                            wire:target="updateFilter('near_deadline')"
                            class="px-4 py-2 rounded-lg text-sm transition-all <?php echo e($filterStatus === 'near_deadline' ? 'bg-red-600 text-white' : 'bg-zinc-100 text-zinc-700 hover:bg-zinc-200'); ?>">
                            <span wire:loading.remove wire:target="updateFilter('near_deadline')">Mendesak</span>
                            <span wire:loading wire:target="updateFilter('near_deadline')" style="display: none;">⏳</span>
                        </button>
                    </div>
                </div>

                <!-- Sort By -->
                <div class="flex-1">
                    <label class="block text-sm font-medium text-zinc-700 mb-2">Urutkan</label>
                    <div class="flex flex-wrap gap-2">
                        <button wire:click="updateSort('deadline')" 
                            wire:loading.class="opacity-50 cursor-wait"
                            wire:target="updateSort('deadline')"
                            class="px-4 py-2 rounded-lg text-sm transition-all <?php echo e($sortBy === 'deadline' ? 'bg-violet-600 text-white' : 'bg-zinc-100 text-zinc-700 hover:bg-zinc-200'); ?>">
                            <span wire:loading.remove wire:target="updateSort('deadline')">Deadline</span>
                            <span wire:loading wire:target="updateSort('deadline')" style="display: none;">⏳</span>
                        </button>
                        <button wire:click="updateSort('status')" 
                            wire:loading.class="opacity-50 cursor-wait"
                            wire:target="updateSort('status')"
                            class="px-4 py-2 rounded-lg text-sm transition-all <?php echo e($sortBy === 'status' ? 'bg-violet-600 text-white' : 'bg-zinc-100 text-zinc-700 hover:bg-zinc-200'); ?>">
                            <span wire:loading.remove wire:target="updateSort('status')">Status</span>
                            <span wire:loading wire:target="updateSort('status')" style="display: none;">⏳</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tasks List -->
        <!--[if BLOCK]><![endif]--><?php if($tasks->count() > 0): ?>
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 items-start">
            <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $tasks; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $userTask): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <?php
                $task = $userTask->task;
                $hoursLeft = $userTask->deadline_at ? (int) now()->diffInHours($userTask->deadline_at, false) : null;
                $minutesLeft = $userTask->deadline_at ? (int) now()->diffInMinutes($userTask->deadline_at, false) : null;
                $isOverdue = $hoursLeft !== null && $hoursLeft < 0;
                $isUrgent = $hoursLeft !== null && $hoursLeft <= 24 && $hoursLeft >= 0;
                $isVeryUrgent = $hoursLeft !== null && $hoursLeft <= 6 && $hoursLeft >= 0;
                $isCritical = $minutesLeft !== null && $minutesLeft <= 60 && $minutesLeft >= 0;
                
                $isForfeited = $isOverdue;
                
                $statusColor = match(true) {
                    $isForfeited => 'red',
                    $userTask->status === 'taken' => 'orange',
                    in_array($userTask->status, ['pending_verification_1', 'pending_verification_2']) => 'purple',
                    default => 'blue'
                };
                $statusLabel = match(true) {
                    $isForfeited => 'Gagal (Kadaluarsa)',
                    $userTask->status === 'taken' => 'Dikerjakan',
                    $userTask->status === 'pending_verification_1' => 'Direview 1',
                    $userTask->status === 'pending_verification_2' => 'Direview 2',
                    default => 'Aktif'
                };
                
                $timeLeft = '';
                $timeClass = 'text-zinc-500';
                
                if ($userTask->deadline_at) {
                    if ($isOverdue) {
                        $timeLeft = 'Tugas telah gugur';
                        $timeClass = 'text-red-600 font-bold';
                    } elseif ($minutesLeft <= 60) {
                        $timeLeft = $minutesLeft . ' menit lagi';
                        $timeClass = 'text-red-600 font-bold animate-pulse';
                    } elseif ($hoursLeft <= 6) {
                        $remainingMinutes = abs($minutesLeft) % 60;
                        $timeLeft = $hoursLeft . ' jam ' . $remainingMinutes . ' menit lagi';
                        $timeClass = 'text-red-600 font-semibold';
                    } elseif ($hoursLeft <= 24) {
                        $timeLeft = $hoursLeft . ' jam lagi';
                        $timeClass = 'text-orange-600 font-semibold';
                    } elseif ($hoursLeft <= 48) {
                        $timeLeft = $hoursLeft . ' jam lagi';
                        $timeClass = 'text-yellow-600';
                    } else {
                        $daysLeft = (int) floor($hoursLeft / 24);
                        $timeLeft = $daysLeft . ' hari lagi';
                        $timeClass = 'text-violet-600';
                    }
                }
            ?>
            
            <div class="bg-white rounded-xl border border-zinc-200 overflow-hidden transition-all duration-300 flex flex-col <?php echo e($isForfeited ? 'opacity-60 grayscale ring-2 ring-red-500 bg-red-50' : 'hover:shadow-xl group'); ?> <?php echo e(!$isForfeited && $isCritical ? 'ring-2 ring-red-500/70' : ''); ?> <?php echo e(!$isForfeited && $isVeryUrgent && !$isCritical ? 'ring-2 ring-orange-500/50' : ''); ?> <?php echo e(!$isForfeited && $isUrgent && !$isVeryUrgent && !$isCritical ? 'ring-2 ring-yellow-500/50' : ''); ?>">
                <!-- Status Badge - Fixed Height Container -->
                <div class="h-7">
                    <!--[if BLOCK]><![endif]--><?php if($isForfeited): ?>
                    <div class="bg-red-700 text-white text-xs font-bold py-1.5 px-4 text-center h-full flex items-center justify-center gap-2">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                        </svg>
                        <span>❌ GAGAL - Deadline Terlewat</span>
                    </div>
                    <?php elseif($isCritical): ?>
                    <div class="bg-gradient-to-r from-red-600 to-red-500 text-white text-xs font-bold py-1.5 px-4 text-center animate-pulse h-full flex items-center justify-center">
                        🔥 KRITIS - Kurang dari 1 jam!
                    </div>
                    <?php elseif($isVeryUrgent): ?>
                    <div class="bg-gradient-to-r from-orange-600 to-orange-500 text-white text-xs font-bold py-1.5 px-4 text-center h-full flex items-center justify-center">
                        ⏰ MENDESAK - Kurang dari 6 jam!
                    </div>
                    <?php elseif($isUrgent): ?>
                    <div class="bg-gradient-to-r from-yellow-600 to-yellow-500 text-white text-xs font-bold py-1.5 px-4 text-center h-full flex items-center justify-center">
                        ⚡ SEGERA - Kurang dari 24 jam!
                    </div>
                    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                </div>
                
                <!-- Card Header -->
                <div class="p-5 border-b border-zinc-200">
                    <div class="flex items-start justify-between gap-3">
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-2 mb-2 flex-wrap">
                                <span class="px-2.5 py-1 rounded-full text-xs font-medium bg-<?php echo e($statusColor); ?>-100<?php echo e($statusColor); ?>-900/30 text-<?php echo e($statusColor); ?>-700<?php echo e($statusColor); ?>-400">
                                    <?php echo e($statusLabel); ?>

                                </span>
                                <!--[if BLOCK]><![endif]--><?php if($task->category): ?>
                                <span class="px-2.5 py-1 rounded-full text-xs font-medium bg-zinc-100 text-zinc-700">
                                    <?php echo e($task->category->name); ?>

                                </span>
                                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                            </div>
                            <h3 class="text-lg font-bold text-zinc-900 mb-1 line-clamp-2 group-hover:text-violet-600 transition-colors">
                                <?php echo e($task->title); ?>

                            </h3>
                            <div class="text-sm text-zinc-600 line-clamp-2">
                                <?php echo e(strip_tags($task->description)); ?>

                            </div>
                            <!--[if BLOCK]><![endif]--><?php if(strlen(strip_tags($task->description)) > 100): ?>
                            <button wire:click.prevent="viewTask(<?php echo e($userTask->id); ?>)" class="text-violet-600 hover:text-purple-700 text-xs font-medium mt-1 inline-flex items-center gap-1 transition-colors focus:outline-none">
                                Baca selengkapnya
                            </button>
                            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                        </div>
                        <button 
                            wire:click="viewTask(<?php echo e($userTask->id); ?>)" 
                            wire:loading.attr="disabled"
                            wire:loading.class="opacity-50 cursor-wait"
                            wire:target="viewTask(<?php echo e($userTask->id); ?>)"
                            class="flex-shrink-0 p-2 rounded-lg bg-zinc-100 text-zinc-700 hover:bg-blue-100 hover:text-violet-600 transition-all">
                            <!-- Loading Spinner -->
                            <svg wire:loading wire:target="viewTask(<?php echo e($userTask->id); ?>)" style="display: none;" class="w-5 h-5 animate-spin" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            <!-- Eye Icon -->
                            <svg wire:loading.remove wire:target="viewTask(<?php echo e($userTask->id); ?>)" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                        </button>
                    </div>
                </div>

                <!-- Card Body -->
                <div class="p-5 flex-1">
                    <div class="space-y-3 mb-4">
                        <!-- Deadline Info -->
                        <!--[if BLOCK]><![endif]--><?php if($userTask->deadline_at): ?>
                        <div class="flex items-center justify-between text-sm <?php echo e($isOverdue || $isCritical || $isVeryUrgent ? 'bg-red-50 -mx-5 px-5 py-2' : ''); ?>">
                            <div class="flex items-center gap-2 <?php echo e($isOverdue || $isCritical ? 'text-red-600' : ($isVeryUrgent ? 'text-orange-600' : ($isUrgent ? 'text-yellow-600' : 'text-zinc-600'))); ?>">
                                <svg class="w-4 h-4 <?php echo e($isCritical || $isOverdue ? 'animate-pulse' : ''); ?>" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"></path>
                                </svg>
                                <span class="font-medium">Deadline:</span>
                            </div>
                            <div class="text-right">
                                <div class="font-semibold <?php echo e($isOverdue || $isCritical ? 'text-red-600' : ($isVeryUrgent ? 'text-orange-600' : ($isUrgent ? 'text-yellow-600' : 'text-zinc-900'))); ?>">
                                    <?php echo e($userTask->deadline_at->format('d M Y, H:i')); ?>

                                </div>
                                <!--[if BLOCK]><![endif]--><?php if($timeLeft): ?>
                                <div class="text-xs <?php echo e($timeClass); ?>">
                                    <?php echo e($timeLeft); ?>

                                </div>
                                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                            </div>
                        </div>
                        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

                        <!-- Payment Amount -->
                        <div class="flex items-center justify-between text-sm">
                            <div class="flex items-center gap-2 text-zinc-600">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M8.433 7.418c.155-.103.346-.196.567-.267v1.698a2.305 2.305 0 01-.567-.267C8.07 8.34 8 8.114 8 8c0-.114.07-.34.433-.582zM11 12.849v-1.698c.22.071.412.164.567.267.364.243.433.468.433.582 0 .114-.07.34-.433.582a2.305 2.305 0 01-.567.267z"></path>
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-13a1 1 0 10-2 0v.092a4.535 4.535 0 00-1.676.662C6.602 6.234 6 7.009 6 8c0 .99.602 1.765 1.324 2.246.48.32 1.054.545 1.676.662v1.941c-.391-.127-.68-.317-.843-.504a1 1 0 10-1.51 1.31c.562.649 1.413 1.076 2.353 1.253V15a1 1 0 102 0v-.092a4.535 4.535 0 001.676-.662C13.398 13.766 14 12.991 14 12c0-.99-.602-1.765-1.324-2.246A4.535 4.535 0 0011 9.092V7.151c.391.127.68.317.843.504a1 1 0 101.511-1.31c-.563-.649-1.413-1.076-2.354-1.253V5z" clip-rule="evenodd"></path>
                                </svg>
                                <span class="font-medium">Hadiah:</span>
                            </div>
                            <div class="text-right">
                                <!--[if BLOCK]><![endif]--><?php if($userTask->payment_amount): ?>
                                    <span class="font-bold text-violet-600 text-base">
                                        Rp <?php echo e(number_format($userTask->payment_amount, 0, ',', '.')); ?>

                                    </span>
                                <?php elseif($task->estimated_amount): ?>
                                    <span class="font-bold text-violet-600 text-base">
                                        Rp <?php echo e(number_format($task->estimated_amount, 0, ',', '.')); ?>

                                    </span>
                                    <span class="text-xs text-zinc-500 block">(estimasi)</span>
                                <?php else: ?>
                                    <span class="text-zinc-500 text-sm">Belum ditentukan</span>
                                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                            </div>
                        </div>

                        <!-- Taken Date -->
                        <div class="flex items-center justify-between text-sm text-zinc-600">
                            <div class="flex items-center gap-2">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"></path>
                                </svg>
                                <span>Diambil:</span>
                            </div>
                            <span><?php echo e($userTask->taken_at->format('d M Y')); ?></span>
                        </div>
                    </div>

                    <!-- Action Button -->
                    <!--[if BLOCK]><![endif]--><?php if($isForfeited): ?>
                    <div class="w-full flex flex-col items-center justify-center gap-2 px-4 py-3 rounded-lg bg-red-100 text-red-600 font-medium">
                        <div class="flex items-center gap-2">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                            </svg>
                            <span>Tugas Tidak Dapat Dilanjutkan</span>
                        </div>
                        <span class="text-xs text-red-500">Deadline telah terlewat</span>
                    </div>
                    <?php else: ?>
                    <a href="<?php echo e(route('user.task.work', ['task' => $task->id])); ?>" wire:navigate 
                        class="w-full flex items-center justify-center gap-2 px-4 py-2.5 rounded-lg bg-violet-600 text-white hover:bg-purple-700 transition-all duration-300 font-medium group">
                        <span>Lanjutkan Tugas</span>
                        <svg class="w-5 h-5 group-hover:translate-x-1 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6" />
                        </svg>
                    </a>
                    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                </div>
            </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
        </div>
        <?php else: ?>
        <!-- Empty State -->
        <div class="flex flex-col items-center justify-center py-16 px-4">
            <div class="w-64 h-64 mb-6 opacity-50">
                <svg viewBox="0 0 200 200" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-full h-full text-zinc-300">
                    <rect x="40" y="40" width="120" height="140" rx="8" stroke="currentColor" stroke-width="4" fill="none"/>
                    <path d="M60 70H140M60 90H140M60 110H140M60 130H100" stroke="currentColor" stroke-width="4" stroke-linecap="round"/>
                    <circle cx="150" cy="150" r="30" fill="currentColor" opacity="0.2"/>
                    <path d="M140 150L145 155L160 140" stroke="white" stroke-width="4" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </div>
            <h3 class="text-2xl font-bold text-zinc-900 mb-2">Belum Ada Tugas Aktif</h3>
            <p class="text-zinc-600 text-center mb-6 max-w-md">
                Kamu belum memiliki tugas yang sedang dikerjakan. Cari tugas yang tersedia untuk memulai!
            </p>
            <a href="<?php echo e(route('user.dashboard')); ?>" wire:navigate 
                class="px-6 py-3 rounded-lg bg-violet-600 text-white hover:bg-purple-700 transition-all duration-300 flex items-center gap-2 font-medium">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
                Cari Tugas
            </a>
        </div>
        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
    </div>

    <!-- Task Detail Modal -->
    <!--[if BLOCK]><![endif]--><?php if($selectedTask): ?>
    <?php
        $selectedUserTask = $tasks->firstWhere('id', $selectedTask);
        $selectedTaskData = $selectedUserTask?->task;
    ?>
    <!--[if BLOCK]><![endif]--><?php if($selectedUserTask && $selectedTaskData): ?>
    <!-- Overlay -->
    <div class="fixed inset-0 z-40 bg-black/70" wire:click="closeModal"></div>
    
    <!-- Modal -->
    <div class="fixed inset-0 z-50 flex items-center justify-center p-3 sm:p-4 overflow-y-auto">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-2xl mx-auto my-4 overflow-hidden">
            <!-- Modal Header -->
            <div class="bg-gradient-to-r from-violet-600 to-purple-600 px-5 py-4 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="flex-shrink-0 flex items-center justify-center h-10 w-10 rounded-full bg-white/20 backdrop-blur">
                        <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-white">Detail Tugas</h3>
                        <p class="text-violet-100 text-xs"><?php echo e($selectedTaskData->category?->name ?? 'Uncategorized'); ?></p>
                    </div>
                </div>
                <button wire:click="closeModal" class="p-2 rounded-lg hover:bg-white/20 transition-colors">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            
            <!-- Modal Content -->
            <div class="p-5 sm:p-6 space-y-4 max-h-[60vh] overflow-y-auto">
                <!-- Title -->
                <div>
                    <h4 class="text-xl font-bold text-zinc-900 mb-2">
                        <?php echo e($selectedTaskData->title); ?>

                    </h4>
                    <div class="flex flex-wrap gap-2">
                        <span class="px-3 py-1 rounded-full text-xs font-medium 
                            <?php if($selectedUserTask->status === 'taken'): ?> bg-orange-100 text-orange-700
                            <?php elseif(in_array($selectedUserTask->status, ['pending_verification_1', 'pending_verification_2'])): ?> bg-purple-100 text-purple-700
                            <?php else: ?> bg-blue-100 text-blue-700
                            <?php endif; ?>">
                            <?php echo e(\App\Models\UserTask::STATUSES[$selectedUserTask->status] ?? $selectedUserTask->status); ?>

                        </span>
                        <!--[if BLOCK]><![endif]--><?php if($selectedTaskData->difficulty_level): ?>
                        <span class="px-3 py-1 rounded-full text-xs font-medium bg-zinc-100 text-zinc-700">
                            <?php echo e(\App\Models\Task::DIFFICULTIES[$selectedTaskData->difficulty_level] ?? $selectedTaskData->difficulty_level); ?>

                        </span>
                        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                    </div>
                </div>
                
                <!-- Description -->
                <div class="bg-zinc-50 rounded-xl p-4">
                    <h5 class="text-sm font-semibold text-zinc-700 mb-2 flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7"></path>
                        </svg>
                        Deskripsi
                    </h5>
                    <div class="text-sm text-zinc-600 prose prose-sm max-w-none prose-violet prose-p:my-1 prose-ul:my-1 text-left">
                        <?php echo $selectedTaskData->description; ?>

                    </div>
                </div>
                
                <!-- Info Grid -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <!-- Deadline -->
                    <!--[if BLOCK]><![endif]--><?php if($selectedUserTask->deadline_at): ?>
                    <div class="bg-amber-50 border border-amber-200 rounded-xl p-3">
                        <div class="flex items-center gap-2 mb-1">
                            <svg class="w-4 h-4 text-amber-600" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"></path>
                            </svg>
                            <span class="text-xs font-medium text-amber-700">Deadline</span>
                        </div>
                        <p class="text-sm font-bold text-amber-800"><?php echo e($selectedUserTask->deadline_at->format('d M Y, H:i')); ?></p>
                        <p class="text-xs text-amber-600"><?php echo e($selectedUserTask->deadline_at->diffForHumans()); ?></p>
                    </div>
                    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                    
                    <!-- Payment -->
                    <div class="bg-violet-50 border border-violet-200 rounded-xl p-3">
                        <div class="flex items-center gap-2 mb-1">
                            <svg class="w-4 h-4 text-violet-600" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M8.433 7.418c.155-.103.346-.196.567-.267v1.698a2.305 2.305 0 01-.567-.267C8.07 8.34 8 8.114 8 8c0-.114.07-.34.433-.582zM11 12.849v-1.698c.22.071.412.164.567.267.364.243.433.468.433.582 0 .114-.07.34-.433.582a2.305 2.305 0 01-.567.267z"></path>
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-13a1 1 0 10-2 0v.092a4.535 4.535 0 00-1.676.662C6.602 6.234 6 7.009 6 8c0 .99.602 1.765 1.324 2.246.48.32 1.054.545 1.676.662v1.941c-.391-.127-.68-.317-.843-.504a1 1 0 10-1.51 1.31c.562.649 1.413 1.076 2.353 1.253V15a1 1 0 102 0v-.092a4.535 4.535 0 001.676-.662C13.398 13.766 14 12.991 14 12c0-.99-.602-1.765-1.324-2.246A4.535 4.535 0 0011 9.092V7.151c.391.127.68.317.843.504a1 1 0 101.511-1.31c-.563-.649-1.413-1.076-2.354-1.253V5z" clip-rule="evenodd"></path>
                            </svg>
                            <span class="text-xs font-medium text-purple-700">Hadiah</span>
                        </div>
                        <!--[if BLOCK]><![endif]--><?php if($selectedUserTask->payment_amount): ?>
                        <p class="text-sm font-bold text-purple-800">Rp <?php echo e(number_format($selectedUserTask->payment_amount, 0, ',', '.')); ?></p>
                        <?php elseif($selectedTaskData->estimated_amount): ?>
                        <p class="text-sm font-bold text-purple-800">Rp <?php echo e(number_format($selectedTaskData->estimated_amount, 0, ',', '.')); ?></p>
                        <p class="text-xs text-violet-600">(estimasi)</p>
                        <?php else: ?>
                        <p class="text-sm text-violet-600">Belum ditentukan</p>
                        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                    </div>
                    
                    <!-- Taken Date -->
                    <div class="bg-blue-50 border border-blue-200 rounded-xl p-3">
                        <div class="flex items-center gap-2 mb-1">
                            <svg class="w-4 h-4 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"></path>
                            </svg>
                            <span class="text-xs font-medium text-blue-700">Diambil</span>
                        </div>
                        <p class="text-sm font-bold text-blue-800"><?php echo e($selectedUserTask->taken_at->format('d M Y, H:i')); ?></p>
                    </div>
                    
                    <!-- WhatsApp Link -->
                    <!--[if BLOCK]><![endif]--><?php if($selectedTaskData->whatsapp_group_link): ?>
                    <div class="bg-purple-50 border border-purple-200 rounded-xl p-3">
                        <div class="flex items-center gap-2 mb-1">
                            <svg class="w-4 h-4 text-purple-600" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
                            </svg>
                            <span class="text-xs font-medium text-purple-700">Link Grup</span>
                        </div>
                        <a href="<?php echo e($selectedTaskData->whatsapp_group_link); ?>" target="_blank" class="text-sm font-bold text-purple-800 hover:underline truncate block">
                            Buka Link WhatsApp →
                        </a>
                    </div>
                    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                </div>
            </div>
            
            <!-- Modal Footer -->
            <div class="bg-zinc-100 px-5 py-4 flex flex-col sm:flex-row gap-3">
                <a href="<?php echo e(route('user.task.work', ['task' => $selectedTaskData->id])); ?>" wire:navigate 
                    class="flex-1 inline-flex justify-center items-center gap-2 rounded-xl px-4 py-3 bg-violet-600 text-white font-semibold hover:bg-purple-700 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
                    </svg>
                    Lanjutkan Tugas
                </a>
                <button wire:click="closeModal" type="button" 
                    wire:loading.class="opacity-50 cursor-wait"
                    wire:target="closeModal"
                    class="flex-1 sm:flex-none inline-flex justify-center items-center gap-2 rounded-xl px-4 py-3 bg-white border-2 border-zinc-200 text-zinc-700 font-semibold hover:bg-zinc-50 transition-colors">
                    <span wire:loading.remove wire:target="closeModal">Tutup</span>
                    <span wire:loading wire:target="closeModal" style="display: none;">Menutup...</span>
                </button>
            </div>
        </div>
    </div>
    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
</div>
<?php /**PATH C:\laragon\www\baru\resources\views/livewire/my-tasks.blade.php ENDPATH**/ ?>