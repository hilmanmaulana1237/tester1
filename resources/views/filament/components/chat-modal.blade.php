@php
    $userTask = $userTask ?? null;
@endphp

{{-- Clean chat modal for Filament --}}
<div class="-mx-6 -mt-6">

    {{-- Quick Reply Templates --}}
    <div class="bg-gradient-to-r from-blue-50 to-indigo-50 dark:from-gray-800 dark:to-gray-900 border-b border-gray-200 dark:border-gray-700 px-4 py-3">
        <div class="flex items-center gap-2 mb-2">
            <svg class="w-4 h-4 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
            </svg>
            <span class="text-xs font-semibold text-gray-700 dark:text-gray-300">Template Pesan Cepat:</span>
        </div>
        <div class="flex flex-wrap gap-2">
            <button type="button" 
                onclick="sendQuickReply{{ $userTask->id }}('Silahkan membatalkan task ya kak')"
                class="inline-flex items-center gap-1 px-3 py-1.5 bg-white dark:bg-gray-700 text-xs font-medium text-gray-700 dark:text-gray-200 rounded-lg border border-gray-300 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-600 transition-colors shadow-sm">
                <span>✅</span>
                <span>Batalkan Task</span>
            </button>
            <button type="button" 
                onclick="sendQuickReply{{ $userTask->id }}('Mohon untuk submit ulang bukti yang benar')"
                class="inline-flex items-center gap-1 px-3 py-1.5 bg-white dark:bg-gray-700 text-xs font-medium text-gray-700 dark:text-gray-200 rounded-lg border border-gray-300 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-600 transition-colors shadow-sm">
                <span>🔄</span>
                <span>Submit Ulang</span>
            </button>
            <button type="button" 
                onclick="sendQuickReply{{ $userTask->id }}('Bukti yang dikirim kurang lengkap, mohon lengkapi terlebih dahulu sebelum submit.')"
                class="inline-flex items-center gap-1 px-3 py-1.5 bg-white dark:bg-gray-700 text-xs font-medium text-gray-700 dark:text-gray-200 rounded-lg border border-gray-300 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-600 transition-colors shadow-sm">
                <span>⚠️</span>
                <span>Kurang Lengkap</span>
            </button>
            <button type="button" 
                onclick="sendQuickReply{{ $userTask->id }}('Format bukti tidak sesuai dengan ketentuan, silakan perbaiki dan kirim ulang.')"
                class="inline-flex items-center gap-1 px-3 py-1.5 bg-white dark:bg-gray-700 text-xs font-medium text-gray-700 dark:text-gray-200 rounded-lg border border-gray-300 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-600 transition-colors shadow-sm">
                <span>❌</span>
                <span>Format Salah</span>
            </button>
            <button type="button" 
                onclick="sendQuickReply{{ $userTask->id }}('Bukti sudah bagus, sedang dalam proses review. Mohon ditunggu ya.')"
                class="inline-flex items-center gap-1 px-3 py-1.5 bg-white dark:bg-gray-700 text-xs font-medium text-gray-700 dark:text-gray-200 rounded-lg border border-gray-300 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-600 transition-colors shadow-sm">
                <span>👍</span>
                <span>Sudah Bagus</span>
            </button>
            <button type="button" 
                onclick="sendQuickReply{{ $userTask->id }}('Selamat! Bukti Anda sudah disetujui. Pembayaran akan segera diproses.')"
                class="inline-flex items-center gap-1 px-3 py-1.5 bg-white dark:bg-gray-700 text-xs font-medium text-gray-700 dark:text-gray-200 rounded-lg border border-gray-300 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-600 transition-colors shadow-sm">
                <span>✨</span>
                <span>Disetujui</span>
            </button>
            <button type="button" 
                onclick="sendQuickReply{{ $userTask->id }}('Mohon tunggu, bukti Anda sedang direview oleh admin. Terima kasih atas kesabarannya.')"
                class="inline-flex items-center gap-1 px-3 py-1.5 bg-white dark:bg-gray-700 text-xs font-medium text-gray-700 dark:text-gray-200 rounded-lg border border-gray-300 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-600 transition-colors shadow-sm">
                <span>⏳</span>
                <span>Mohon Tunggu</span>
            </button>
        </div>
    </div>

    {{-- Chat Component with Fixed Height --}}
    <div class="bg-white dark:bg-gray-800" style="height: 520px;">
        @livewire('task-chat', ['userTaskId' => $userTask->id], key('modal-chat-' . $userTask->id))
    </div>
</div>

{{-- Quick Reply Function --}}
<script>
function sendQuickReply{{ $userTask->id }}(message) {
    // Find the chat component
    const chatEl = document.getElementById('chat-messages-{{ $userTask->id }}');
    if (!chatEl) {
        console.error('Chat element not found');
        return;
    }
    
    const rootEl = chatEl.closest('[wire\\:id]');
    if (!rootEl) {
        console.error('Livewire root element not found');
        return;
    }
    
    const wireId = rootEl.getAttribute('wire:id');
    const component = window.Livewire?.find(wireId);
    
    if (!component) {
        console.error('Livewire component not found');
        return;
    }
    
    // Set message and send
    component.set('newMessage', message);
    
    // Wait a bit then send
    setTimeout(() => {
        component.call('sendMessage');
    }, 100);
}
</script>

{{-- Echo setup untuk Filament modal --}}
<script>
console.log('[Filament INIT] Script executing immediately');

// Execute immediately when script loads
(function() {
    const channelName = 'chat.{{ $userTask->id }}';
    const componentKey = 'modal-chat-{{ $userTask->id }}';
    
    console.log('[Filament] Script loaded! Channel:', channelName);
    
    let retries = 0;
    const maxRetries = 50;
    
    function trySubscribe() {
        console.log('[Filament] Attempt', retries + 1, '- Checking Echo...');
        
        if (typeof window.Echo === 'undefined') {
            retries++;
            if (retries < maxRetries) {
                setTimeout(trySubscribe, 200);
            } else {
                console.error('[Filament] Echo NEVER loaded after', maxRetries, 'attempts!');
                console.log('[Filament] window.Echo:', typeof window.Echo);
                console.log('[Filament] window keys:', Object.keys(window).filter(k => k.includes('cho')));
            }
            return;
        }
        
        console.log('[Filament] ✅ Echo FOUND! Config:', {
            broadcaster: window.Echo.connector?.options?.broadcaster,
            host: window.Echo.connector?.options?.wsHost,
            port: window.Echo.connector?.options?.wsPort,
        });
        
        // Subscribe
        const channel = window.Echo.channel(channelName);
        console.log('[Filament] Channel object created:', channel);

        // Ensure initial scroll if element already present
        const initialEl = document.getElementById('chat-messages-{{ $userTask->id }}');
        if (initialEl) {
            initialEl.scrollTop = initialEl.scrollHeight;
            console.log('[Filament] Initial scroll applied:', initialEl.scrollTop);
        }
        
        channel.listen('MessageSent', function(e) {
            console.log('[Filament] 📨 MESSAGE RECEIVED:', e);
            
            // Find Livewire component
            setTimeout(function() {
                const el = document.getElementById('chat-messages-{{ $userTask->id }}');
                console.log('[Filament] Looking for messages element:', el);
                const rootEl = el ? el.closest('[wire\\:id]') : null;
                console.log('[Filament] Root element:', rootEl);
                console.log('[Filament] Looking for element with key:', componentKey, 'Found:', el);
                
                if (rootEl && typeof Livewire !== 'undefined') {
                    const wireId = rootEl.getAttribute('wire:id');
                    const component = Livewire.find(wireId);
                    
                    console.log('[Filament] Livewire component:', component);
                    
                        if (component) {
                            component.call('messageReceived', e);
                            console.log('[Filament] ✅ Called messageReceived');
                            // scroll to bottom
                            if (el) el.scrollTop = el.scrollHeight;
                        }
                }
            }, 100);
        });
        
        console.log('[Filament] ✅ Subscribed! State:', window.Echo.connector?.pusher?.connection?.state);
        
        // Monitor state changes
        if (window.Echo.connector?.pusher?.connection) {
            window.Echo.connector.pusher.connection.bind('state_change', function(states) {
                console.log('[Filament] 🔄 State:', states.previous, '->', states.current);
            });
        }
    }
    
    // Start immediately
    console.log('[Filament] Starting subscribe attempts...');
    trySubscribe();
})();
</script>
