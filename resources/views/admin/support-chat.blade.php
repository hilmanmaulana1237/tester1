<div class="h-screen flex flex-col bg-gray-50 support-chat-root" 
     x-data="{
         activeThreadId: @entangle('activeThreadId'),
         messageCount: @entangle('messageCount'),
         isLoadingThread: false,
         pollInterval: null,
         optimisticMessages: [],
         cachedNodes: {},
         categoryFilter: @entangle('categoryFilter'),
         statusFilter: @entangle('statusFilter'),
         isListLoading: false,
         cachedLists: {},
         pendingThreadTimeout: null,
         pendingListTimeout: null,
         isPrivateNote: false,
         userIsTyping: false,
         typingTimeout: null,
         echoChannel: null,
         showSlashMenu: false,
         slashSearch: '',
         slashIndex: 0,
         slashCommands: [
             { label: '✅ Disetujui', text: 'Selamat! Bukti Anda sudah disetujui. Pembayaran akan segera diproses.' },
             { label: '🔄 Submit Ulang', text: 'Mohon untuk submit ulang bukti yang benar.' },
             { label: '⚠️ Kurang Lengkap', text: 'Bukti yang dikirim kurang lengkap, mohon lengkapi terlebih dahulu sebelum submit.' },
             { label: '❌ Format Salah', text: 'Format bukti tidak sesuai ketentuan, silakan perbaiki dan kirim ulang.' },
             { label: '👍 Sudah Bagus', text: 'Bukti sudah bagus, sedang dalam proses review. Mohon ditunggu ya.' },
             { label: '⏳ Mohon Tunggu', text: 'Mohon tunggu, bukti Anda sedang direview. Terima kasih atas kesabarannya.' },
             { label: '📋 Info Task', text: 'Silakan baca panduan task dengan teliti sebelum melakukan submit.' },
             { label: '💰 Pembayaran', text: 'Pembayaran sedang diproses, mohon tunggu 1x24 jam kerja.' },
         ],
         filteredSlash() {
             if (!this.slashSearch) return this.slashCommands;
             return this.slashCommands.filter(c => c.label.toLowerCase().includes(this.slashSearch.toLowerCase()));
         },
         applySlash(cmd) {
             const el = document.getElementById('admin-chat-input');
             if (el) { el.value = cmd.text; this.$wire.set('newMessage', cmd.text); el.style.height = 'auto'; el.style.height = Math.min(el.scrollHeight, 100) + 'px'; el.focus(); }
             this.showSlashMenu = false;
             this.slashSearch = '';
             this.slashIndex = 0;
         },
         handleSlashInput(e) {
             const val = e.target.value;
             if (val === '/') { this.showSlashMenu = true; this.slashSearch = ''; this.slashIndex = 0; return; }
             if (val.startsWith('/') && !val.includes(' ')) { this.showSlashMenu = true; this.slashSearch = val.slice(1); this.slashIndex = 0; return; }
             this.showSlashMenu = false;
             this.sendTypingWhisper({{ $activeThread->task_id ?? 'null' }});
         },
         handleSlashKey(e) {
             if (!this.showSlashMenu) return;
             if (e.key === 'ArrowDown') { e.preventDefault(); this.slashIndex = Math.min(this.slashIndex + 1, this.filteredSlash().length - 1); }
             else if (e.key === 'ArrowUp') { e.preventDefault(); this.slashIndex = Math.max(this.slashIndex - 1, 0); }
             else if (e.key === 'Enter') { e.preventDefault(); const cmd = this.filteredSlash()[this.slashIndex]; if (cmd) this.applySlash(cmd); }
             else if (e.key === 'Escape') { this.showSlashMenu = false; }
         },
         switchList(type, val) {
             const oldKey = this.categoryFilter + '-' + this.statusFilter;
             
             const listSlot = document.getElementById('thread-list-slot');
             const listCache = document.getElementById('list-cache-container');
             
             if (listSlot && listSlot.firstElementChild) {
                 this.cachedLists[oldKey] = listSlot.firstElementChild;
                 listCache.appendChild(listSlot.firstElementChild);
             }
             
             if (type === 'cat') this.categoryFilter = val;
             if (type === 'status') this.statusFilter = val;
             
             const newKey = this.categoryFilter + '-' + this.statusFilter;
             
             if (this.cachedLists[newKey] && listSlot) {
                 listSlot.appendChild(this.cachedLists[newKey]);
                 this.isListLoading = false;
             } else {
                 this.isListLoading = true;
             }
             
             if (this.pendingListTimeout) clearTimeout(this.pendingListTimeout);
             
             this.pendingListTimeout = setTimeout(() => {
                 if (type === 'cat') {
                     this.$wire.set('categoryFilter', val).then(() => this.isListLoading = false);
                 } else {
                     this.$wire.set('statusFilter', val).then(() => this.isListLoading = false);
                 }
             }, 100);
         },
         async submitMessage() {
             const textarea = document.getElementById('admin-chat-input');
             if (!textarea) return;
             const text = textarea.value.trim();
             if (!text) return;
             
             const tempId = Date.now();
             this.optimisticMessages.push({ id: tempId, text: text });
             
             // Clear visually without wiping the Livewire state payload
             textarea.value = '';
             textarea.style.height = '40px';
             
             setTimeout(() => {
                 const container = document.getElementById('chat-messages-container');
                 if (container) container.scrollTop = container.scrollHeight;
             }, 10);
             
             await this.$wire.sendMessage(text);
             
             this.optimisticMessages = this.optimisticMessages.filter(m => m.id !== tempId);
         },
         startPolling() {
             if (this.pollInterval) clearInterval(this.pollInterval);
             this.pollInterval = setInterval(() => this.checkCount(), 3000);
         },
         async checkCount() {
             if (!this.activeThreadId) return;
             try {
                 const response = await fetch('/api/support-thread/' + this.activeThreadId + '/count');
                 if (response.ok) {
                     const data = await response.json();
                     if (data.count !== this.messageCount) {
                         $wire.checkForNewMessages(data.count);
                     }
                 }
             } catch (e) {}
         },
         joinEchoChannel(threadId, taskId) {
             if (!taskId || typeof window.Echo === 'undefined') return;
             if (this.echoChannel) this.echoChannel.stopListeningForWhisper('typing');
             this.echoChannel = window.Echo.private('chat.' + taskId);
             this.echoChannel.listenForWhisper('typing', () => {
                 this.userIsTyping = true;
                 if (this.typingTimeout) clearTimeout(this.typingTimeout);
                 this.typingTimeout = setTimeout(() => { this.userIsTyping = false; }, 2500);
             });
         },
         sendTypingWhisper(taskId) {
             if (!taskId || !this.echoChannel || typeof window.Echo === 'undefined') return;
             this.echoChannel.whisper('typing', { name: 'Admin' });
         }
     }"
     x-init="
         $watch('activeThreadId', (val, oldVal) => {
             if (val) {
                 checkCount();
                 startPolling();
             } else {
                 if (pollInterval) clearInterval(pollInterval);
             }
         });
         if (activeThreadId) startPolling();
     "
>

  {{-- ===== TOP STATS BAR ===== --}}
  <div class="flex-shrink-0 bg-white border-b border-gray-200">
    <div class="flex items-center justify-between px-4 py-2.5">
      <div class="flex items-center gap-3">
        <div class="w-8 h-8 rounded-lg bg-gradient-to-br from-violet-600 to-purple-700 flex items-center justify-center">
          <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path></svg>
        </div>
        <h1 class="text-sm font-bold text-gray-900 hidden sm:block">Support Chat</h1>
      </div>

      {{-- Stats Pills --}}
      <div class="flex items-center gap-2">
        <div class="flex items-center gap-1 px-2 py-1 bg-emerald-50 border border-emerald-200 rounded-full">
          <span class="w-1.5 h-1.5 bg-emerald-500 rounded-full"></span>
          <span class="text-[11px] font-bold text-emerald-700">{{ $stats['open'] }} Aktif</span>
        </div>
        <div class="hidden sm:flex items-center gap-1 px-2 py-1 bg-amber-50 border border-amber-200 rounded-full">
          <span class="text-[11px] font-bold text-amber-700">{{ $stats['today'] }} Hari Ini</span>
        </div>
        @if($stats['unread'] > 0)
          <div class="flex items-center gap-1 px-2 py-1 bg-red-50 border border-red-200 rounded-full animate-pulse">
            <span class="text-[11px] font-bold text-red-700">{{ $stats['unread'] }} Belum Baca</span>
          </div>
        @endif
      </div>

      <a href="{{ route('filament.admin.pages.dashboard') }}" class="flex items-center gap-1 px-2.5 py-1.5 text-xs font-medium text-gray-500 hover:text-violet-700 hover:bg-violet-50 rounded-lg transition">
        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
        <span class="hidden sm:inline">Dashboard</span>
      </a>
    </div>
  </div>

  {{-- ===== MAIN CONTENT: 3-COLUMN LAYOUT ===== --}}
  <div class="flex-1 flex overflow-hidden">

    {{-- ===== COLUMN 1: CATEGORY SIDEBAR (Hidden on mobile) ===== --}}
    <div class="hidden lg:flex w-56 xl:w-64 flex-shrink-0 bg-white border-r border-gray-200 flex-col">
      <div class="p-3 border-b border-gray-100 flex items-center justify-between">
        <h2 class="text-xs font-bold text-gray-400 uppercase tracking-wider">Kategori</h2>
        @if($stats['unread'] > 0)
          <button wire:click="markAllAsRead" class="text-[10px] text-violet-600 hover:text-violet-800 font-bold transition flex items-center gap-1" title="Tandai semua pesan sudah dibaca">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
            Tandai Dibaca
          </button>
        @endif
      </div>
      <div class="flex-1 overflow-y-auto">
        {{-- All categories --}}
        <button @click="switchList('cat', null)" 
                :class="categoryFilter === null ? 'bg-violet-50 text-violet-700 border-r-2 border-violet-600' : 'text-gray-600 hover:bg-gray-50'"
                class="w-full text-left px-3 py-2.5 flex items-center justify-between transition-colors">
          <div class="flex items-center gap-2">
            <div :class="categoryFilter === null ? 'bg-violet-100 text-violet-600' : 'bg-gray-100 text-gray-400'" class="w-7 h-7 rounded-lg flex items-center justify-center">
              <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"></path></svg>
            </div>
            <span class="text-sm font-medium">Semua</span>
          </div>
          <span :class="categoryFilter === null ? 'text-violet-600' : 'text-gray-400'" class="text-xs font-bold">{{ $stats['total'] }}</span>
        </button>

        @foreach($categoryStats as $cat)
          <button @click="switchList('cat', {{ $cat->id }})" 
                  :class="categoryFilter === {{ $cat->id }} ? 'bg-violet-50 text-violet-700 border-r-2 border-violet-600' : 'text-gray-600 hover:bg-gray-50'"
                  class="w-full text-left px-3 py-2.5 flex items-center justify-between transition-colors">
            <div class="flex items-center gap-2 min-w-0">
              <div :class="categoryFilter === {{ $cat->id }} ? 'bg-violet-100' : 'bg-gray-100'" class="w-7 h-7 rounded-lg flex items-center justify-center flex-shrink-0">
                <span class="text-xs">{{ strtoupper(substr($cat->name, 0, 2)) }}</span>
              </div>
              <div class="min-w-0">
                <p class="text-sm font-medium truncate">{{ $cat->name }}</p>
                @if($cat->today_active > 0)
                  <p class="text-[10px] text-amber-600 font-medium">🔥 {{ $cat->today_active }} aktif hari ini</p>
                @endif
              </div>
            </div>
            <div class="flex flex-col items-end gap-0.5 flex-shrink-0 ml-2">
              @if($cat->open_count > 0)
                <span class="inline-flex items-center justify-center min-w-[20px] h-5 px-1.5 text-[10px] font-bold bg-emerald-100 text-emerald-700 rounded-full">{{ $cat->open_count }}</span>
              @endif
            </div>
          </button>
        @endforeach
      </div>

      {{-- Status Filter --}}
      <div class="p-3 border-t border-gray-100">
        <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-2">Status Filter</p>
        <div class="flex gap-1">
          <button @click="switchList('status', 'open')" :class="statusFilter === 'open' ? 'bg-emerald-100 text-emerald-700' : 'bg-gray-100 text-gray-500 hover:bg-gray-200'" class="flex-1 py-1.5 text-[11px] font-semibold rounded-md transition">Aktif</button>
          <button @click="switchList('status', 'closed')" :class="statusFilter === 'closed' ? 'bg-gray-200 text-gray-700' : 'bg-gray-100 text-gray-500 hover:bg-gray-200'" class="flex-1 py-1.5 text-[11px] font-semibold rounded-md transition">Selesai</button>
          <button @click="switchList('status', 'all')" :class="statusFilter === 'all' ? 'bg-violet-100 text-violet-700' : 'bg-gray-100 text-gray-500 hover:bg-gray-200'" class="flex-1 py-1.5 text-[11px] font-semibold rounded-md transition">Semua</button>
        </div>
      </div>
    </div>

    {{-- ===== COLUMN 2: THREAD LIST ===== --}}
    <div class="w-full sm:w-80 lg:w-80 xl:w-96 flex-shrink-0 bg-white border-r border-gray-200 flex flex-col {{ $showMobileChat ? 'hidden sm:flex' : 'flex' }}">
      {{-- Search + Mobile Filters --}}
      <div class="p-3 border-b border-gray-100 space-y-2">
        <div class="relative">
          <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
          <input type="search" wire:model.live.debounce.300ms="search" placeholder="Cari user atau task..." class="w-full pl-9 pr-3 py-2 text-sm bg-gray-50 border border-gray-200 rounded-lg focus:bg-white focus:ring-1 focus:ring-violet-500 focus:border-violet-500 placeholder-gray-400 transition">
        </div>

        {{-- Mobile-only category filter pills --}}
        <div class="flex lg:hidden gap-1.5 overflow-x-auto pb-1" style="scrollbar-width: none;">
          <button @click="switchList('cat', null)" :class="categoryFilter === null ? 'bg-violet-600 text-white' : 'bg-gray-100 text-gray-600'" class="flex-shrink-0 px-3 py-1 text-[11px] font-medium rounded-full">Semua</button>
          @foreach($categoryStats as $cat)
            <button @click="switchList('cat', {{ $cat->id }})" :class="categoryFilter === {{ $cat->id }} ? 'bg-violet-600 text-white' : 'bg-gray-100 text-gray-600'" class="flex-shrink-0 px-3 py-1 text-[11px] font-medium rounded-full whitespace-nowrap">
              {{ $cat->name }}
              @if($cat->open_count > 0)<span class="ml-0.5 text-[10px]">({{ $cat->open_count }})</span>@endif
            </button>
          @endforeach
        </div>

        {{-- Mobile-only status filter --}}
        <div class="flex lg:hidden gap-1">
          <button @click="switchList('status', 'open')" :class="statusFilter === 'open' ? 'bg-emerald-100 text-emerald-700' : 'bg-gray-100 text-gray-500'" class="flex-1 py-1 text-[11px] font-semibold rounded-md transition">Aktif</button>
          <button @click="switchList('status', 'closed')" :class="statusFilter === 'closed' ? 'bg-gray-200 text-gray-700' : 'bg-gray-100 text-gray-500'" class="flex-1 py-1 text-[11px] font-semibold rounded-md transition">Selesai</button>
          <button @click="switchList('status', 'all')" :class="statusFilter === 'all' ? 'bg-violet-100 text-violet-700' : 'bg-gray-100 text-gray-500'" class="flex-1 py-1 text-[11px] font-semibold rounded-md transition">Semua</button>
        </div>
      </div>

      {{-- Thread Items --}}
      <div id="thread-list-slot" class="flex-1 overflow-y-auto relative bg-white">
        {{-- Animated Loading Overlay --}}
        <div x-show="isListLoading" style="display: none;" class="absolute inset-0 bg-white/60 backdrop-blur-sm z-10 flex flex-col items-center justify-start pt-10">
            <svg class="w-8 h-8 text-violet-500 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
        </div>
        
        <div wire:key="thread-list-{{ $categoryFilter ?? 'all' }}-{{ $statusFilter }}" class="divide-y divide-gray-50 flex-1 min-h-full flex flex-col">
          @forelse($threads as $thread)
          @php
            $lastMsg = $thread->latestMessage;
            $isToday = $thread->last_message_at && $thread->last_message_at->isToday();
          @endphp
          <button 
            @click="
              const oldId = activeThreadId;
              const newId = {{ $thread->id }};
              if (oldId !== newId) {
                  const slot = document.getElementById('active-chat-slot');
                  const cacheStore = document.getElementById('chat-cache-container');
                  
                  if (slot && slot.firstElementChild && oldId) {
                      cachedNodes[oldId] = slot.firstElementChild;
                      cacheStore.appendChild(slot.firstElementChild);
                  }

                  activeThreadId = newId;

                  if (cachedNodes[newId] && slot) {
                      slot.appendChild(cachedNodes[newId]);
                      isLoadingThread = false; 
                  } else {
                      isLoadingThread = true;
                  }

                  if (pendingThreadTimeout) clearTimeout(pendingThreadTimeout);

                  pendingThreadTimeout = setTimeout(() => {
                      $wire.setThread(newId).then(() => { 
                          isLoadingThread = false; 
                      });
                  }, 100);
              }
            " 
            :class="activeThreadId === {{ $thread->id }} ? 'bg-violet-50' : 'hover:bg-gray-50'"
            class="w-full text-left px-3 py-3 transition-colors"
          >
            <div class="flex items-center gap-2.5">
              {{-- Avatar --}}
              <div class="relative flex-shrink-0">
                <div class="w-10 h-10 rounded-full flex items-center justify-center text-white font-bold text-sm"
                     :class="activeThreadId === {{ $thread->id }} ? 'bg-violet-600 ring-2 ring-violet-200' : 'bg-gray-400'">
                  {{ strtoupper(substr(optional($thread->user)->name ?? 'U', 0, 1)) }}
                </div>
                @if($thread->status === 'open')
                  <span class="absolute -bottom-0.5 -right-0.5 w-3 h-3 bg-emerald-400 border-2 border-white rounded-full"></span>
                @endif
              </div>

              {{-- Content --}}
              <div class="flex-1 min-w-0">
                <div class="flex items-center justify-between gap-2 mb-0.5">
                  <span class="text-sm font-semibold text-gray-900 truncate">{{ optional($thread->user)->name ?? 'User' }}</span>
                  <span class="text-[10px] {{ $isToday ? 'text-violet-600 font-bold' : 'text-gray-400' }} flex-shrink-0">{{ optional($thread->last_message_at)?->diffForHumans(null, true, true) ?? '' }}</span>
                </div>
                <p class="text-xs text-gray-500 truncate mb-1">{{ Str::limit(optional($lastMsg)->message ?? 'Belum ada pesan', 45) }}</p>
                <div class="flex items-center gap-1.5">
                  <span class="inline-block px-1.5 py-0.5 text-[9px] font-bold uppercase rounded {{ $thread->status === 'open' ? 'bg-emerald-100 text-emerald-700' : 'bg-gray-100 text-gray-500' }}">{{ $thread->status === 'open' ? 'Aktif' : 'Selesai' }}</span>
                  @if($thread->category)
                    <span class="inline-block px-1.5 py-0.5 text-[9px] font-medium rounded bg-violet-50 text-violet-600">{{ $thread->category->name }}</span>
                  @endif
                  @if($isToday)
                    <span class="text-[9px] text-amber-600">🔥</span>
                  @endif
                </div>
              </div>
            </div>
          </button>
        @empty
          <div class="flex flex-col items-center justify-center flex-1 py-12 px-8 text-center min-h-[200px]">
            <div class="w-14 h-14 bg-gray-100 rounded-full flex items-center justify-center mb-3">
              <svg class="w-6 h-6 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 006.586 13H4"></path></svg>
            </div>
            <p class="text-sm font-medium text-gray-500">Tidak ada percakapan</p>
            <p class="text-xs text-gray-400 mt-0.5">Coba ubah filter atau pencarian</p>
          </div>
         @endforelse
        </div>
      </div>
    </div>

    {{-- ===== COLUMN 3: CHAT PANEL ===== --}}
    <div class="relative flex-1 flex flex-col min-w-0 bg-gray-50 {{ !$showMobileChat ? 'hidden sm:flex' : 'flex' }}">

      {{-- Loading Overlay Skeleton --}}
      <div x-show="isLoadingThread" 
           style="display: none;" 
           class="absolute inset-0 z-50 bg-gray-50 flex flex-col pointer-events-none">
        <!-- Header Skeleton -->
        <div class="flex-shrink-0 bg-white border-b border-gray-200 px-4 py-3 flex items-center gap-3">
            <div class="w-9 h-9 rounded-full bg-gray-200 animate-pulse"></div>
            <div class="space-y-2 flex-1">
                <div class="h-3.5 bg-gray-200 rounded w-1/4 animate-pulse"></div>
                <div class="h-2.5 bg-gray-100 rounded w-1/3 animate-pulse"></div>
            </div>
        </div>
        <!-- Body Skeleton -->
        <div class="flex-1 p-4 space-y-6">
            <div class="flex gap-3 max-w-[80%]">
                <div class="w-8 h-8 rounded-full bg-gray-200 animate-pulse flex-shrink-0"></div>
                <div class="h-16 bg-gray-200 rounded-2xl rounded-tl-none w-64 animate-pulse"></div>
            </div>
            <div class="flex gap-3 max-w-[80%] ml-auto justify-end">
                <div class="h-12 bg-violet-100 rounded-2xl rounded-tr-none w-48 animate-pulse"></div>
            </div>
            <div class="flex gap-3 max-w-[80%]">
                <div class="w-8 h-8 rounded-full bg-gray-200 animate-pulse flex-shrink-0"></div>
                <div class="h-24 bg-gray-200 rounded-2xl rounded-tl-none w-72 animate-pulse"></div>
            </div>
        </div>
      </div>


      <div id="active-chat-slot" class="flex flex-col flex-1 min-h-0 relative w-full h-full">
        @if($activeThread)
          <div class="flex flex-col flex-1 min-h-0 w-full h-full" wire:key="thread-wrapper-{{ $activeThread->id }}">
            {{-- Chat Header --}}
            <div class="flex-shrink-0 bg-white border-b border-gray-200 px-4 py-2.5">
          <div class="flex items-center justify-between">
            <div class="flex items-center gap-3">
              {{-- Mobile back button --}}
              <button wire:click="backToList" class="sm:hidden p-1 -ml-1 text-gray-500 hover:text-violet-600">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
              </button>

              <div class="w-9 h-9 rounded-full bg-violet-600 flex items-center justify-center text-white font-bold text-sm">
                {{ strtoupper(substr(optional($activeThread->user)->name ?? 'U', 0, 1)) }}
              </div>
              <div class="min-w-0">
                <div class="flex items-center gap-1.5">
                  <h2 class="text-sm font-bold text-gray-900 truncate">{{ optional($activeThread->user)->name ?? 'User' }}</h2>
                  <span class="flex-shrink-0 inline-flex items-center px-1.5 py-0.5 rounded text-[9px] font-bold uppercase {{ $activeThread->status === 'open' ? 'bg-emerald-100 text-emerald-700' : 'bg-gray-100 text-gray-500' }}">
                    {{ $activeThread->status === 'open' ? 'Aktif' : 'Selesai' }}
                  </span>
                </div>
                <p class="text-[11px] text-gray-500 truncate max-w-[300px]">{{ optional($activeThread->task)->title ?? $activeThread->title }}</p>
              </div>
            </div>

            <div class="flex items-center gap-1.5">
              @if($activeThread->status === 'open')
                <button wire:click="closeThread" wire:confirm="Tandai thread ini selesai?" class="px-2.5 py-1.5 text-[11px] font-semibold text-gray-500 hover:text-emerald-700 bg-gray-100 hover:bg-emerald-50 rounded-lg transition-all">
                  <svg class="w-3.5 h-3.5 inline -mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                  <span class="hidden sm:inline ml-1">Selesai</span>
                </button>
              @else
                <button wire:click="reopenThread" wire:confirm="Buka kembali thread ini?" class="px-2.5 py-1.5 text-[11px] font-semibold text-gray-500 hover:text-violet-700 bg-gray-100 hover:bg-violet-50 rounded-lg transition-all">
                  <svg class="w-3.5 h-3.5 inline -mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                  <span class="hidden sm:inline ml-1">Buka Lagi</span>
                </button>
              @endif
            </div>
          </div>
        </div>

        {{-- Thread Detail Bar --}}
        <div class="flex-shrink-0 bg-gray-50 border-b border-gray-100 px-4 py-1.5">
          <div class="flex items-center gap-3 text-[11px] text-gray-400 overflow-x-auto" style="scrollbar-width:none;">
            @if($activeThread->user)
              <span class="flex items-center gap-1 flex-shrink-0">
                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                {{ $activeThread->user->email ?? '-' }}
              </span>
            @endif
            @if($activeThread->category)
              <span class="flex items-center gap-1 flex-shrink-0">
                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path></svg>
                {{ $activeThread->category->name }}
              </span>
            @endif
            <span class="flex items-center gap-1 flex-shrink-0">
              <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
              {{ optional($activeThread->created_at)->format('d M Y') }}
            </span>
            <span class="flex items-center gap-1 flex-shrink-0">
              <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"></path></svg>
              {{ $activeThread->messages->count() }} pesan
            </span>
          </div>
        </div>

        {{-- Messages --}}
        <div id="chat-messages-container"
          class="flex-1 overflow-y-auto px-4 py-3 space-y-2.5 relative"
          x-data="{ scroll() { this.$el.scrollTop = this.$el.scrollHeight; } }"
          x-init="scroll(); $nextTick(() => scroll()); new MutationObserver(() => scroll()).observe($el, { childList: true, subtree: true });"
          @message-sent.window="setTimeout(() => scroll(), 50)"
        >
          {{-- Start marker --}}
          <div class="flex items-center gap-3 my-2">
            <div class="flex-1 h-px bg-gray-200"></div>
            <span class="text-[10px] text-gray-400 font-medium">{{ optional($activeThread->created_at)->format('d M Y, H:i') }}</span>
            <div class="flex-1 h-px bg-gray-200"></div>
          </div>

          @foreach($activeThread->messages as $message)
            @php $isAdmin = in_array($message->sender_role, ['admin', 'superadmin']); $isPrivate = !empty($message->meta['is_private']); @endphp

            <div class="flex {{ $isAdmin ? 'justify-end' : 'justify-start' }}" wire:key="sm-{{ $message->id }}">
              <div class="flex {{ $isAdmin ? 'flex-row-reverse' : 'flex-row' }} items-end gap-1.5 max-w-[75%]">
                @if(!$isAdmin)
                  <div class="flex-shrink-0 w-6 h-6 rounded-full bg-gray-300 flex items-center justify-center text-white text-[10px] font-bold">
                    {{ strtoupper(substr(optional($message->sender)->name ?? 'U', 0, 1)) }}
                  </div>
                @endif
                <div>
                  <div class="{{ $isPrivate ? 'bg-amber-50 border border-amber-300 text-amber-900' : ($isAdmin ? 'bg-violet-600 text-white' : 'bg-white border border-gray-200 text-gray-800') }} rounded-2xl {{ $isAdmin ? 'rounded-br-md' : 'rounded-bl-md' }} px-3 py-2 shadow-sm">
                    @if($isPrivate)
                      <p class="text-[9px] font-bold text-amber-600 uppercase tracking-wider mb-1">🔒 Catatan Internal</p>
                    @endif
                    @if(!empty($message->meta['file_path']))
                      <a href="{{ asset('storage/' . $message->meta['file_path']) }}" target="_blank" class="flex items-center gap-1.5 text-xs {{ $isAdmin ? 'text-violet-200 hover:text-white' : 'text-violet-600' }} underline mb-1">
                        📎 {{ $message->meta['file_name'] ?? 'File' }}
                      </a>
                    @endif
                    @if($message->message)
                      <p class="text-[13px] leading-relaxed whitespace-pre-wrap break-words" style="word-break: break-word;">{{ $message->message }}</p>
                    @endif
                  </div>
                  <span class="text-[10px] {{ $isAdmin ? 'text-gray-400 text-right' : 'text-gray-400' }} block mt-0.5 px-1">{{ $message->created_at->format('H:i') }}</span>
                </div>
              </div>
            </div>
          @endforeach

          {{-- Optimistic Sending Bubbles Queue --}}
          <template x-for="optMsg in optimisticMessages" :key="optMsg.id">
            <div class="flex justify-end mt-2">
              <div class="flex flex-row-reverse items-end gap-1.5 max-w-[75%]">
                <div>
                  <div class="bg-violet-600/70 text-white rounded-2xl rounded-br-md px-3 py-2 shadow-sm animate-pulse">
                    <p class="text-[13px] leading-relaxed whitespace-pre-wrap break-words" x-text="optMsg.text"></p>
                  </div>
                  <span class="text-[10px] text-gray-400 text-right block mt-0.5 px-1">Mengirim...</span>
                </div>
              </div>
            </div>
          </template>

          {{-- Typing Indicator --}}
          <div x-show="userIsTyping" x-transition style="display:none;" class="flex justify-start mt-1">
            <div class="flex items-end gap-1.5">
              <div class="w-6 h-6 rounded-full bg-gray-300 flex items-center justify-center text-white text-[10px] font-bold flex-shrink-0">U</div>
              <div class="bg-white border border-gray-200 rounded-2xl rounded-bl-md px-3 py-2 shadow-sm">
                <span class="flex gap-1 items-center h-4">
                  <span class="w-1.5 h-1.5 bg-gray-400 rounded-full animate-bounce" style="animation-delay:0ms"></span>
                  <span class="w-1.5 h-1.5 bg-gray-400 rounded-full animate-bounce" style="animation-delay:150ms"></span>
                  <span class="w-1.5 h-1.5 bg-gray-400 rounded-full animate-bounce" style="animation-delay:300ms"></span>
                </span>
              </div>
            </div>
          </div>
        </div>

        {{-- Input Area --}}
        <div class="flex-shrink-0 bg-white border-t border-gray-200 px-4 py-2.5">
          @if($activeThread->status === 'open')
            {{-- Quick Replies --}}
            <div class="mb-2 flex flex-wrap gap-1">
              @php
                $quickReplies = [
                  ['✅', 'Disetujui', 'Selamat! Bukti Anda sudah disetujui. Pembayaran akan segera diproses.'],
                  ['🔄', 'Submit Ulang', 'Mohon untuk submit ulang bukti yang benar.'],
                  ['⚠️', 'Kurang Lengkap', 'Bukti yang dikirim kurang lengkap, mohon lengkapi terlebih dahulu sebelum submit.'],
                  ['❌', 'Format Salah', 'Format bukti tidak sesuai ketentuan, silakan perbaiki dan kirim ulang.'],
                  ['👍', 'Sudah Bagus', 'Bukti sudah bagus, sedang dalam proses review. Mohon ditunggu ya.'],
                  ['⏳', 'Mohon Tunggu', 'Mohon tunggu, bukti Anda sedang direview. Terima kasih atas kesabarannya.'],
                ];
              @endphp
              @foreach($quickReplies as $qr)
                <button type="button" @click="document.getElementById('admin-chat-input').value = '{{ $qr[2] }}'; submitMessage()" class="inline-flex items-center gap-0.5 px-2 py-0.5 text-[10px] font-medium text-gray-500 bg-gray-50 border border-gray-200 rounded-full hover:bg-violet-50 hover:text-violet-700 hover:border-violet-200 transition">
                  <span>{{ $qr[0] }}</span><span>{{ $qr[1] }}</span>
                </button>
              @endforeach
            </div>

            {{-- Message Input --}}
            <div class="flex items-end gap-2">
              <div class="flex-1 relative">
                {{-- Slash Command Popup --}}
                <div x-show="showSlashMenu && filteredSlash().length > 0" x-transition style="display:none;"
                  class="absolute bottom-full left-0 right-0 mb-1 bg-white border border-gray-200 rounded-xl shadow-xl overflow-hidden z-50">
                  <div class="px-2 py-1 bg-gray-50 border-b border-gray-100 flex items-center gap-1.5">
                    <span class="text-[10px] text-gray-400 font-medium">/</span>
                    <span class="text-[10px] text-gray-500">Pilih template balasan cepat</span>
                    <kbd class="ml-auto text-[9px] bg-gray-200 text-gray-500 px-1 rounded">↑↓ pilih · Enter pakai · Esc tutup</kbd>
                  </div>
                  <ul class="max-h-40 overflow-y-auto">
                    <template x-for="(cmd, idx) in filteredSlash()" :key="idx">
                      <li @click="applySlash(cmd)"
                          :class="idx === slashIndex ? 'bg-violet-50 text-violet-700' : 'text-gray-700 hover:bg-gray-50'"
                          class="px-3 py-1.5 text-xs cursor-pointer transition-colors flex items-start gap-2">
                        <span x-text="cmd.label" class="font-medium flex-shrink-0"></span>
                        <span x-text="cmd.text" class="text-gray-400 text-[11px] truncate"></span>
                      </li>
                    </template>
                  </ul>
                </div>

                <textarea
                  id="admin-chat-input"
                  wire:model="newMessage"
                  rows="1"
                  class="w-full px-3 py-2 text-sm border rounded-xl focus:ring-2 resize-none placeholder-gray-400 transition"
                  :class="isPrivateNote ? 'bg-amber-50 border-amber-300 focus:ring-amber-200 focus:border-amber-400' : 'bg-gray-50 border-gray-200 focus:bg-white focus:ring-violet-200 focus:border-violet-400'"
                  :placeholder="isPrivateNote ? '🔒 Catatan internal (tidak terlihat user)...' : 'Ketik pesan atau / untuk template...' "
                  style="min-height: 40px; max-height: 100px;"
                  x-data="{ resize() { $el.style.height = 'auto'; $el.style.height = Math.min($el.scrollHeight, 100) + 'px'; } }"
                  x-init="resize()"
                  @input="resize(); handleSlashInput($event)"
                  @keydown="handleSlashKey($event)"
                  @keydown.enter.prevent="if(!showSlashMenu && !$event.shiftKey) { submitMessage(); $el.style.height = '40px'; }"
                ></textarea>
              </div>
              {{-- Private Note Toggle --}}
              <button type="button" @click="isPrivateNote = !isPrivateNote"
                :class="isPrivateNote ? 'bg-amber-100 text-amber-700 border-amber-300 hover:bg-amber-200' : 'bg-gray-100 text-gray-400 hover:bg-gray-200 border-gray-200'"
                class="flex-shrink-0 w-10 h-10 flex items-center justify-center rounded-xl border transition-all"
                title="Toggle Catatan Internal">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
              </button>
              <button type="button" @click="submitMessage()" class="flex-shrink-0 w-10 h-10 flex items-center justify-center rounded-xl bg-violet-600 hover:bg-violet-700 text-white shadow transition-all">
                <svg class="w-4 h-4 rotate-90" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path></svg>
              </button>
            </div>
            <p class="text-[10px] text-gray-400 mt-1 text-center">Enter kirim · Shift+Enter baris baru</p>
          @else
            <div class="py-3 text-center bg-gray-50 rounded-xl border border-gray-200 flex items-center justify-center gap-2">
              <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
              <span class="text-sm text-gray-500 font-medium">Thread ditutup</span>
              <button wire:click="reopenThread" class="text-sm text-violet-600 font-semibold hover:underline ml-2">Buka Lagi?</button>
            </div>
          @endif
        </div>

          </div>
        @else
          {{-- Empty State --}}
          <div class="flex-1 flex flex-col items-center justify-center p-8 text-center" wire:key="empty-state">
            <div class="w-16 h-16 mb-4 bg-gray-100 rounded-2xl flex items-center justify-center">
              <svg class="w-8 h-8 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path></svg>
            </div>
            <h3 class="text-base font-bold text-gray-600 mb-1">Pilih Percakapan</h3>
            <p class="text-sm text-gray-400 max-w-xs">Pilih thread dari daftar di sebelah kiri untuk mulai membalas.</p>
          </div>
        @endif
      </div>
    </div>
  </div>

  {{-- Hidden Cache Container --}}
  <div id="chat-cache-container" wire:ignore style="display: none;"></div>
  <div id="list-cache-container" wire:ignore style="display: none;"></div>
</div>
