<div class="space-y-4">
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
        {{-- Chat Header --}}
        <div class="flex items-center justify-between px-4 py-3 bg-gradient-to-r from-blue-600 to-indigo-600">
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10 rounded-full bg-white/20 flex items-center justify-center">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                    </svg>
                </div>
                <div>
                    <h3 class="text-white font-semibold">Chat dengan {{ $record->user->name ?? 'User' }}</h3>
                    <p class="text-white/80 text-xs">Task: {{ Str::limit($record->task->title ?? 'N/A', 40) }}</p>
                </div>
            </div>
            <button 
                wire:click="refreshMessages" 
                class="p-2 hover:bg-white/10 rounded-lg transition-colors"
                title="Refresh messages"
            >
                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                </svg>
            </button>
        </div>

        {{-- Messages Area --}}
        <div 
            x-data="{
                scrollToBottom() {
                    $nextTick(() => {
                        const container = $refs.messagesContainer;
                        if (container) {
                            container.scrollTop = container.scrollHeight;
                        }
                    });
                }
            }"
            x-init="scrollToBottom()"
            @scroll-to-bottom.window="scrollToBottom()"
        >
            <div 
                x-ref="messagesContainer"
                class="overflow-y-auto p-4 space-y-3 bg-gray-50 dark:bg-gray-900"
                style="max-height: 500px; min-height: 300px;"
            >
                @forelse($messages as $message)
                    @php
                        $isAdmin = $message['sender_type'] === 'admin';
                        $isCurrentUser = $message['user_id'] === auth()->id();
                    @endphp
                    
                    <div wire:key="widget-msg-{{ $message['id'] ?? uniqid() }}" class="flex {{ $isCurrentUser ? 'justify-end' : 'justify-start' }}">
                        <div class="flex items-end space-x-2 max-w-[75%] {{ $isCurrentUser ? 'flex-row-reverse space-x-reverse' : '' }}">
                            {{-- Avatar --}}
                            <div class="flex-shrink-0 w-8 h-8 rounded-full {{ $isAdmin ? 'bg-gradient-to-br from-amber-500 to-orange-600' : 'bg-gradient-to-br from-blue-500 to-indigo-600' }} flex items-center justify-center text-white text-xs font-bold shadow-md">
                                {{ $isAdmin ? 'A' : substr($message['user']['name'], 0, 1) }}
                            </div>
                            
                            {{-- Message Bubble --}}
                            <div class="flex flex-col {{ $isCurrentUser ? 'items-end' : 'items-start' }}">
                                {{-- Sender Name --}}
                                <div class="text-xs text-gray-500 dark:text-gray-400 mb-1 px-1">
                                    {{ $isAdmin ? '👨‍💼 Admin' : $message['user']['name'] }}
                                </div>
                                
                                {{-- Message Content --}}
                                <div class="relative group">
                                    <div class="rounded-2xl px-4 py-2.5 shadow-sm {{ $isCurrentUser ? 'bg-gradient-to-br from-blue-600 to-indigo-600 text-white rounded-br-none' : 'bg-white dark:bg-gray-800 text-gray-800 dark:text-gray-200 border border-gray-200 dark:border-gray-700 rounded-bl-none' }}">
                                        <p class="text-sm leading-relaxed break-words whitespace-pre-wrap overflow-wrap-anywhere" style="word-break: break-word; overflow-wrap: anywhere;">{{ $message['message'] }}</p>
                                    </div>
                                    
                                    {{-- Timestamp --}}
                                    <div class="text-xs text-gray-400 dark:text-gray-500 mt-1 px-1">
                                        {{ \Carbon\Carbon::parse($message['created_at'])->timezone('Asia/Jakarta')->format('H:i, d M Y') }}
                                        @if($message['is_read'])
                                            <span class="ml-1 text-green-500">✓✓</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="flex flex-col items-center justify-center h-full text-gray-400 dark:text-gray-500 py-12">
                        <svg class="w-16 h-16 mb-3 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                        </svg>
                        <p class="text-sm font-medium">Belum ada percakapan</p>
                        <p class="text-xs mt-1">Kirim pesan pertama untuk memulai diskusi</p>
                    </div>
                @endforelse
            </div>
        </div>

        {{-- Input Area --}}
        <div class="p-4 bg-white dark:bg-gray-800 border-t border-gray-200 dark:border-gray-700">
            <form wire:submit.prevent="sendMessage" class="flex items-end space-x-3">
                {{-- Text Input --}}
                <div class="flex-1 relative">
                    <textarea 
                        wire:model="newMessage"
                        wire:ignore.self
                        placeholder="Ketik pesan Anda..."
                        rows="1"
                        class="w-full px-4 py-2.5 pr-12 rounded-xl border-2 border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-900 text-gray-800 dark:text-gray-200 placeholder-gray-400 focus:border-blue-500 dark:focus:border-blue-400 focus:ring-2 focus:ring-blue-500/20 resize-none transition-all"
                        style="max-height: 120px;"
                    ></textarea>
                    
                    {{-- Character count --}}
                    <div class="absolute bottom-2 right-2 text-xs text-gray-400">
                        {{ strlen($newMessage) }}/1000
                    </div>
                </div>

                {{-- Send Button --}}
                <button 
                    type="submit"
                    class="flex-shrink-0 px-5 py-2.5 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white font-medium rounded-xl shadow-md hover:shadow-lg transform hover:scale-105 transition-all duration-200 disabled:opacity-50 disabled:cursor-not-allowed disabled:transform-none"
                    wire:loading.attr="disabled"
                    wire:target="sendMessage"
                >
                    <span wire:loading.remove wire:target="sendMessage" class="flex items-center space-x-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                        </svg>
                        <span>Kirim</span>
                    </span>
                    <span wire:loading wire:target="sendMessage" class="flex items-center space-x-2">
                        <svg class="animate-spin w-5 h-5" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <span>Mengirim...</span>
                    </span>
                </button>
            </form>
        </div>

        {{-- Auto-refresh polling dengan Alpine.js --}}
        <div 
            x-data="{ pollingInterval: null }"
            x-init="
                pollingInterval = setInterval(() => {
                    // Only refresh if textarea is not focused
                    const textarea = document.querySelector('textarea[wire\\:model=\"newMessage\"]');
                    if (!textarea || document.activeElement !== textarea) {
                        $wire.call('refreshMessages');
                    }
                }, 10000);
            "
            x-destroy="clearInterval(pollingInterval)"
        ></div>
    </div>
</div>
