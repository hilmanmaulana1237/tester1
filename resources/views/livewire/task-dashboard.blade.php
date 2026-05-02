<div>
    <!-- Android/Mobile View -->
    <div class="block sm:hidden">
        <div class="flex h-full w-full flex-1 flex-col gap-4 p-3 text-base-content">
            @if($viewMode === 'categories')
                <!-- Header -->
                <div class="flex flex-col gap-3">
                    <div class="space-y-1">
                        <h1 class="text-2xl font-bold text-violet-600">Pilih Kategori Task</h1>
                        <p class="text-sm text-muted-foreground">Pilih kategori untuk melihat task yang tersedia</p>
                    </div>
                    <div class="flex items-center justify-end gap-2">
                        @if(Route::has('pages.tutorial-page'))
                            <a href="{{ Route::has('pages.tutorial-page') ? route('pages.tutorial-page') : url('/pages/tutorial-page') }}"
                                class="inline-flex items-center gap-2 px-4 py-2.5 bg-violet-600 border border-transparent rounded-lg text-white hover:bg-purple-700 transition-colors w-full justify-center shadow-lg shadow-violet-500/20">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z">
                                    </path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                Panduan
                            </a>
                        @endif
                        <a href="{{ route('user.history') }}"
                            class="inline-flex items-center gap-2 px-4 py-2.5 bg-white border border-zinc-200 rounded-lg text-zinc-700 hover:bg-zinc-50 transition-colors w-full justify-center">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            History
                        </a>
                    </div>
                </div>
                <!-- Category Cards -->
                <div class="grid grid-cols-1 gap-4">
                    @forelse($categories as $category)
                        @php
                            $isPremiumAdmin = $category->createdBy && $category->createdBy->badge === 'premium_admin';
                        @endphp
                        <div wire:click="selectCategory({{ $category->id }})" wire:loading.class="opacity-50 cursor-wait"
                            wire:target="selectCategory({{ $category->id }})"
                            class="bg-white rounded-xl border {{ $isPremiumAdmin ? 'border-amber-400 ring-2 ring-amber-200' : 'border-zinc-200' }} p-4 hover:shadow-lg transition-all duration-200 cursor-pointer group hover:border-violet-300 relative overflow-hidden">

                            @if($isPremiumAdmin)
                                <div class="absolute top-0 right-0">
                                    <div
                                        class="bg-gradient-to-l from-amber-500 to-yellow-400 text-white text-xs font-bold px-3 py-1 rounded-bl-lg shadow-sm">
                                        ⭐ Terverifikasi
                                    </div>
                                </div>
                            @endif

                            <div class="flex items-center gap-3">
                                @php
                                    $words = explode(' ', trim($category->name));
                                    $initials = count($words) >= 2
                                        ? strtoupper(substr($words[0], 0, 1) . substr($words[1], 0, 1))
                                        : strtoupper(substr($category->name, 0, 2));
                                @endphp
                                <div
                                    class="w-10 h-10 {{ $isPremiumAdmin ? 'bg-gradient-to-br from-amber-400 to-yellow-500' : 'bg-gradient-to-br from-blue-500 to-purple-600' }} rounded-lg flex items-center justify-center text-white font-bold text-sm group-hover:scale-110 transition-transform duration-200">
                                    {{ $initials }}
                                </div>
                                <div class="flex-1">
                                    <h3 class="font-bold text-base text-zinc-900 group-hover:text-violet-600 transition-colors">
                                        {{ $category->name }}
                                    </h3>
                                    <div class="flex items-center gap-2 text-xs">
                                        <span class="text-zinc-600">{{ $category->available_tasks_count }} tersedia</span>
                                        @if($category->in_progress_count > 0)
                                            <span class="text-amber-600">• {{ $category->in_progress_count }} dikerjakan</span>
                                        @endif
                                    </div>
                                </div>
                                <div
                                    class="w-8 h-8 bg-zinc-100 rounded-full flex items-center justify-center group-hover:bg-violet-100 transition-colors">
                                    <!-- Loading spinner -->
                                    <svg wire:loading wire:target="selectCategory({{ $category->id }})" style="display: none;"
                                        class="w-4 h-4 animate-spin text-violet-600" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                            stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor"
                                            d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                        </path>
                                    </svg>
                                    <!-- Arrow icon -->
                                    <svg wire:loading.remove wire:target="selectCategory({{ $category->id }})"
                                        class="w-4 h-4 text-zinc-600 group-hover:text-violet-600" fill="none"
                                        stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7">
                                        </path>
                                    </svg>
                                </div>
                            </div>

                            <!-- Admin Info -->
                            @if($category->createdBy)
                                <div
                                    class="mt-3 flex items-center gap-2 text-xs {{ $isPremiumAdmin ? 'text-amber-600' : 'text-zinc-500' }}">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                    </svg>
                                    <span>Admin: <strong>{{ $category->createdBy->name }}</strong></span>
                                    @if($isPremiumAdmin)
                                        <span
                                            class="px-1.5 py-0.5 bg-amber-100 text-amber-700 rounded text-[10px] font-semibold">PREMIUM</span>
                                    @endif
                                </div>
                            @endif

                            @if($category->description)
                                <p class="mt-2 text-xs text-zinc-600 line-clamp-2">
                                    {{ $category->description }}
                                </p>
                            @endif
                        </div>
                    @empty
                        <div class="col-span-full">
                            <div class="text-center py-8">
                                <div class="w-16 h-16 bg-zinc-100 rounded-full flex items-center justify-center mx-auto mb-3">
                                    <svg class="w-8 h-8 text-zinc-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10">
                                        </path>
                                    </svg>
                                </div>
                                <h3 class="text-base font-medium text-zinc-900 mb-1">Tidak Ada Kategori Dengan Task Tersedia
                                </h3>
                                <p class="text-sm text-zinc-600">Saat ini belum ada kategori yang memiliki task tersedia.</p>
                            </div>
                        </div>
                    @endforelse
                </div>
            @else
                <!-- Header for Tasks View -->
                <div class="flex flex-col gap-3">
                    <!-- Back Button Row -->
                    <div class="flex items-center">
                        <button wire:click="backToCategories" wire:loading.class="opacity-50 cursor-wait"
                            wire:target="backToCategories"
                            class="inline-flex items-center gap-1.5 px-3 py-2 bg-white border border-zinc-200 rounded-lg text-zinc-700 hover:bg-violet-50 transition-colors text-sm">
                            <!-- Loading spinner -->
                            <svg wire:loading wire:target="backToCategories" style="display: none;"
                                class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4">
                                </circle>
                                <path class="opacity-75" fill="currentColor"
                                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                </path>
                            </svg>
                            <!-- Back arrow -->
                            <svg wire:loading.remove wire:target="backToCategories" class="w-4 h-4" fill="none"
                                stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7">
                                </path>
                            </svg>
                            <span wire:loading.remove wire:target="backToCategories">Kembali</span>
                            <span wire:loading wire:target="backToCategories" style="display: none;">Loading...</span>
                        </button>
                    </div>

                    <!-- Category Title Card -->
                    <div class="bg-gradient-to-r from-violet-50 to-purple-50 border border-violet-200 rounded-xl p-4">
                        <h1 class="text-xl font-bold text-zinc-900 text-center">{{ $selectedCategoryName }}</h1>
                        <p class="text-xs text-zinc-600 text-center mt-1">Task yang tersedia dalam kategori ini</p>
                    </div>

                    <!-- Admin Info Card -->
                    @if($selectedCategoryAdmin)
                        @php
                            $isPremiumAdmin = $selectedCategoryAdmin->badge === 'premium_admin';
                        @endphp
                        <div
                            class="bg-gradient-to-r {{ $isPremiumAdmin ? 'from-amber-50 to-yellow-50 border-amber-200' : 'from-blue-50 to-indigo-50 border-blue-200' }} border rounded-xl p-3">
                            <div class="flex items-center gap-3">
                                <div
                                    class="w-10 h-10 {{ $isPremiumAdmin ? 'bg-gradient-to-br from-amber-400 to-yellow-500' : 'bg-gradient-to-br from-blue-500 to-indigo-600' }} rounded-full flex items-center justify-center text-white font-bold text-sm shadow-lg">
                                    {{ strtoupper(substr($selectedCategoryAdmin->name, 0, 2)) }}
                                </div>
                                <div class="flex-1">
                                    <div class="flex items-center gap-2">
                                        <span
                                            class="font-semibold text-sm text-zinc-900">{{ $selectedCategoryAdmin->name }}</span>
                                        @if($isPremiumAdmin)
                                            <span class="px-1.5 py-0.5 bg-amber-100 text-amber-700 rounded text-[10px] font-bold">⭐
                                                Terverifikasi</span>
                                        @endif
                                    </div>
                                    <p class="text-xs text-zinc-500">Admin Pembuat Kategori</p>
                                </div>
                                @if($selectedCategoryAdmin->whatsapp)
                                    <a href="https://wa.me/{{ $selectedCategoryAdmin->whatsapp }}" target="_blank"
                                        class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-violet-500 hover:bg-violet-600 text-white rounded-lg text-xs font-medium transition-colors shadow-sm">
                                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                            <path
                                                d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z" />
                                        </svg>
                                        Hubungi
                                    </a>
                                @endif
                            </div>
                        </div>
                    @endif

                    <div class="flex items-center justify-end">
                        <a href="{{ route('user.history') }}"
                            class="inline-flex items-center gap-2 px-4 py-2.5 bg-white border border-zinc-200 rounded-lg text-zinc-700 hover:bg-zinc-50 transition-colors w-full justify-center">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            Task History
                        </a>
                    </div>
                </div>
                <!-- Task Filters (only show when viewing tasks) -->
                <div class="bg-white rounded-xl border border-zinc-200 p-4">
                    <div class="grid grid-cols-1 gap-3">
                        <!-- Search -->
                        <div>
                            <label class="block text-xs font-medium text-zinc-700 mb-1">Cari Task</label>
                            <input type="text" wire:model.live="search" placeholder="Cari task..."
                                class="w-full px-3 py-2.5 border border-zinc-300 rounded-md focus:outline-none focus:ring-2 focus:ring-violet-500 text-sm" />
                        </div>
                        <!-- Status Filter -->
                        <div>
                            <label class="block text-xs font-medium text-zinc-700 mb-1">Status</label>
                            <select wire:model.live="filter"
                                class="w-full px-3 py-2.5 border border-zinc-300 rounded-md focus:outline-none focus:ring-2 focus:ring-violet-500 text-sm">
                                <option value="available">Task Tersedia</option>
                                <option value="my_tasks">Task Saya (Dikerjakan)</option>
                                <option value="completed">Task Selesai</option>
                                <option value="failed">Task Gagal/Dibatalkan</option>
                            </select>
                        </div>
                    </div>
                </div>
                <!-- Tasks Grid (only show when viewing tasks) -->
                @if($viewMode === 'tasks')
                    <div class="grid grid-cols-1 gap-4">
                        @forelse($tasks as $task)
                            @php
                                $userTask = $task->userTasks->where('user_id', auth()->id())->first();
                            @endphp
                            <div class="bg-white rounded-xl border border-zinc-200 p-4 space-y-3">
                                <!-- Category Badge and Difficulty -->
                                <div class="flex items-center justify-between">
                                    <span class="px-2 py-1 bg-violet-100 text-blue-800 rounded text-xs font-medium">
                                        {{ $task->category->name }}
                                    </span>
                                    <span class="px-2 py-1 rounded text-xs font-medium
                                                                   @if($task->difficulty_level === 'easy') bg-violet-100 text-purple-800
                                                                   @elseif($task->difficulty_level === 'medium') bg-yellow-100 text-yellow-800
                                                                   @else bg-red-100 text-red-800 @endif">
                                        {{ ucfirst($task->difficulty_level) }}
                                    </span>
                                </div>
                                <!-- Task Title -->
                                <h3 class="text-base font-semibold text-zinc-900 line-clamp-2">
                                    {{ $task->title }}
                                </h3>
                                <!-- Task Description -->
                                <p class="text-xs text-zinc-600 line-clamp-3">
                                    {{ Str::limit(strip_tags($task->description), 100) }}
                                </p>
                                <!-- Admin Info -->
                                <div class="flex items-center gap-2 p-2 bg-zinc-50 rounded-lg">
                                    <div
                                        class="w-6 h-6 bg-gradient-to-br from-blue-500 to-purple-600 rounded-full flex items-center justify-center">
                                        <span
                                            class="text-white text-xs font-bold">{{ substr($task->creator->name ?? 'A', 0, 1) }}</span>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-xs font-medium text-zinc-900 truncate">
                                            {{ $task->creator->name ?? 'Admin' }}
                                        </p>
                                    </div>
                                    @if($task->creator && $task->creator->badge === 'premium_admin')
                                        <span
                                            class="inline-flex items-center gap-1 px-1.5 py-0.5 bg-amber-100 text-amber-700 rounded text-xs font-medium">
                                            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                                <path
                                                    d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z">
                                                </path>
                                            </svg>
                                            Terverifikasi
                                        </span>
                                    @endif
                                </div>
                                <!-- Estimasi Nominal -->
                                @if($task->estimated_amount)
                                    <div class="flex items-center gap-2">
                                        <span class="text-xs text-zinc-500">💰 Estimasi Pendapatan Anda:</span>
                                        <span class="text-sm font-bold text-violet-600">
                                            Rp {{ number_format($task->estimated_amount, 0, ',', '.') }}
                                        </span>
                                    </div>
                                @endif
                                <!-- UserTask Status (untuk my_tasks, completed, failed) -->
                                @if($userTask && in_array($filter, ['my_tasks', 'completed', 'failed']))
                                    <div class="border-t border-zinc-200 pt-3 space-y-2">
                                        <!-- Status Badge -->
                                        <div class="flex items-center justify-between">
                                            <span class="text-xs font-medium text-zinc-700">Status:</span>
                                            <span class="px-2 py-1 rounded-full text-xs font-semibold
                                                                                   @if($userTask->isOverdue()) bg-red-100 text-red-800
                                                                                   @elseif($userTask->status === 'taken') bg-yellow-100 text-yellow-800
                                                                                   @elseif($userTask->status === 'pending_verification_1') bg-orange-100 text-orange-800
                                                                                   @elseif($userTask->status === 'pending_verification_2') bg-purple-100 text-purple-800
                                                                                   @elseif($userTask->status === 'completed') bg-violet-100 text-purple-800
                                                                                   @elseif($userTask->status === 'failed') bg-red-100 text-red-800
                                                                                   @elseif($userTask->status === 'cancelled') bg-gray-100 text-gray-800
                                                                                   @else bg-gray-100 text-gray-800 @endif">
                                                {{ $userTask->isOverdue() ? 'Gagal (Kadaluarsa)' : \App\Models\UserTask::STATUSES[$userTask->status] }}
                                            </span>
                                        </div>
                                        <!-- Payment Info (untuk completed) -->
                                        @if($userTask->status === 'completed')
                                            <div class="flex items-center justify-between">
                                                <span class="text-xs font-medium text-zinc-700">Payment:</span>
                                                <div class="text-right">
                                                    <div
                                                        class="px-2 py-1 rounded-full text-xs font-semibold
                                                                                                   @if($userTask->payment_status === 'success') bg-violet-100 text-purple-800
                                                                                                   @elseif($userTask->payment_status === 'failed') bg-red-100 text-red-800
                                                                                                   @else bg-yellow-100 text-yellow-800 @endif">
                                                        {{ \App\Models\UserTask::PAYMENT_STATUSES[$userTask->payment_status] }}
                                                    </div>
                                                    @if($userTask->payment_amount)
                                                        <div class="text-xs font-bold text-violet-600 mt-1">
                                                            Rp {{ number_format($userTask->payment_amount, 0, ',', '.') }}
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                        @endif
                                        <!-- Deadline Info -->
                                        @if($userTask->deadline_at)
                                            <div class="flex items-center justify-between">
                                                <span class="text-xs font-medium text-zinc-700">Deadline:</span>
                                                <span
                                                    class="text-xs {{ $userTask->isOverdue() ? 'text-red-600 font-bold' : 'text-zinc-600' }}">
                                                    {{ $userTask->deadline_at->format('d M Y H:i') }}
                                                    @if($userTask->isOverdue())
                                                        <span class="text-xs">(Overdue)</span>
                                                    @endif
                                                </span>
                                            </div>
                                        @endif
                                        <!-- Failed Count (untuk failed) -->
                                        @if($userTask->status === 'failed' && $userTask->failed_count > 0)
                                            <div class="flex items-center justify-between">
                                                <span class="text-xs font-medium text-zinc-700">Failed Attempts:</span>
                                                <span class="text-xs text-red-600 font-bold">{{ $userTask->failed_count }}x</span>
                                            </div>
                                        @endif
                                    </div>
                                @endif
                                <!-- Task Info (untuk available tasks) -->
                                @if($filter === 'available')
                                    <div class="space-y-1">
                                        <div class="flex items-center text-xs text-zinc-600">
                                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                            Expired: {{ $task->expired_at->format('d M Y H:i') }}
                                        </div>
                                        <div class="flex items-center text-xs text-zinc-600">
                                        </div>
                                    </div>
                                @endif
                                <!-- Action Button -->
                                @if($filter === 'available')
                                    @if($task->isTaken())
                                        <button disabled
                                            class="w-full px-4 py-2.5 bg-gray-300 text-gray-500 rounded-md cursor-not-allowed text-sm">
                                            Sudah Diambil
                                        </button>
                                    @elseif($task->isExpired())
                                        <button disabled
                                            class="w-full px-4 py-2.5 bg-gray-300 text-gray-500 rounded-md cursor-not-allowed text-sm">
                                            Task Expired
                                        </button>
                                    @else
                                        <button wire:click="takeTask({{ $task->id }})" wire:loading.attr="disabled"
                                            wire:loading.class="opacity-50 cursor-wait" wire:target="takeTask({{ $task->id }})"
                                            class="w-full px-4 py-2.5 bg-violet-600 text-white rounded-md hover:bg-purple-700 transition-colors text-sm flex items-center justify-center gap-2">
                                            <svg wire:loading wire:target="takeTask({{ $task->id }})" style="display: none;"
                                                class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4">
                                                </circle>
                                                <path class="opacity-75" fill="currentColor"
                                                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                                </path>
                                            </svg>
                                            <span wire:loading.remove wire:target="takeTask({{ $task->id }})">Ambil Task</span>
                                            <span wire:loading wire:target="takeTask({{ $task->id }})"
                                                style="display: none;">Memproses...</span>
                                        </button>
                                    @endif
                                @elseif($filter === 'my_tasks')
                                    @php
                                        $userTask = $task->userTasks->where('user_id', auth()->id())->first();
                                    @endphp
                                    @if($userTask && $userTask->isOverdue())
                                        <div
                                            class="block w-full px-4 py-2.5 bg-red-100 text-red-600 rounded-md text-center text-sm font-semibold">
                                            Tidak Bisa Dilanjutkan (Deadline Terlewat)
                                        </div>
                                    @else
                                        <a href="{{ route('user.task.work', $task->id) }}"
                                            class="block w-full px-4 py-2.5 bg-violet-600 text-white rounded-md hover:bg-purple-700 transition-colors text-center text-sm">
                                            Lanjutkan Pengerjaan
                                        </a>
                                    @endif
                                @elseif($filter === 'completed')
                                    <a href="{{ route('user.task.work', $task->id) }}"
                                        class="block w-full px-4 py-2.5 bg-violet-600 text-white rounded-md hover:bg-purple-700 transition-colors text-center text-sm">
                                        Lihat Status & Detail
                                    </a>
                                @elseif($filter === 'failed')
                                    <div class="space-y-2">
                                        @if($userTask->status === 'failed')
                                            <a href="{{ route('user.task.work', $task->id) }}"
                                                class="block w-full px-4 py-2.5 bg-orange-600 text-white rounded-md hover:bg-orange-700 transition-colors text-center text-sm">
                                                Lihat Detail Kegagalan
                                            </a>
                                        @elseif($userTask->status === 'cancelled')
                                            <div class="text-gray-600 font-medium text-center py-2 text-sm">
                                                Task Dibatalkan
                                            </div>
                                        @endif
                                    </div>
                                @endif
                            </div>
                        @empty
                            <div class="col-span-full">
                                <div class="bg-white rounded-xl border border-zinc-200 text-center py-8">
                                    <svg class="w-12 h-12 text-zinc-400 mx-auto mb-3" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1"
                                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                                        </path>
                                    </svg>
                                    <h3 class="text-base font-semibold text-zinc-900 mb-1">Tidak ada task ditemukan</h3>
                                    <p class="text-sm text-zinc-600">Coba ubah filter atau kata kunci pencarian</p>
                                </div>
                            </div>
                        @endforelse
                    </div>
                    <!-- Pagination -->
                    @if($tasks->hasPages())
                        <div class="flex justify-center">
                            {{ $tasks->links() }}
                        </div>
                    @endif
                @endif
                <!-- Flash Messages -->
                @if (session()->has('success'))
                    <div class="fixed bottom-4 right-4 left-4 z-50">
                        <div class="bg-violet-500 text-white px-4 py-3 rounded-md shadow-lg text-center">
                            {{ session('success') }}
                        </div>
                    </div>
                @endif
                @if (session()->has('error'))
                    <div class="fixed bottom-4 right-4 left-4 z-50">
                        <div class="bg-red-500 text-white px-4 py-3 rounded-md shadow-lg text-center">
                            {{ session('error') }}
                        </div>
                    </div>
                @endif
            @endif
        </div>
    </div>

    <!-- Desktop View -->
    <div class="hidden sm:block">
        <div class="flex h-full w-full flex-1 flex-col gap-6 p-4 md:p-6 bg-base-100 text-base-content">
            @if($viewMode === 'categories')
                <!-- Header -->
                <div class="flex items-center justify-between">
                    <div class="space-y-2">
                        <h1 class="text-3xl font-bold text-violet-600">Pilih Kategori Task</h1>
                        <p class="text-muted-foreground">Pilih kategori untuk melihat task yang tersedia</p>
                    </div>
                    <div class="flex items-center gap-3">
                        @if(Route::has('pages.tutorial-page'))
                            <a href="{{ Route::has('pages.tutorial-page') ? route('pages.tutorial-page') : url('/pages/tutorial-page') }}"
                                class="inline-flex items-center gap-2 px-4 py-2 bg-violet-600 border border-transparent rounded-lg text-white hover:bg-purple-700 transition-colors shadow-lg shadow-violet-500/20">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z">
                                    </path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                Panduan
                            </a>
                        @endif
                        <a href="{{ route('user.history') }}"
                            class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-zinc-200 rounded-lg text-zinc-700 hover:bg-zinc-50 transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            Task History
                        </a>
                    </div>
                </div>
                <!-- Category Cards -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                    @forelse($categories as $category)
                        @php
                            $isPremiumAdmin = $category->createdBy && $category->createdBy->badge === 'premium_admin';
                        @endphp
                        <div wire:click="selectCategory({{ $category->id }})" wire:loading.class="opacity-50 cursor-wait"
                            wire:target="selectCategory({{ $category->id }})"
                            class="bg-white rounded-xl border {{ $isPremiumAdmin ? 'border-amber-400 ring-2 ring-amber-200' : 'border-zinc-200' }} p-6 hover:shadow-lg transition-all duration-200 cursor-pointer group hover:border-violet-300 relative overflow-hidden">

                            @if($isPremiumAdmin)
                                <div class="absolute top-0 right-0">
                                    <div
                                        class="bg-gradient-to-l from-amber-500 to-yellow-400 text-white text-xs font-bold px-4 py-1.5 rounded-bl-xl shadow-md">
                                        ⭐ Terverifikasi
                                    </div>
                                </div>
                            @endif

                            <div class="flex items-center gap-4">
                                @php
                                    $words = explode(' ', trim($category->name));
                                    $initials = count($words) >= 2
                                        ? strtoupper(substr($words[0], 0, 1) . substr($words[1], 0, 1))
                                        : strtoupper(substr($category->name, 0, 2));
                                @endphp
                                <div
                                    class="w-12 h-12 {{ $isPremiumAdmin ? 'bg-gradient-to-br from-amber-400 to-yellow-500' : 'bg-gradient-to-br from-blue-500 to-purple-600' }} rounded-lg flex items-center justify-center text-white font-bold text-lg group-hover:scale-110 transition-transform duration-200 shadow-lg">
                                    {{ $initials }}
                                </div>
                                <div class="flex-1">
                                    <h3 class="font-bold text-lg text-zinc-900 group-hover:text-violet-600 transition-colors">
                                        {{ $category->name }}
                                    </h3>
                                    <div class="flex items-center gap-2 text-sm">
                                        <span class="text-zinc-600">{{ $category->available_tasks_count }} task tersedia</span>
                                        @if($category->in_progress_count > 0)
                                            <span class="text-amber-600">• {{ $category->in_progress_count }} dikerjakan</span>
                                        @endif
                                    </div>
                                </div>
                                <div
                                    class="w-8 h-8 bg-zinc-100 rounded-full flex items-center justify-center group-hover:bg-violet-100 transition-colors">
                                    <!-- Loading spinner -->
                                    <svg wire:loading wire:target="selectCategory({{ $category->id }})" style="display: none;"
                                        class="w-4 h-4 animate-spin text-violet-600" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                            stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor"
                                            d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                        </path>
                                    </svg>
                                    <!-- Arrow icon -->
                                    <svg wire:loading.remove wire:target="selectCategory({{ $category->id }})"
                                        class="w-4 h-4 text-zinc-600 group-hover:text-violet-600" fill="none"
                                        stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7">
                                        </path>
                                    </svg>
                                </div>
                            </div>

                            <!-- Admin Info -->
                            @if($category->createdBy)
                                <div
                                    class="mt-4 flex items-center gap-2 text-sm {{ $isPremiumAdmin ? 'text-amber-600' : 'text-zinc-500' }}">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                    </svg>
                                    <span>Admin: <strong>{{ $category->createdBy->name }}</strong></span>
                                    @if($isPremiumAdmin)
                                        <span
                                            class="px-2 py-0.5 bg-amber-100 text-amber-700 rounded text-xs font-semibold">PREMIUM</span>
                                    @endif
                                </div>
                            @endif

                            @if($category->description)
                                <p class="mt-3 text-sm text-zinc-600 line-clamp-2">
                                    {{ $category->description }}
                                </p>
                            @endif
                        </div>
                    @empty
                        <div class="col-span-full">
                            <div class="text-center py-12">
                                <div class="w-24 h-24 bg-zinc-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                    <svg class="w-12 h-12 text-zinc-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10">
                                        </path>
                                    </svg>
                                </div>
                                <h3 class="text-lg font-medium text-zinc-900 mb-2">Tidak Ada Kategori Dengan Task Tersedia</h3>
                                <p class="text-zinc-600">Saat ini belum ada kategori yang memiliki task tersedia.</p>
                            </div>
                        </div>
                    @endforelse
                </div>
            @else
                <!-- Header for Tasks View -->
                <div class="flex flex-col gap-4">
                    <div class="flex items-center justify-between">
                        <button wire:click="backToCategories" wire:loading.class="opacity-50 cursor-wait"
                            wire:target="backToCategories"
                            class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-zinc-200 rounded-lg text-zinc-700 hover:bg-zinc-50 transition-colors">
                            <!-- Loading spinner -->
                            <svg wire:loading wire:target="backToCategories" style="display: none;"
                                class="w-5 h-5 animate-spin" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4">
                                </circle>
                                <path class="opacity-75" fill="currentColor"
                                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                </path>
                            </svg>
                            <!-- Back arrow -->
                            <svg wire:loading.remove wire:target="backToCategories" class="w-5 h-5" fill="none"
                                stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7">
                                </path>
                            </svg>
                            <span wire:loading.remove wire:target="backToCategories">Kembali ke Kategori</span>
                            <span wire:loading wire:target="backToCategories" style="display: none;">Loading...</span>
                        </button>
                        @if(Route::has('pages.tutorial-page'))
                            <a href="{{ Route::has('pages.tutorial-page') ? route('pages.tutorial-page') : url('/pages/tutorial-page') }}"
                                class="inline-flex items-center gap-2 px-4 py-2 bg-violet-600 border border-transparent rounded-lg text-white hover:bg-purple-700 transition-colors shadow-lg shadow-violet-500/20">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z">
                                    </path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                Panduan
                            </a>
                        @endif
                        <a href="{{ route('user.history') }}"
                            class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-zinc-200 rounded-lg text-zinc-700 hover:bg-zinc-50 transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            Task History
                        </a>
                    </div>

                    <!-- Category Title Card -->
                    <div class="bg-gradient-to-r from-violet-50 to-purple-50 border border-violet-200 rounded-xl p-5">
                        <h1 class="text-2xl font-bold text-zinc-900 text-center">{{ $selectedCategoryName }}</h1>
                        <p class="text-zinc-600 text-center mt-1">Task yang tersedia dalam kategori ini</p>
                    </div>
                </div>
                <!-- Task Filters (only show when viewing tasks) -->
                <div class="bg-white rounded-xl border border-zinc-200 p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <!-- Search -->
                        <div>
                            <label class="block text-sm font-medium text-zinc-700 mb-2">Cari Task</label>
                            <input type="text" wire:model.live="search" placeholder="Cari task dalam kategori ini..."
                                class="w-full px-3 py-2 border border-zinc-300 rounded-md focus:outline-none focus:ring-2 focus:ring-violet-500" />
                        </div>
                        <!-- Status Filter -->
                        <div>
                            <label class="block text-sm font-medium text-zinc-700 mb-2">Status</label>
                            <select wire:model.live="filter"
                                class="w-full px-3 py-2 border border-zinc-300 rounded-md focus:outline-none focus:ring-2 focus:ring-violet-500">
                                <option value="available">Task Tersedia</option>
                                <option value="my_tasks">Task Saya (Dikerjakan)</option>
                                <option value="completed">Task Selesai</option>
                                <option value="failed">Task Gagal/Dibatalkan</option>
                            </select>
                        </div>
                    </div>
                </div>
                <!-- Tasks Grid (only show when viewing tasks) -->
                @if($viewMode === 'tasks')
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        @forelse($tasks as $task)
                            @php
                                $userTask = $task->userTasks->where('user_id', auth()->id())->first();
                            @endphp
                            <div class="bg-white rounded-xl border border-zinc-200 p-6 space-y-4">
                                <!-- Category Badge and Difficulty -->
                                <div class="flex items-center justify-between">
                                    <span class="px-2 py-1 bg-violet-100 text-blue-800 rounded text-xs font-medium">
                                        {{ $task->category->name }}
                                    </span>
                                    <span class="px-2 py-1 rounded text-xs font-medium
                                                                   @if($task->difficulty_level === 'easy') bg-violet-100 text-purple-800
                                                                   @elseif($task->difficulty_level === 'medium') bg-yellow-100 text-yellow-800
                                                                   @else bg-red-100 text-red-800 @endif">
                                        {{ ucfirst($task->difficulty_level) }}
                                    </span>
                                </div>
                                <!-- Task Title -->
                                <h3 class="text-lg font-semibold text-zinc-900 line-clamp-2">
                                    {{ $task->title }}
                                </h3>
                                <!-- Task Description -->
                                <p class="text-zinc-600 line-clamp-3">
                                    {{ Str::limit(strip_tags($task->description), 100) }}
                                </p>
                                <!-- Admin Info -->
                                <div class="flex items-center gap-2 p-2 bg-zinc-50 rounded-lg">
                                    <div
                                        class="w-8 h-8 bg-gradient-to-br from-blue-500 to-purple-600 rounded-full flex items-center justify-center">
                                        <span
                                            class="text-white text-xs font-bold">{{ substr($task->creator->name ?? 'A', 0, 1) }}</span>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-medium text-zinc-900 truncate">
                                            {{ $task->creator->name ?? 'Admin' }}
                                        </p>
                                        @if($task->creator && $task->creator->badge === 'premium_admin')
                                            <span
                                                class="inline-flex items-center gap-1 px-1.5 py-0.5 bg-amber-100 text-amber-700 rounded text-xs font-medium">
                                                <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                                    <path
                                                        d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z">
                                                    </path>
                                                </svg>
                                                Premium
                                            </span>
                                        @else
                                            <span class="text-xs text-zinc-500">Admin</span>
                                        @endif
                                    </div>
                                </div>
                                <!-- Estimasi Nominal -->
                                @if($task->estimated_amount)
                                    <div class="flex items-center gap-2">
                                        <span class="text-sm text-zinc-500">💰 Estimasi:</span>
                                        <span class="text-base font-bold text-violet-600">
                                            Rp {{ number_format($task->estimated_amount, 0, ',', '.') }}
                                        </span>
                                    </div>
                                @endif
                                <!-- UserTask Status (untuk my_tasks, completed, failed) -->
                                @if($userTask && in_array($filter, ['my_tasks', 'completed', 'failed']))
                                    <div class="border-t border-zinc-200 pt-4 space-y-3">
                                        <!-- Status Badge -->
                                        <div class="flex items-center justify-between">
                                            <span class="text-sm font-medium text-zinc-700">Status:</span>
                                            <span class="px-3 py-1 rounded-full text-xs font-semibold
                                                                                   @if($userTask->isOverdue()) bg-red-100 text-red-800
                                                                                   @elseif($userTask->status === 'taken') bg-yellow-100 text-yellow-800
                                                                                   @elseif($userTask->status === 'pending_verification_1') bg-orange-100 text-orange-800
                                                                                   @elseif($userTask->status === 'pending_verification_2') bg-purple-100 text-purple-800
                                                                                   @elseif($userTask->status === 'completed') bg-violet-100 text-purple-800
                                                                                   @elseif($userTask->status === 'failed') bg-red-100 text-red-800
                                                                                   @elseif($userTask->status === 'cancelled') bg-gray-100 text-gray-800
                                                                                   @else bg-gray-100 text-gray-800 @endif">
                                                {{ $userTask->isOverdue() ? 'Gagal (Kadaluarsa)' : \App\Models\UserTask::STATUSES[$userTask->status] }}
                                            </span>
                                        </div>
                                        <!-- Payment Info (untuk completed) -->
                                        @if($userTask->status === 'completed')
                                            <div class="flex items-center justify-between">
                                                <span class="text-sm font-medium text-zinc-700">Payment:</span>
                                                <div class="text-right">
                                                    <div
                                                        class="px-3 py-1 rounded-full text-xs font-semibold
                                                                                                   @if($userTask->payment_status === 'success') bg-violet-100 text-purple-800
                                                                                                   @elseif($userTask->payment_status === 'failed') bg-red-100 text-red-800
                                                                                                   @else bg-yellow-100 text-yellow-800 @endif">
                                                        {{ \App\Models\UserTask::PAYMENT_STATUSES[$userTask->payment_status] }}
                                                    </div>
                                                    @if($userTask->payment_amount)
                                                        <div class="text-sm font-bold text-violet-600 mt-1">
                                                            Rp {{ number_format($userTask->payment_amount, 0, ',', '.') }}
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                        @endif
                                        <!-- Deadline Info -->
                                        @if($userTask->deadline_at)
                                            <div class="flex items-center justify-between">
                                                <span class="text-sm font-medium text-zinc-700">Deadline:</span>
                                                <span
                                                    class="text-sm {{ $userTask->isOverdue() ? 'text-red-600 font-bold' : 'text-zinc-600' }}">
                                                    {{ $userTask->deadline_at->format('d M Y H:i') }}
                                                    @if($userTask->isOverdue())
                                                        <span class="text-xs">(Overdue)</span>
                                                    @endif
                                                </span>
                                            </div>
                                        @endif
                                        <!-- Failed Count (untuk failed) -->
                                        @if($userTask->status === 'failed' && $userTask->failed_count > 0)
                                            <div class="flex items-center justify-between">
                                                <span class="text-sm font-medium text-zinc-700">Failed Attempts:</span>
                                                <span class="text-sm text-red-600 font-bold">{{ $userTask->failed_count }}x</span>
                                            </div>
                                        @endif
                                    </div>
                                @endif
                                <!-- Task Info (untuk available tasks) -->
                                @if($filter === 'available')
                                    <div class="space-y-2">
                                        <div class="flex items-center text-sm text-zinc-600">
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                            Expired: {{ $task->expired_at->format('d M Y H:i') }}
                                        </div>
                                        <div class="flex items-center text-sm text-zinc-600">
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283-.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z">
                                                </path>
                                            </svg>
                                            <span class="font-semibold">{{ $task->activeUserTask->count() ?? 0 }}</span>
                                            <span class="ml-1">sedang dikerjakan</span>
                                        </div>
                                    </div>
                                @endif
                                <!-- Action Button -->
                                @if($filter === 'available')
                                    @if($task->isTaken())
                                        <button disabled class="w-full px-4 py-2 bg-gray-300 text-gray-500 rounded-md cursor-not-allowed">
                                            Sudah Diambil
                                        </button>
                                    @elseif($task->isExpired())
                                        <button disabled class="w-full px-4 py-2 bg-gray-300 text-gray-500 rounded-md cursor-not-allowed">
                                            Task Expired
                                        </button>
                                    @else
                                        <button wire:click="takeTask({{ $task->id }})" wire:loading.attr="disabled"
                                            wire:loading.class="opacity-50 cursor-wait" wire:target="takeTask({{ $task->id }})"
                                            class="w-full px-4 py-2 bg-violet-600 text-white rounded-md hover:bg-purple-700 transition-colors flex items-center justify-center gap-2">
                                            <svg wire:loading wire:target="takeTask({{ $task->id }})" style="display: none;"
                                                class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4">
                                                </circle>
                                                <path class="opacity-75" fill="currentColor"
                                                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                                </path>
                                            </svg>
                                            <span wire:loading.remove wire:target="takeTask({{ $task->id }})">Ambil Task</span>
                                            <span wire:loading wire:target="takeTask({{ $task->id }})"
                                                style="display: none;">Memproses...</span>
                                        </button>
                                    @endif
                                @elseif($filter === 'my_tasks')
                                    <a href="{{ route('user.task.work', $task->id) }}"
                                        class="block w-full px-4 py-2 bg-violet-600 text-white rounded-md hover:bg-purple-700 transition-colors text-center">
                                        Lanjutkan Pengerjaan
                                    </a>
                                @elseif($filter === 'completed')
                                    <a href="{{ route('user.task.work', $task->id) }}"
                                        class="block w-full px-4 py-2 bg-violet-600 text-white rounded-md hover:bg-purple-700 transition-colors text-center">
                                        Lihat Status & Detail
                                    </a>
                                @elseif($filter === 'failed')
                                    <div class="space-y-2">
                                        @if($userTask->status === 'failed')
                                            <a href="{{ route('user.task.work', $task->id) }}"
                                                class="block w-full px-4 py-2 bg-orange-600 text-white rounded-md hover:bg-orange-700 transition-colors text-center">
                                                Lihat Detail Kegagalan
                                            </a>
                                        @elseif($userTask->status === 'cancelled')
                                            <div class="text-gray-600 font-medium text-center py-2">
                                                Task Dibatalkan
                                            </div>
                                        @endif
                                    </div>
                                @endif
                            </div>
                        @empty
                            <div class="col-span-full">
                                <div class="bg-white rounded-xl border border-zinc-200 text-center py-12">
                                    <svg class="w-16 h-16 text-zinc-400 mx-auto mb-4" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1"
                                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                                        </path>
                                    </svg>
                                    <h3 class="text-lg font-semibold text-zinc-900 mb-2">Tidak ada task ditemukan</h3>
                                    <p class="text-zinc-600">Coba ubah filter atau kata kunci pencarian</p>
                                </div>
                            </div>
                        @endforelse
                    </div>
                    <!-- Pagination -->
                    @if($tasks->hasPages())
                        <div class="flex justify-center">
                            {{ $tasks->links() }}
                        </div>
                    @endif
                @endif
                <!-- Flash Messages -->
                @if (session()->has('success'))
                    <div class="fixed bottom-4 right-4 z-50">
                        <div class="bg-violet-500 text-white px-6 py-3 rounded-md shadow-lg">
                            {{ session('success') }}
                        </div>
                    </div>
                @endif
                @if (session()->has('error'))
                    <div class="fixed bottom-4 right-4 z-50">
                        <div class="bg-red-500 text-white px-6 py-3 rounded-md shadow-lg">
                            {{ session('error') }}
                        </div>
                    </div>
                @endif
            @endif
        </div>
    </div>

    <!-- Weekly Task Warning Modal -->
    @if($showWeeklyWarningModal)
        <!-- Combined Overlay & Modal Container -->
        <div class="fixed inset-0 z-50 flex items-center justify-center p-3 sm:p-4 bg-black/80 backdrop-blur-sm"
            wire:click.self="cancelTakeTask">
            <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md overflow-hidden animate-in zoom-in-95 duration-200">
                <!-- Header with Warning Icon -->
                <div class="bg-gradient-to-r from-amber-500 to-orange-500 px-4 py-4 sm:px-6 sm:py-5">
                    <div class="flex items-center gap-3">
                        <div
                            class="flex-shrink-0 flex items-center justify-center h-10 w-10 sm:h-12 sm:w-12 rounded-full bg-white/20 backdrop-blur">
                            <svg class="h-6 w-6 sm:h-7 sm:w-7 text-white" fill="none" viewBox="0 0 24 24" stroke-width="2"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-lg sm:text-xl font-bold text-white">
                                Peringatan Spam WhatsApp
                            </h3>
                            <p class="text-amber-100 text-xs sm:text-sm">Lindungi akun WhatsApp Anda</p>
                        </div>
                    </div>
                </div>

                <!-- Content -->
                <div class="px-4 py-4 sm:px-6 sm:py-5 space-y-4">
                    <!-- Last Task Info -->
                    <div class="bg-amber-50 border border-amber-200 rounded-xl p-3 sm:p-4">
                        <div class="flex items-start gap-3">
                            <div class="flex-shrink-0 mt-0.5">
                                <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <div>
                                <p class="text-xs sm:text-sm text-amber-700 font-medium">Task terakhir diambil:</p>
                                <p class="text-base sm:text-lg font-bold text-amber-800">{{ $lastTaskDate }}</p>
                                <p class="text-xs text-amber-600 mt-0.5">{{ $daysSinceLastTask }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Warning Message -->
                    <div class="space-y-3">
                        <p class="text-sm text-zinc-600">
                            Untuk menghindari akun WhatsApp dianggap <span class="font-bold text-red-600">SPAM</span>:
                        </p>

                        <div class="bg-zinc-50 rounded-xl p-3 sm:p-4">
                            <ul class="text-sm text-zinc-700 space-y-2">
                                <li class="flex items-start gap-2">
                                    <span
                                        class="flex-shrink-0 w-5 h-5 rounded-full bg-blue-100 flex items-center justify-center text-blue-600 text-xs font-bold">1</span>
                                    <span>Maksimal <strong>1 task per minggu</strong> per akun WA</span>
                                </li>
                                <li class="flex items-start gap-2">
                                    <span
                                        class="flex-shrink-0 w-5 h-5 rounded-full bg-blue-100 flex items-center justify-center text-blue-600 text-xs font-bold">2</span>
                                    <span>Tunggu minimal <strong>7 hari</strong> sebelum task baru</span>
                                </li>
                            </ul>
                        </div>
                    </div>

                    <!-- Tip -->
                    <div class="bg-violet-50 border border-violet-200 rounded-xl p-3 sm:p-4">
                        <div class="flex items-start gap-2">
                            <span class="text-lg">💡</span>
                            <p class="text-xs sm:text-sm text-purple-700">
                                <strong>Tips:</strong> Gunakan <strong>akun WhatsApp berbeda</strong> jika ingin melanjutkan
                                sekarang.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Buttons -->
                <div class="bg-zinc-100 px-4 py-4 sm:px-6 flex flex-col gap-2 sm:gap-3">
                    <button wire:click="confirmTakeTask" wire:loading.attr="disabled"
                        wire:loading.class="opacity-50 cursor-wait" wire:target="confirmTakeTask" type="button"
                        class="w-full inline-flex justify-center items-center gap-2 rounded-xl px-4 py-3 bg-violet-600 text-white font-semibold hover:bg-purple-700 active:bg-purple-800 transition-colors text-sm sm:text-base">
                        <!-- Loading spinner -->
                        <svg wire:loading wire:target="confirmTakeTask" style="display: none;" class="w-5 h-5 animate-spin"
                            fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4">
                            </circle>
                            <path class="opacity-75" fill="currentColor"
                                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                            </path>
                        </svg>
                        <!-- Check icon -->
                        <svg wire:loading.remove wire:target="confirmTakeTask" class="w-5 h-5" fill="none"
                            stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        <span wire:loading.remove wire:target="confirmTakeTask">Ya, Saya Pakai Akun WA Baru</span>
                        <span wire:loading wire:target="confirmTakeTask" style="display: none;">Memproses...</span>
                    </button>
                    <button wire:click="cancelTakeTask" wire:loading.attr="disabled"
                        wire:loading.class="opacity-50 cursor-wait" wire:target="cancelTakeTask" type="button"
                        class="w-full inline-flex justify-center items-center gap-2 rounded-xl px-4 py-3 bg-white border-2 border-zinc-200 text-zinc-700 font-semibold hover:bg-zinc-50 active:bg-zinc-100 transition-colors text-sm sm:text-base">
                        <!-- Loading spinner -->
                        <svg wire:loading wire:target="cancelTakeTask" style="display: none;" class="w-5 h-5 animate-spin"
                            fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4">
                            </circle>
                            <path class="opacity-75" fill="currentColor"
                                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                            </path>
                        </svg>
                        <!-- X icon -->
                        <svg wire:loading.remove wire:target="cancelTakeTask" class="w-5 h-5" fill="none"
                            stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12">
                            </path>
                        </svg>
                        <span wire:loading.remove wire:target="cancelTakeTask">Batal, Tunggu Minggu Depan</span>
                        <span wire:loading wire:target="cancelTakeTask" style="display: none;">Menutup...</span>
                    </button>
                </div>
            </div>
        </div>
    @endif

    <style>
        .line-clamp-2 {
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .line-clamp-3 {
            display: -webkit-box;
            -webkit-line-clamp: 3;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
    </style>
</div>