<div class="flex flex-col h-full bg-white" 
     x-data="{
         userTaskId: @entangle('userTaskId'),
         lastMessageCount: @entangle('lastMessageCount'),
         pollInterval: null,
         optimisticMessages: [],
         adminIsTyping: false,
         typingTimeout: null,
         echoChannel: null,
         async submitMessage() {
             const textarea = document.getElementById('task-message-input-{{ $userTask->id }}');
             if (!textarea) return;
             const text = textarea.value.trim();
             if (!text) return;
             
             const tempId = Date.now();
             this.optimisticMessages.push({ id: tempId, text: text });
             
             // Clear visually without wiping the Livewire state payload
             textarea.value = '';
             textarea.style.height = '40px';
             
             setTimeout(() => {
                 const container = document.getElementById('chat-messages-{{ $userTask->id }}');
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
             if (!this.userTaskId) return;
             try {
                 const response = await fetch('/api/task-thread/' + this.userTaskId + '/count');
                 if (response.ok) {
                     const data = await response.json();
                     if (data.count !== this.lastMessageCount) {
                         $wire.checkForNewMessages(data.count);
                     }
                 }
             } catch (e) {
                 // ignore network errors
             }
         }
     }"
     x-init="
         $watch('userTaskId', (val) => {
             if (val) startPolling();
             else if (pollInterval) clearInterval(pollInterval);
         });
         if (userTaskId) startPolling();
         // Join Echo channel for typing whispers
         if (userTaskId && typeof window.Echo !== 'undefined') {
             echoChannel = window.Echo.private('chat.' + userTaskId);
             echoChannel.listenForWhisper('typing', () => {
                 adminIsTyping = true;
                 if (typingTimeout) clearTimeout(typingTimeout);
                 typingTimeout = setTimeout(() => { adminIsTyping = false; }, 2500);
             });
         }
     "
>
    
    {{-- Header - Purple Theme --}}
    <div class="flex-shrink-0 bg-gradient-to-r from-violet-600 to-purple-600 px-4 py-3 shadow-md">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-2">
                <div class="w-10 h-10 bg-white/20 backdrop-blur rounded-xl flex items-center justify-center">
                    <span class="text-xl">💬</span>
                </div>
                <div>
                    <h3 class="text-white font-semibold text-sm">
                        @if(auth()->user()->role === 'user')
                            Support Chat
                        @else
                            {{ $userTask->user->name ?? 'User' }}
                        @endif
                    </h3>
                    <p class="text-white/80 text-xs">{{ Str::limit($userTask->task->title ?? 'Task', 30) }}</p>
                </div>
            </div>
            <div class="flex items-center gap-1">
                <span class="w-2 h-2 bg-violet-300 rounded-full animate-pulse"></span>
                <span class="text-white text-xs">Online</span>
            </div>
            <button 
                type="button" 
                @click="$dispatch('close-chat')" 
                class="ml-3 p-1 rounded-full hover:bg-white/20 text-white transition-colors focus:outline-none focus:ring-2 focus:ring-white/50"
                aria-label="Tutup Chat"
            >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>
    </div>

    {{-- Messages Area --}}
    <div 
        id="chat-messages-{{ $userTask->id }}"
        x-ref="messagesContainer"
        class="flex-1 overflow-y-auto p-3 space-y-2 bg-gray-50"
        style="scrollbar-width: thin;"
        x-data="{ 
            scrollToBottom() {
                this.$el.scrollTop = this.$el.scrollHeight;
            }
        }"
        x-init="
            // Scroll immediately on init
            scrollToBottom();
            
            // Also scroll after DOM ready
            $nextTick(() => {
                scrollToBottom();
                setTimeout(() => scrollToBottom(), 100);
            });
            
            // Watch for DOM changes
            new MutationObserver(() => scrollToBottom()).observe($el, { childList: true, subtree: true });
        "
        @messages-loaded.window="scrollToBottom()"
    >
        @forelse($messages as $message)
            @php
                $isMe = $message['user_id'] === auth()->id();
            @endphp
            
            <div wire:key="msg-{{ $message['id'] ?? uniqid() }}" class="flex {{ $isMe ? 'justify-end' : 'justify-start' }}">
                <div class="flex items-end gap-1.5 {{ $isMe ? 'flex-row-reverse' : '' }} max-w-[85%]">
                    {{-- Avatar --}}
                    <div class="flex-shrink-0 w-7 h-7 rounded-full {{ $isMe ? 'bg-violet-600' : 'bg-gray-500' }} flex items-center justify-center text-white text-xs font-semibold">
                        {{ strtoupper(substr($message['user']['name'] ?? 'U', 0, 1)) }}
                    </div>
                    
                    {{-- Message Bubble --}}
                    <div class="flex flex-col {{ $isMe ? 'items-end' : 'items-start' }}">
                        <div class="rounded-2xl px-3 py-2 {{ $isMe ? 'bg-violet-600 text-white rounded-br-sm' : 'bg-white text-gray-800 border border-gray-200 rounded-bl-sm' }}">
                            @if(!empty($message['message']))
                                <p class="text-sm leading-relaxed whitespace-pre-wrap break-words" style="word-break: break-word; overflow-wrap: anywhere;">{{ $message['message'] }}</p>
                            @endif
                            
                            @if(!empty($message['file_path']))
                                <div class="mt-1.5 {{ !empty($message['message']) ? 'pt-1.5 border-t ' : '' }} {{ $isMe ? 'border-white/20' : 'border-gray-200' }}">
                                    @php
                                        $fileUrl = \Storage::url($message['file_path']);
                                        $isImage = !empty($message['file_type']) && str_starts_with($message['file_type'], 'image/');
                                    @endphp
                                    
                                    @if($isImage)
                                        <a href="{{ $fileUrl }}" target="_blank" class="block rounded-lg overflow-hidden">
                                            <img src="{{ $fileUrl }}" alt="Image" class="max-w-full h-auto max-h-32 object-contain">
                                        </a>
                                    @else
                                        <a href="{{ $fileUrl }}" download class="flex items-center gap-1.5 text-xs {{ $isMe ? 'text-white' : 'text-gray-600' }}">
                                            <span>📎</span>
                                            <span class="truncate">{{ $message['file_name'] ?? 'File' }}</span>
                                        </a>
                                    @endif
                                </div>
                            @endif
                        </div>
                        
                        <span class="text-xs text-gray-500 mt-0.5 px-1">
                            {{ \Carbon\Carbon::parse($message['created_at'])->timezone('Asia/Jakarta')->format('H:i') }}
                            @if($isMe && !empty($message['is_read']))
                                <span class="text-violet-600">✓✓</span>
                            @endif
                        </span>
                    </div>
                </div>
            </div>
        @empty
            <div class="flex flex-col items-center justify-center h-full py-12 text-center">
                <div class="text-5xl mb-3 opacity-40">💬</div>
                <p class="text-sm text-gray-500">Belum ada pesan</p>
                <p class="text-xs text-gray-400">Mulai percakapan</p>
            </div>
        @endforelse

        {{-- Optimistic Sending Bubbles Queue --}}
        <template x-for="optMsg in optimisticMessages" :key="optMsg.id">
            <div class="flex justify-end mt-2">
                <div class="flex items-end gap-1.5 flex-row-reverse max-w-[85%]">
                    {{-- Avatar --}}
                    <div class="flex-shrink-0 w-7 h-7 rounded-full bg-violet-600 flex items-center justify-center text-white text-xs font-semibold">
                        {{ strtoupper(substr(auth()->user()->name ?? 'U', 0, 1)) }}
                    </div>
                    
                    {{-- Message Bubble --}}
                    <div class="flex flex-col items-end">
                        <div class="rounded-2xl px-3 py-2 bg-violet-600/70 text-white rounded-br-sm shadow-sm animate-pulse">
                            <p class="text-sm leading-relaxed whitespace-pre-wrap break-words" x-text="optMsg.text"></p>
                        </div>
                        <span class="text-xs text-gray-400 mt-0.5 px-1">Mengirim...</span>
                    </div>
                </div>
            </div>
        </template>

        {{-- Admin Typing Indicator --}}
        <div x-show="adminIsTyping" x-transition style="display:none;" class="flex justify-start">
            <div class="flex items-end gap-1.5">
                <div class="flex-shrink-0 w-7 h-7 rounded-full bg-gray-500 flex items-center justify-center text-white text-xs font-semibold">A</div>
                <div class="bg-white border border-gray-200 rounded-2xl rounded-bl-sm px-3 py-2 shadow-sm">
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
    <div class="flex-shrink-0 bg-white border-t border-gray-200 p-3">
        {{-- File Preview --}}
        @if($file)
            <div class="mb-2 flex items-center gap-2 p-2 bg-violet-50 border border-violet-200 rounded-lg">
                <span class="text-lg">📄</span>
                <div class="flex-1 min-w-0">
                    <p class="text-xs font-medium text-gray-800 truncate">{{ $file->getClientOriginalName() }}</p>
                    <p class="text-xs text-gray-500">{{ number_format($file->getSize() / 1024, 1) }} KB</p>
                </div>
                <button 
                    type="button"
                    wire:click="removeFile"
                    class="p-1 hover:bg-red-100 rounded transition-colors"
                >
                    <span class="text-sm">❌</span>
                </button>
            </div>
        @endif

        {{-- Error --}}
        @error('newMessage')
            <div wire:key="error-new-message" class="mb-2 p-2 bg-red-50 border border-red-200 rounded-lg">
                <p class="text-xs text-red-700">{{ $message }}</p>
            </div>
        @enderror

        {{-- Input Form --}}
        <div class="flex items-end gap-2">
            <div class="flex-1">
                <textarea 
                    id="task-message-input-{{ $userTask->id }}"
                    wire:model="newMessage" 
                    placeholder="Ketik pesan..."
                    rows="1"
                    class="w-full px-3 py-2 text-sm rounded-lg border border-gray-300 bg-white text-gray-800 placeholder-gray-400 focus:border-violet-500 focus:ring-1 focus:ring-violet-500 resize-none"
                    style="min-height: 38px; max-height: 100px;"
                    x-data="{ 
                        resize() { 
                            $el.style.height = 'auto'; 
                            $el.style.height = Math.min($el.scrollHeight, 100) + 'px'; 
                        } 
                    }"
                    x-init="resize()"
                    @input="resize(); if(echoChannel) echoChannel.whisper('typing', { name: '{{ auth()->user()->name }}' })"
                    @keydown.enter.prevent="if(!$event.shiftKey) { submitMessage(); resize(); }"
                ></textarea>
            </div>

            <button 
                type="button"
                @click="submitMessage()"
                class="flex-shrink-0 p-2 bg-violet-600 hover:bg-violet-700 text-white rounded-lg shadow hover:shadow-md transition-all disabled:opacity-50 disabled:cursor-not-allowed"
            >
                <svg class="w-5 h-5 transform rotate-90" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path></svg>
            </button>
        </div>

        <p class="text-xs text-gray-400 mt-1.5 text-center">
            Enter = kirim • Shift+Enter = baris baru
        </p>
    </div>
</div>

