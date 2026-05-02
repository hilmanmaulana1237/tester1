<div x-data="{
    open: false,
    tabFilter: 'all',
    audioCtx: null,
    previousCount: {{ $unreadCount }},
    isNavigating: false,
    notifCount: {{ $notifications->count() }},
    lockedHeight: null,
    clickNotif(id, actionUrl) {
        // Lock the list height BEFORE skeleton shows (prevents panel from jumping)
        const list = document.getElementById('notif-list-container');
        if (list) this.lockedHeight = list.scrollHeight;
        // Immediately show skeleton
        this.isNavigating = true;
        // Fire mark-as-read in background (we don't await it)
        this.$wire.markAsRead(id).catch(() => {});
        // Navigate after a tiny paint frame so the skeleton is visible
        if (actionUrl) {
            setTimeout(() => { window.location.href = actionUrl; }, 180);
        } else {
            setTimeout(() => { this.isNavigating = false; this.lockedHeight = null; }, 1500);
        }
    },
    init() {
        if ('Notification' in window && Notification.permission === 'default') {
            Notification.requestPermission();
        }

        const AC = window.AudioContext || window.webkitAudioContext;
        if (AC) {
            this.audioCtx = new AC();
            document.addEventListener('click', () => {
                if (this.audioCtx && this.audioCtx.state === 'suspended') {
                    this.audioCtx.resume();
                }
            }, { once: true });
        }

        setInterval(() => {
            $wire.loadNotifications().then(() => {
                const currentCount = parseInt($wire.unreadCount) || 0;
                if (currentCount > this.previousCount) {
                    this.showBrowserNotification(currentCount - this.previousCount);
                    this.playSound();
                }
                this.previousCount = currentCount;
            });
        }, 15000);
    },
    playSound() {
        if (!this.audioCtx) return;
        try {
            if (this.audioCtx.state === 'suspended') {
                this.audioCtx.resume();
            }

            const now = this.audioCtx.currentTime;

            const osc1 = this.audioCtx.createOscillator();
            const gain1 = this.audioCtx.createGain();
            osc1.type = 'sine';
            osc1.frequency.setValueAtTime(880, now);
            gain1.gain.setValueAtTime(0, now);
            gain1.gain.linearRampToValueAtTime(0.3, now + 0.02);
            gain1.gain.exponentialRampToValueAtTime(0.001, now + 0.3);
            osc1.connect(gain1);
            gain1.connect(this.audioCtx.destination);
            osc1.start(now);
            osc1.stop(now + 0.4);

            const osc2 = this.audioCtx.createOscillator();
            const gain2 = this.audioCtx.createGain();
            osc2.type = 'sine';
            osc2.frequency.setValueAtTime(659.25, now + 0.2);
            gain2.gain.setValueAtTime(0, now + 0.2);
            gain2.gain.linearRampToValueAtTime(0.5, now + 0.22);
            gain2.gain.exponentialRampToValueAtTime(0.001, now + 0.7);
            osc2.connect(gain2);
            gain2.connect(this.audioCtx.destination);
            osc2.start(now + 0.2);
            osc2.stop(now + 0.8);
            
        } catch(e) {
            console.error('Audio failed:', e);
        }
    },
    showBrowserNotification(count) {
        if ('Notification' in window && Notification.permission === 'granted') {
            new Notification('Notifikasi Baru', {
                body: count + ' notifikasi baru tersedia.',
                icon: '/favicon.ico',
                tag: 'app-notification'
            });
        }
    }
}" class="relative">

    {{-- Bell Button --}}
    <button @click="open = !open"
        class="relative p-2 text-zinc-500 hover:text-violet-600 transition-colors rounded-lg hover:bg-violet-50">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75v-.7V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0" />
        </svg>
        @if($unreadCount > 0)
            <span class="absolute -top-0.5 -right-0.5 inline-flex items-center justify-center px-1.5 py-0.5 text-[10px] font-bold leading-none text-white bg-red-500 rounded-full min-w-[18px] animate-pulse">
                {{ $unreadCount > 99 ? '99+' : $unreadCount }}
            </span>
        @endif
    </button>

    {{-- Dropdown Panel --}}
    <div x-show="open" x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 translate-y-1" x-transition:enter-end="opacity-100 translate-y-0"
        x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 translate-y-0"
        x-transition:leave-end="opacity-0 translate-y-1" @click.away="open = false"
        x-cloak
        class="absolute right-0 mt-2 w-80 sm:w-96 max-w-[min(92vw,400px)] bg-white rounded-2xl shadow-2xl border border-gray-200 z-50 overflow-hidden"
        style="display: none; max-height: calc(100vh - 100px);">

        {{-- Header --}}
        <div class="flex items-center justify-between px-4 py-3 bg-gradient-to-r from-violet-600 to-purple-600">
            <h3 class="text-sm font-bold text-white flex items-center gap-2">
                🔔 Notifikasi
                @if($unreadCount > 0)
                    <span class="text-[10px] bg-white/20 text-white px-1.5 py-0.5 rounded-full">{{ $unreadCount }} baru</span>
                @endif
            </h3>
            <div class="flex items-center gap-2">
                @if($unreadCount > 0)
                    <button wire:click="markAllAsRead" class="text-[10px] text-white/80 hover:text-white font-medium transition">
                        ✓ Baca Semua
                    </button>
                @endif
            </div>
        </div>

        {{-- Tab Filters --}}
        <div class="flex border-b border-gray-100 bg-gray-50">
            <button @click="tabFilter = 'all'"
                :class="tabFilter === 'all' ? 'text-violet-600 border-b-2 border-violet-600 bg-white' : 'text-gray-500 hover:text-gray-700'"
                class="flex-1 py-2 text-xs font-semibold text-center transition">
                Semua
            </button>
            <button @click="tabFilter = 'unread'"
                :class="tabFilter === 'unread' ? 'text-violet-600 border-b-2 border-violet-600 bg-white' : 'text-gray-500 hover:text-gray-700'"
                class="flex-1 py-2 text-xs font-semibold text-center transition">
                Belum Dibaca
                @if($unreadCount > 0)
                    <span class="ml-1 inline-flex items-center justify-center w-4 h-4 text-[9px] font-bold text-white bg-red-500 rounded-full">{{ $unreadCount > 9 ? '9+' : $unreadCount }}</span>
                @endif
            </button>
        </div>

        {{-- Notifications List --}}
        <div id="notif-list-container"
            class="overflow-y-auto divide-y divide-gray-50 relative"
            :style="lockedHeight ? 'height:' + lockedHeight + 'px; overflow:hidden;' : 'max-height: min(55vh, 320px);'">

            {{-- Navigation Skeleton Overlay --}}
            <div x-show="isNavigating" x-transition style="display:none;"
                class="absolute inset-0 bg-white z-20 flex flex-col divide-y divide-gray-50">
                <template x-for="i in notifCount" :key="i">
                    <div class="flex items-start gap-3 px-4 py-3 animate-pulse">
                        <div class="w-10 h-10 rounded-xl bg-gray-200 flex-shrink-0"></div>
                        <div class="flex-1 space-y-2 pt-1">
                            <div class="h-3 bg-gray-200 rounded" :style="'width:' + (i % 2 === 0 ? 60 : 75) + '%'"></div>
                            <div class="h-2.5 bg-gray-100 rounded w-full"></div>
                            <div class="h-2.5 bg-gray-100 rounded" :style="'width:' + (i % 3 === 0 ? 50 : 40) + '%'"></div>
                        </div>
                    </div>
                </template>
            </div>

            @foreach($notifications as $notification)
                <div class="group relative" x-show="tabFilter === 'all' || {{ $notification->read ? 'false' : 'true' }}">
                    <button
                        @click="clickNotif({{ $notification->id }}, '{{ $notification->action_url ?? '' }}')"
                        class="w-full text-left px-4 py-3 hover:bg-violet-50/50 transition-colors {{ !$notification->read ? 'bg-violet-50/30 border-l-3 border-l-violet-500' : '' }}">
                        <div class="flex items-start gap-3">
                            {{-- Icon --}}
                            <div class="flex-shrink-0 w-10 h-10 rounded-xl {{ $notification->color_class }} flex items-center justify-center text-base shadow-sm">
                                {{ $notification->icon }}
                            </div>

                            {{-- Content --}}
                            <div class="flex-1 min-w-0">
                                <div class="flex items-start justify-between gap-2">
                                    <h4 class="text-sm font-semibold text-gray-800 truncate {{ !$notification->read ? 'text-gray-900' : 'text-gray-600' }}">
                                        {{ $notification->title }}
                                    </h4>
                                    @if(!$notification->read)
                                        <span class="flex-shrink-0 w-2 h-2 bg-violet-500 rounded-full mt-1.5 animate-pulse"></span>
                                    @endif
                                </div>
                                <p class="text-xs text-gray-500 mt-0.5 line-clamp-2 leading-relaxed">
                                    {{ $notification->message }}
                                </p>
                                <div class="flex items-center justify-between mt-1.5">
                                    <span class="text-[10px] text-gray-400">
                                        {{ $notification->created_at->diffForHumans() }}
                                    </span>
                                    <span class="text-[10px] text-violet-500 font-medium opacity-0 group-hover:opacity-100 transition">
                                        Klik untuk buka →
                                    </span>
                                </div>
                            </div>
                        </div>
                    </button>

                    {{-- Delete button --}}
                    <button wire:click="deleteNotification({{ $notification->id }})"
                        class="absolute top-2 right-2 p-1 rounded-lg bg-white shadow-sm border border-gray-200 text-gray-400 hover:text-red-500 hover:border-red-200 opacity-0 group-hover:opacity-100 transition-all"
                        title="Hapus">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>
            @endforeach

            @php
                $visibleUnreadCount = $notifications->where('read', false)->count();
            @endphp
            
            @if($notifications->count() === 0)
                <div x-show="tabFilter === 'all'" x-cloak class="px-4 py-10 text-center">
                    <div class="w-14 h-14 mx-auto mb-3 bg-gray-100 rounded-2xl flex items-center justify-center">
                        <svg class="w-7 h-7 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75v-.7V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0" />
                        </svg>
                    </div>
                    <p class="text-sm font-medium text-gray-500">Belum ada notifikasi</p>
                    <p class="text-xs text-gray-400 mt-0.5">Notifikasi akan muncul di sini</p>
                </div>
            @endif

            @if($visibleUnreadCount === 0)
                <div x-show="tabFilter === 'unread'" x-cloak class="px-4 py-10 text-center">
                    <div class="w-14 h-14 mx-auto mb-3 bg-gray-100 rounded-2xl flex items-center justify-center">
                        <svg class="w-7 h-7 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75v-.7V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0" />
                        </svg>
                    </div>
                    <p class="text-sm font-medium text-gray-500">Semua sudah dibaca!</p>
                    <p class="text-xs text-gray-400 mt-0.5">Kamu sudah up-to-date 🎉</p>
                </div>
            @endif
        </div>

        {{-- Footer --}}
        @if($notifications->count() > 0)
            <div class="px-4 py-2.5 bg-gray-50 border-t border-gray-100 flex items-center justify-between">
                <button wire:click="deleteAllRead"
                    class="text-[10px] text-red-400 hover:text-red-600 font-medium transition">
                    🗑️ Hapus yang Dibaca
                </button>
                <span class="text-[10px] text-gray-400">
                    {{ $notifications->count() }} notifikasi
                </span>
            </div>
        @endif
    </div>

</div>
