<div class="h-[calc(100vh-200px)] flex flex-col bg-gradient-to-br from-slate-50 via-blue-50 to-indigo-50 rounded-2xl shadow-2xl overflow-hidden"
    wire:poll.5s="refreshMessages">

    {{-- Messages Container --}}
    <div x-data="{
            scrollToBottom() {
                $nextTick(() => {
                    const container = $refs.messagesContainer;
                    if (container) {
                        container.scrollTop = container.scrollHeight;
                    }
                });
            }
        }" x-init="scrollToBottom()" @scroll-to-bottom.window="scrollToBottom()"
        class="flex-1 overflow-y-auto p-8 space-y-6" x-ref="messagesContainer">
        @if(count($messages) === 0)
            <div class="flex items-center justify-center h-full">
                <div class="text-center max-w-md">
                    <div class="mb-6">
                        <div
                            class="w-24 h-24 mx-auto bg-gradient-to-br from-blue-100 to-indigo-100 rounded-3xl flex items-center justify-center shadow-lg">
                            <span class="text-5xl">💬</span>
                        </div>
                    </div>
                    <h4 class="text-2xl font-bold text-gray-800 mb-3">Belum Ada Percakapan</h4>
                    <p class="text-gray-600 text-base leading-relaxed">
                        Mulai percakapan dengan <strong>{{ $userTask->user->name }}</strong> dengan mengirim pesan pertama
                    </p>
                </div>
            </div>
        @else
            @foreach($messages as $message)
                <div wire:key="msg-full-{{ $message->id ?? uniqid() }}"
                    class="flex {{ $message->sender_type === 'admin' ? 'justify-end' : 'justify-start' }} animate-fade-in">
                    <div
                        class="flex flex-col {{ $message->sender_type === 'admin' ? 'items-end' : 'items-start' }} max-w-[80%] gap-2">

                        {{-- Message Bubble --}}
                        <div class="group relative">
                            {{-- Sender & Time Badge --}}
                            <div
                                class="flex items-center gap-2 mb-1 {{ $message->sender_type === 'admin' ? 'flex-row-reverse' : '' }}">
                                <span
                                    class="text-xs font-semibold text-gray-700 bg-white/70 backdrop-blur-sm px-3 py-1 rounded-full shadow-sm border border-gray-200">
                                    {{ $message->sender_type === 'admin' ? 'Anda' : $userTask->user->name }}
                                </span>
                                <span class="text-xs text-gray-500">
                                    {{ $message->created_at->diffForHumans() }}
                                </span>
                            </div>

                            {{-- The Bubble --}}
                            <div class="{{ $message->sender_type === 'admin'
                    ? 'bg-gradient-to-br from-blue-600 via-blue-500 to-indigo-600 text-white shadow-xl shadow-blue-500/40'
                    : 'bg-white text-gray-900 shadow-xl border-2 border-gray-200' }}
                                        rounded-3xl px-6 py-4 transition-all duration-200 hover:shadow-2xl hover:scale-[1.01]">

                                {{-- Message Text --}}
                                <div class="text-[15px] leading-relaxed break-words whitespace-pre-wrap"
                                    style="word-break: break-word; overflow-wrap: anywhere;">
                                    {{ $message->message }}
                                </div>

                                {{-- File Attachment --}}
                                @if($message->file_path)
                                            <div
                                                class="mt-4 pt-4 border-t {{ $message->sender_type === 'admin' ? 'border-blue-400/30' : 'border-gray-300' }}">
                                                <a href="{{ Storage::url($message->file_path) }}" target="_blank"
                                                    class="inline-flex items-center gap-3 px-4 py-3 rounded-2xl font-medium text-sm transition-all hover:scale-105
                                                                   {{ $message->sender_type === 'admin'
                                    ? 'bg-white/20 hover:bg-white/30 backdrop-blur-sm'
                                    : 'bg-gradient-to-r from-gray-100 to-gray-200 hover:from-gray-200 hover:to-gray-300' }}">
                                                    <span class="text-2xl">📎</span>
                                                    <span class="truncate max-w-[300px]">{{ $message->file_name ?? 'Unduh file' }}</span>
                                                </a>
                                            </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        @endif
    </div>

    {{-- Input Area --}}
    <div class="border-t-2 border-gray-300 bg-white/90 backdrop-blur-xl p-6 shadow-2xl">
        <form wire:submit.prevent="sendMessage">
            <div class="flex gap-4 items-end">
                <div class="flex-1">
                    <textarea wire:model="newMessage" placeholder="💬 Ketik pesan Anda..." rows="2"
                        class="w-full px-6 py-4 text-base rounded-3xl border-2 border-gray-300 bg-white text-gray-900 placeholder-gray-500 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/30 resize-none transition-all shadow-md hover:shadow-lg font-medium"
                        wire:loading.attr="disabled" x-data="{}" @keydown.ctrl.enter="$wire.sendMessage()"
                        @input="$el.style.height = 'auto'; $el.style.height = Math.min($el.scrollHeight, 150) + 'px'"></textarea>
                    @error('newMessage')
                        <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span>
                    @enderror
                </div>

                <button type="submit" wire:loading.attr="disabled"
                    class="group relative overflow-hidden px-8 py-4 bg-gradient-to-r from-blue-600 via-blue-500 to-indigo-600 hover:from-blue-700 hover:via-blue-600 hover:to-indigo-700 text-white font-bold text-base rounded-3xl disabled:opacity-40 disabled:cursor-not-allowed transition-all duration-300 shadow-xl shadow-blue-500/50 hover:shadow-2xl hover:shadow-blue-600/60 hover:scale-105 active:scale-95 disabled:hover:scale-100 min-w-[140px] h-[64px]">
                    <span class="relative z-10 flex items-center justify-center gap-2.5">
                        <span wire:loading.remove class="flex items-center gap-2.5">
                            <span class="text-lg">✉️</span>
                            <span>Kirim</span>
                        </span>
                        <span wire:loading class="flex items-center gap-2.5">
                            <span
                                class="inline-block w-5 h-5 border-3 border-white/30 border-t-white rounded-full animate-spin"></span>
                            <span>Kirim...</span>
                        </span>
                    </span>

                    {{-- Shine effect --}}
                    <span
                        class="absolute inset-0 bg-gradient-to-r from-transparent via-white/20 to-transparent -translate-x-full group-hover:translate-x-full transition-transform duration-1000"></span>
                </button>
            </div>

            <div class="flex items-center justify-between mt-4">
                <div class="flex items-center gap-2.5 text-sm text-gray-600">
                    <span class="relative flex h-3 w-3">
                        <span
                            class="animate-ping absolute inline-flex h-full w-full rounded-full bg-green-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-3 w-3 bg-green-500"></span>
                    </span>
                    <span class="font-medium">Live • Diperbarui otomatis setiap 5 detik</span>
                </div>
                <div
                    class="flex items-center gap-2 text-xs text-gray-600 bg-gradient-to-r from-gray-100 to-gray-200 px-4 py-2 rounded-full shadow-sm">
                    <span>💡</span>
                    <span class="font-medium">Tekan</span>
                    <kbd
                        class="px-2.5 py-1 bg-white border-2 border-gray-300 rounded-lg text-xs font-bold shadow-sm">Ctrl</kbd>
                    <span>+</span>
                    <kbd
                        class="px-2.5 py-1 bg-white border-2 border-gray-300 rounded-lg text-xs font-bold shadow-sm">Enter</kbd>
                </div>
            </div>
        </form>
    </div>
</div>

<style>
    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(20px) scale(0.95);
        }

        to {
            opacity: 1;
            transform: translateY(0) scale(1);
        }
    }

    .animate-fade-in {
        animation: fadeIn 0.5s cubic-bezier(0.34, 1.56, 0.64, 1);
    }

    /* Custom Scrollbar */
    [x-ref="messagesContainer"]::-webkit-scrollbar {
        width: 12px;
    }

    [x-ref="messagesContainer"]::-webkit-scrollbar-track {
        background: transparent;
        margin: 8px 0;
    }

    [x-ref="messagesContainer"]::-webkit-scrollbar-thumb {
        background: linear-gradient(to bottom, #3b82f6, #6366f1);
        border-radius: 10px;
        border: 2px solid transparent;
        background-clip: padding-box;
    }

    [x-ref="messagesContainer"]::-webkit-scrollbar-thumb:hover {
        background: linear-gradient(to bottom, #2563eb, #4f46e5);
        background-clip: padding-box;
    }

    .dark [x-ref="messagesContainer"]::-webkit-scrollbar-thumb {
        background: linear-gradient(to bottom, #1e40af, #4338ca);
        background-clip: padding-box;
    }

    .dark [x-ref="messagesContainer"]::-webkit-scrollbar-thumb:hover {
        background: linear-gradient(to bottom, #1e3a8a, #3730a3);
        background-clip: padding-box;
    }
</style>