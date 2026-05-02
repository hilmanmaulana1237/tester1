@if($userTask->status === \App\Models\UserTask::STATUS_COMPLETED)
<!-- Completed Task Details Modal -->
<template x-teleport="body">
<div 
  x-data="{ show: false }"
  x-on:open-modal.window="if ($event.detail.name === 'completed-task-{{ $userTask->id }}') show = true"
  x-on:close-modal.window="if ($event.detail.name === 'completed-task-{{ $userTask->id }}') show = false"
  x-on:keydown.escape.window="show = false"
  x-show="show"
  x-cloak
  class="fixed inset-0 z-50"
  style="display: none;">
  <!-- Backdrop -->
  <div x-show="show" 
       @click="show = false"
       class="fixed inset-0 bg-black/60"></div>
  
  <!-- Modal Content -->
  <div class="flex min-h-screen items-center justify-center p-4">
    <div x-show="show"
         x-transition:enter="ease-out duration-200"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100"
         x-transition:leave="ease-in duration-150"
         x-transition:leave-start="opacity-100 scale-100"
         x-transition:leave-end="opacity-0 scale-95"
         @click.stop
         class="relative bg-white rounded-xl shadow-2xl max-w-2xl w-full max-h-[90vh] overflow-hidden will-change-transform">
      
      <!-- Header -->
      <div class="bg-gradient-to-br from-green-50 via-emerald-50 to-teal-50 px-6 py-4 border-b border-green-200">
        <div class="flex items-center justify-between">
          <div class="flex items-center gap-3">
            <div class="w-12 h-12 bg-gradient-to-br from-green-400 to-emerald-500 rounded-full flex items-center justify-center shadow-lg">
              <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
              </svg>
            </div>
            <div>
              <h3 class="text-lg font-bold text-green-900 flex items-center gap-2">
                ✨ Task Completed
              </h3>
              <p class="text-sm text-green-700">{{ $userTask->task->title }}</p>
            </div>
          </div>
          <button @click="show = false" class="text-green-400 hover:text-green-600 transition-colors">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
          </button>
        </div>
      </div>
      
      <!-- Body -->
      <div class="px-6 py-4 max-h-[60vh] overflow-y-auto overscroll-contain" style="transform: translateZ(0); -webkit-overflow-scrolling: touch;">
        <!-- Success Message -->
        <div class="mb-5">
          <div class="bg-gradient-to-r from-green-50 to-emerald-50 border-l-4 border-green-500 rounded-lg p-4 shadow-sm">
            <div class="flex items-start gap-3">
              <div class="flex-shrink-0">
                <div class="w-10 h-10 bg-gradient-to-br from-green-500 to-emerald-600 rounded-full flex items-center justify-center shadow-lg">
                  <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                  </svg>
                </div>
              </div>
              <div class="flex-1">
                <h4 class="font-bold text-green-900 mb-1 text-base">Congratulations! 🎉</h4>
                <p class="text-green-800 text-sm leading-relaxed">You have successfully completed this task. Great job!</p>
              </div>
            </div>
          </div>
        </div>

        <!-- Payment Information (if exists) -->
        @if($userTask->payment_amount || $userTask->task->payment_amount)
        <div class="mb-5">
          <div class="bg-gradient-to-br from-emerald-50 via-green-50 to-teal-50 border border-emerald-200 rounded-xl p-5 shadow-sm">
            <div class="flex items-center justify-between mb-4">
              <h4 class="font-bold text-zinc-900 flex items-center gap-2 text-base">
                <div class="w-8 h-8 bg-gradient-to-br from-green-500 to-emerald-600 rounded-lg flex items-center justify-center shadow-md">
                  <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                  </svg>
                </div>
                Payment Information
              </h4>
            </div>
            
            <!-- Payment Amount -->
            <div class="bg-white rounded-lg p-4 mb-3 border border-emerald-200">
              <div class="flex justify-between items-center mb-2">
                <span class="text-sm text-zinc-600 font-medium">Task Reward</span>
                <div class="flex items-baseline gap-1">
                  <span class="text-xs text-zinc-500">Rp</span>
                  <span class="text-2xl font-bold bg-gradient-to-r from-green-600 to-emerald-600 bg-clip-text text-transparent">
                    {{ number_format($userTask->payment_amount ?? $userTask->task->payment_amount, 0, ',', '.') }}
                  </span>
                </div>
              </div>
              <div class="h-1 bg-gradient-to-r from-green-200 to-emerald-200 rounded-full"></div>
            </div>

            <!-- Payment Status -->
            <div class="bg-white rounded-lg p-4 border border-emerald-200">
              <div class="flex justify-between items-center">
                <span class="text-sm text-zinc-600 font-medium">Payment Status</span>
                @if($userTask->payment_status === 'success')
                  <div class="flex items-center gap-2">
                    <div class="flex items-center gap-1.5 px-3 py-1.5 bg-gradient-to-r from-green-500 to-emerald-600 text-white rounded-lg text-sm font-bold shadow-md">
                      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                      </svg>
                      <span>Paid</span>
                    </div>
                  </div>
                @elseif($userTask->payment_status === 'failed')
                  <div class="flex items-center gap-2">
                    <div class="flex items-center gap-1.5 px-3 py-1.5 bg-gradient-to-r from-red-500 to-rose-600 text-white rounded-lg text-sm font-bold shadow-md">
                      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                      </svg>
                      <span>Failed</span>
                    </div>
                  </div>
                @else
                  <div class="flex items-center gap-2">
                    <div class="flex items-center gap-1.5 px-3 py-1.5 bg-gradient-to-r from-orange-500 to-amber-600 text-white rounded-lg text-sm font-bold shadow-md">
                      <svg class="w-4 h-4 animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                      </svg>
                      <span>Pending</span>
                    </div>
                  </div>
                @endif
              </div>
              @if($userTask->payment_status !== 'success')
              <div class="mt-2 pt-2 border-t border-emerald-200">
                <p class="text-xs text-orange-600 flex items-start gap-1.5">
                  <svg class="w-3.5 h-3.5 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                  </svg>
                  <span>
                    @if($userTask->payment_status === 'failed')
                      Payment failed. Please contact admin for assistance.
                    @else
                      Your payment is being processed and will be transferred soon.
                    @endif
                  </span>
                </p>
              </div>
              @else
              <div class="mt-2 pt-2 border-t border-emerald-200">
                <p class="text-xs text-green-600 flex items-start gap-1.5">
                  <svg class="w-3.5 h-3.5 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                  </svg>
                  <span>
                    Payment verified
                    @if($userTask->payment_verified_at)
                      on {{ $userTask->payment_verified_at->format('M d, Y H:i') }}
                    @endif
                  </span>
                </p>
              </div>
              @endif
            </div>
          </div>
        </div>
        @endif
        
        <!-- Task Summary -->
        <div class="bg-gradient-to-br from-zinc-50 to-slate-50 border border-zinc-200 rounded-xl p-5 mb-5 shadow-sm">
          <h4 class="font-bold text-zinc-900 mb-4 flex items-center gap-2 text-base">
            <div class="w-8 h-8 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-lg flex items-center justify-center shadow-md">
              <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
              </svg>
            </div>
            Task Details
          </h4>
          <div class="space-y-3 text-sm">
            <div class="flex justify-between items-start py-2.5 border-b border-zinc-200">
              <span class="text-zinc-600 font-medium">Task Name</span>
              <span class="font-semibold text-zinc-900 text-right ml-4 max-w-xs">{{ $userTask->task->title }}</span>
            </div>
            <div class="flex justify-between items-center py-2.5 border-b border-zinc-200">
              <span class="text-zinc-600 font-medium">Category</span>
              <span class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-gradient-to-r from-purple-100 to-pink-100 text-purple-700 rounded-lg text-xs font-bold shadow-sm">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                </svg>
                {{ $userTask->task->category->name ?? 'N/A' }}
              </span>
            </div>
            <div class="flex justify-between items-center py-2.5">
              <span class="text-zinc-600 font-medium">Task Status</span>
              <span class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-gradient-to-r from-green-500 to-emerald-600 text-white rounded-lg text-xs font-bold shadow-md">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
                Completed
              </span>
            </div>
          </div>
        </div>

        <!-- Timeline -->
        <div class="bg-gradient-to-br from-blue-50 to-indigo-50 border border-blue-200 rounded-xl p-5 shadow-sm">
          <h4 class="font-bold text-zinc-900 mb-4 flex items-center gap-2 text-base">
            <div class="w-8 h-8 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-lg flex items-center justify-center shadow-md">
              <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
              </svg>
            </div>
            Timeline
          </h4>
          <div class="space-y-3 text-sm">
            @if($userTask->taken_at)
            <div class="flex items-start gap-3 bg-white rounded-lg p-3 border border-blue-200">
              <div class="flex-shrink-0 w-10 h-10 bg-gradient-to-br from-blue-500 to-blue-600 rounded-lg flex items-center justify-center shadow-md">
                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
              </div>
              <div class="flex-1 pt-1">
                <p class="font-bold text-zinc-900 mb-0.5">Task Started</p>
                <p class="text-xs text-zinc-600">{{ $userTask->taken_at->format('l, M d, Y') }}</p>
                <p class="text-xs text-blue-600 font-semibold">{{ $userTask->taken_at->format('H:i') }} WIB</p>
              </div>
            </div>
            @endif
            @if($userTask->completed_at)
            <div class="flex items-start gap-3 bg-white rounded-lg p-3 border border-green-200">
              <div class="flex-shrink-0 w-10 h-10 bg-gradient-to-br from-green-500 to-emerald-600 rounded-lg flex items-center justify-center shadow-md">
                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
              </div>
              <div class="flex-1 pt-1">
                <p class="font-bold text-zinc-900 mb-0.5">Task Completed</p>
                <p class="text-xs text-zinc-600">{{ $userTask->completed_at->format('l, M d, Y') }}</p>
                <p class="text-xs text-green-600 font-semibold">{{ $userTask->completed_at->format('H:i') }} WIB</p>
              </div>
            </div>
            @endif
            @if($userTask->taken_at && $userTask->completed_at)
            <div class="bg-gradient-to-r from-indigo-50 to-blue-50 rounded-lg p-3 border border-indigo-200">
              <div class="flex items-center justify-between">
                <div class="flex items-center gap-2">
                  <div class="w-8 h-8 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-lg flex items-center justify-center shadow-md">
                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                    </svg>
                  </div>
                  <span class="text-xs text-zinc-600 font-medium">Completion Time</span>
                </div>
                <span class="font-bold text-indigo-600">{{ $userTask->taken_at->diffForHumans($userTask->completed_at, true) }}</span>
              </div>
            </div>
            @endif
          </div>
        </div>
      </div>
      
      <!-- Footer -->
      <div class="bg-gradient-to-r from-green-50 via-emerald-50 to-teal-50 px-6 py-4 border-t border-green-200">
        <button @click="show = false" class="w-full px-4 py-3 bg-gradient-to-r from-green-600 via-emerald-600 to-green-600 hover:from-green-700 hover:via-emerald-700 hover:to-green-700 text-white rounded-xl font-bold transition-all shadow-lg hover:shadow-xl hover:-translate-y-0.5 flex items-center justify-center gap-2">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
          </svg>
          Got it!
        </button>
      </div>
    </div>
  </div>
</div>
</template>
@endif

@if($userTask->status === \App\Models\UserTask::STATUS_CANCELLED)
<!-- Cancelled Task Details Modal -->
<template x-teleport="body">
<div 
  x-data="{ show: false }"
  x-on:open-modal.window="if ($event.detail.name === 'cancelled-task-{{ $userTask->id }}') show = true"
  x-on:close-modal.window="if ($event.detail.name === 'cancelled-task-{{ $userTask->id }}') show = false"
  x-on:keydown.escape.window="show = false"
  x-show="show"
  x-cloak
  class="fixed inset-0 z-50 overflow-y-auto"
  style="display: none;">
  <!-- Backdrop -->
  <div x-show="show" 
       x-transition:enter="transition ease-out duration-300"
       x-transition:enter-start="opacity-0"
       x-transition:enter-end="opacity-100"
       x-transition:leave="transition ease-in duration-200"
       x-transition:leave-start="opacity-100"
       x-transition:leave-end="opacity-0"
       @click="show = false"
       class="fixed inset-0 bg-black/60"></div>
  
  <!-- Modal Content -->
  <div class="flex min-h-screen items-center justify-center p-4">
    <div x-show="show"
         x-transition:enter="ease-out duration-200"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100"
         x-transition:leave="ease-in duration-150"
         x-transition:leave-start="opacity-100 scale-100"
         x-transition:leave-end="opacity-0 scale-95"
         @click.stop
         class="relative bg-white rounded-xl shadow-2xl max-w-2xl w-full max-h-[90vh] overflow-hidden will-change-transform">
      
      <!-- Header -->
      <div class="bg-gradient-to-br from-gray-50 via-zinc-50 to-slate-50 px-6 py-4 border-b border-gray-200">
        <div class="flex items-center justify-between">
          <div class="flex items-center gap-3">
            <div class="w-12 h-12 bg-gradient-to-br from-gray-400 to-zinc-500 rounded-full flex items-center justify-center shadow-lg">
              <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 0 0 5.636 5.636m12.728 12.728A9 9 0 0 1 5.636 5.636m12.728 12.728L5.636 5.636"></path>
              </svg>
            </div>
            <div>
              <h3 class="text-lg font-bold text-gray-900 flex items-center gap-2">
                🚫 Task Cancelled
              </h3>
              <p class="text-sm text-gray-700">{{ $userTask->task->title }}</p>
            </div>
          </div>
          <button @click="show = false" class="text-gray-400 hover:text-gray-600 transition-colors">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
          </button>
        </div>
      </div>
      
      <!-- Body -->
      <div class="px-6 py-4 max-h-[60vh] overflow-y-auto overscroll-contain" style="transform: translateZ(0); -webkit-overflow-scrolling: touch;">
        <!-- Info Message -->
        <div class="mb-4">
          <div class="bg-gradient-to-r from-gray-50 to-zinc-50 border border-gray-200 rounded-lg p-4">
            <div class="flex items-start gap-3">
              <div class="flex-shrink-0">
                <div class="w-10 h-10 bg-gray-100 rounded-full flex items-center justify-center">
                  <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                  </svg>
                </div>
              </div>
              <div>
                <h4 class="font-semibold text-gray-900 mb-1">Task Cancelled</h4>
                <p class="text-gray-700 text-sm">This task has been cancelled and is no longer active.</p>
              </div>
            </div>
          </div>
        </div>
        
        <!-- Task Summary -->
        <div class="bg-zinc-50 border border-zinc-200 rounded-lg p-4 mb-4">
          <h4 class="font-semibold text-zinc-900 mb-3 flex items-center gap-2">
            <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
            </svg>
            Task Summary
          </h4>
          <div class="space-y-2.5 text-sm">
            <div class="flex justify-between items-start">
              <span class="text-zinc-600">Task:</span>
              <span class="font-medium text-zinc-900 text-right ml-4">{{ $userTask->task->title }}</span>
            </div>
            <div class="flex justify-between">
              <span class="text-zinc-600">Category:</span>
              <span class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-purple-100 text-purple-700 rounded-lg text-xs font-medium">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                </svg>
                {{ $userTask->task->category->name ?? 'N/A' }}
              </span>
            </div>
            <div class="flex justify-between">
              <span class="text-zinc-600">Status:</span>
              <span class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-gray-100 text-gray-700 rounded-lg text-xs font-medium">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 0 0 5.636 5.636m12.728 12.728A9 9 0 0 1 5.636 5.636m12.728 12.728L5.636 5.636"></path>
                </svg>
                Cancelled
              </span>
            </div>
          </div>
        </div>

        <!-- Timeline -->
        <div class="bg-gradient-to-br from-orange-50/50 to-amber-50/50 border border-orange-200 rounded-lg p-4">
          <h4 class="font-semibold text-zinc-900 mb-3 flex items-center gap-2">
            <svg class="w-5 h-5 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            Timeline
          </h4>
          <div class="space-y-3 text-sm">
            @if($userTask->taken_at)
            <div class="flex items-center gap-3">
              <div class="flex-shrink-0 w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
                <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
              </div>
              <div class="flex-1">
                <p class="font-medium text-zinc-900">Task Started</p>
                <p class="text-xs text-zinc-600">{{ $userTask->taken_at->format('M d, Y H:i') }}</p>
              </div>
            </div>
            @endif
            @if($userTask->cancelled_at)
            <div class="flex items-center gap-3">
              <div class="flex-shrink-0 w-8 h-8 bg-gray-100 rounded-full flex items-center justify-center">
                <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
              </div>
              <div class="flex-1">
                <p class="font-medium text-zinc-900">Task Cancelled</p>
                <p class="text-xs text-zinc-600">{{ $userTask->cancelled_at->format('M d, Y H:i') }}</p>
              </div>
            </div>
            @endif
            @if($userTask->taken_at && $userTask->cancelled_at)
            <div class="flex items-center gap-3 pl-11 pt-2 border-t border-orange-200">
              <div class="text-xs">
                <span class="text-zinc-600">Duration before cancellation: </span>
                <span class="font-semibold text-orange-600">{{ $userTask->taken_at->diffForHumans($userTask->cancelled_at, true) }}</span>
              </div>
            </div>
            @endif
          </div>
        </div>
      </div>
      
      <!-- Footer -->
      <div class="bg-gradient-to-r from-gray-50 to-zinc-50 px-6 py-4 border-t border-gray-200">
        <button @click="show = false" class="w-full px-4 py-2 bg-gradient-to-r from-gray-600 to-zinc-600 hover:from-gray-700 hover:to-zinc-700 text-white rounded-lg font-medium transition-all shadow-lg hover:shadow-xl">
          Close
        </button>
      </div>
    </div>
  </div>
</div>
</template>
@endif

@if($userTask->status === \App\Models\UserTask::STATUS_FAILED)
<!-- Failed Task Details Modal -->
<template x-teleport="body">
<div 
  x-data="{ show: false }"
  x-on:open-modal.window="if ($event.detail.name === 'failed-task-{{ $userTask->id }}') show = true"
  x-on:close-modal.window="if ($event.detail.name === 'failed-task-{{ $userTask->id }}') show = false"
  x-on:keydown.escape.window="show = false"
  x-show="show"
  x-cloak
  class="fixed inset-0 z-50 overflow-y-auto"
  style="display: none;">
  <!-- Backdrop -->
  <div x-show="show" 
       x-transition:enter="transition ease-out duration-300"
       x-transition:enter-start="opacity-0"
       x-transition:enter-end="opacity-100"
       x-transition:leave="transition ease-in duration-200"
       x-transition:leave-start="opacity-100"
       x-transition:leave-end="opacity-0"
       @click="show = false"
       class="fixed inset-0 bg-black/60"></div>
  
  <!-- Modal Content -->
  <div class="flex min-h-screen items-center justify-center p-4">
    <div x-show="show"
         x-transition:enter="ease-out duration-200"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100"
         x-transition:leave="ease-in duration-150"
         x-transition:leave-start="opacity-100 scale-100"
         x-transition:leave-end="opacity-0 scale-95"
         @click.stop
         class="relative bg-white rounded-xl shadow-2xl max-w-2xl w-full max-h-[90vh] overflow-hidden will-change-transform">
      
      <!-- Header -->
      <div class="bg-red-50 px-6 py-4 border-b border-red-200">
        <div class="flex items-center justify-between">
          <div class="flex items-center gap-3">
            <div class="w-10 h-10 bg-red-100 rounded-full flex items-center justify-center">
              @php
                $feedback = $userTask->verification_1_status;
                $isTimeout = strpos($feedback, 'deadline') !== false || strpos($feedback, 'Failed:') !== false;
              @endphp
              @if($isTimeout)
                <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
              @else
                <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
              @endif
            </div>
            <div>
              <h3 class="text-lg font-bold text-red-900">
                @if($isTimeout) ⏰ Task Timeout @else ❌ Task Failed @endif
              </h3>
              <p class="text-sm text-red-700">{{ $userTask->task->title }}</p>
            </div>
          </div>
          <button @click="show = false" class="text-red-400 hover:text-red-600">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
          </button>
        </div>
      </div>
      
      <!-- Body -->
      <div class="px-6 py-4 max-h-[60vh] overflow-y-auto overscroll-contain" style="transform: translateZ(0); -webkit-overflow-scrolling: touch;">
        <!-- Reason -->
        <div class="mb-4">
          <h4 class="font-semibold text-zinc-900 mb-2 flex items-center gap-2">
            <svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            @if($isTimeout) Reason: @else Admin Feedback: @endif
          </h4>
          <div class="bg-red-50 border border-red-200 rounded-lg p-4">
            <p class="text-red-800 text-sm">
              @if($userTask->verification_2_status && strpos($userTask->verification_2_status, 'Rejected') !== false)
                @php
                  preg_match('/Rejected by admin at .+?\. (.+)$/', $userTask->verification_2_status, $matches);
                  echo $matches[1] ?? $userTask->verification_2_status;
                @endphp
              @elseif($userTask->verification_1_status)
                @php
                  if (strpos($userTask->verification_1_status, 'Rejected by admin') !== false) {
                    preg_match('/Rejected by admin at .+?\. (.+)$/', $userTask->verification_1_status, $matches);
                    echo $matches[1] ?? $userTask->verification_1_status;
                  } else {
                    echo $userTask->verification_1_status;
                  }
                @endphp
              @else
                Task gagal. Silakan hubungi admin untuk informasi lebih lanjut.
              @endif
            </p>
          </div>
        </div>
        
        <!-- Task Summary -->
        <div class="bg-zinc-50 border border-zinc-200 rounded-lg p-4">
          <h4 class="font-semibold text-zinc-900 mb-3">Task Summary</h4>
          <div class="space-y-2 text-sm">
            <div class="flex justify-between">
              <span class="text-zinc-600">Task:</span>
              <span class="font-medium text-zinc-900 text-right ml-4">{{ $userTask->task->title }}</span>
            </div>
            <div class="flex justify-between">
              <span class="text-zinc-600">Category:</span>
              <span class="font-medium text-zinc-900">{{ $userTask->task->category->name ?? 'N/A' }}</span>
            </div>
            <div class="flex justify-between">
              <span class="text-zinc-600">Status:</span>
              <span class="px-2 py-0.5 bg-red-100 text-red-700 rounded text-xs font-medium">Failed</span>
            </div>
            @if($userTask->taken_at)
              <div class="flex justify-between">
                <span class="text-zinc-600">Taken At:</span>
                <span class="font-medium text-zinc-900">{{ $userTask->taken_at->format('M d, Y H:i') }}</span>
              </div>
            @endif
            @if($userTask->cancelled_at)
              <div class="flex justify-between">
                <span class="text-zinc-600">Failed At:</span>
                <span class="font-medium text-zinc-900">{{ $userTask->cancelled_at->format('M d, Y H:i') }}</span>
              </div>
            @endif
          </div>
        </div>
      </div>
      
      <!-- Footer -->
      <div class="bg-zinc-50 px-6 py-4 border-t border-zinc-200">
        <button @click="show = false" class="w-full px-4 py-2 bg-zinc-600 hover:bg-zinc-700 text-white rounded-lg font-medium transition-colors">
          Close
        </button>
      </div>
    </div>
  </div>
</div>
</template>
@endif
