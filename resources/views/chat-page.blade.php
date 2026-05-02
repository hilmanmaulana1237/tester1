<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Chat: {{ $userTask->user->name }}</title>
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="bg-gray-100 dark:bg-gray-900">
    <div class="min-h-screen p-6">
        <div class="max-w-5xl mx-auto">
            <!-- Header -->
            <div class="bg-white dark:bg-gray-800 rounded-t-2xl shadow-lg p-6 border-b-2 border-gray-200 dark:border-gray-700">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900 dark:text-white flex items-center gap-3">
                            💬 Chat dengan {{ $userTask->user->name }}
                        </h1>
                        <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                            Task: {{ $userTask->task->title }}
                        </p>
                    </div>
                    <a href="{{ url()->previous() }}" 
                       class="px-4 py-2 bg-gray-200 dark:bg-gray-700 hover:bg-gray-300 dark:hover:bg-gray-600 rounded-lg transition-colors text-gray-700 dark:text-gray-300 font-medium">
                        ← Kembali
                    </a>
                </div>
            </div>
            
            <!-- Chat Component -->
            <div class="bg-white dark:bg-gray-800 rounded-b-2xl shadow-2xl" style="height: 600px;">
                @livewire('task-chat', ['userTaskId' => $userTask->id], key('user-chat-'.$userTask->id))
            </div>
        </div>
    </div>
    
    @livewireScripts
    
    {{-- Echo setup - EXACT COPY from Filament --}}
    <script>
    console.log('[User Chat INIT] Script executing immediately');

    (function() {
        const channelName = 'chat.{{ $userTask->id }}';
        const componentKey = 'user-chat-{{ $userTask->id }}';
        
        console.log('[User Chat] Script loaded! Channel:', channelName);
        
        let retries = 0;
        const maxRetries = 50;
        
        function trySubscribe() {
            console.log('[User Chat] Attempt', retries + 1, '- Checking Echo...');
            
            if (typeof window.Echo === 'undefined') {
                retries++;
                if (retries < maxRetries) {
                    setTimeout(trySubscribe, 200);
                } else {
                    console.error('[User Chat] Echo NEVER loaded after', maxRetries, 'attempts!');
                }
                return;
            }
            
            console.log('[User Chat] ✅ Echo FOUND! Config:', {
                broadcaster: window.Echo.connector?.options?.broadcaster,
                host: window.Echo.connector?.options?.wsHost,
                port: window.Echo.connector?.options?.wsPort,
            });
            
            // Subscribe
            const channel = window.Echo.channel(channelName);
            console.log('[User Chat] Channel object created:', channel);
            console.log('[User Chat] Channel name being used:', channel.name);
            console.log('[User Chat] Pusher channel:', window.Echo.connector.pusher.channel(channelName));

            // Ensure initial scroll if element already present
            const initialEl = document.getElementById('chat-messages-{{ $userTask->id }}');
            if (initialEl) {
                initialEl.scrollTop = initialEl.scrollHeight;
                console.log('[User Chat] Initial scroll applied:', initialEl.scrollTop);
            }
            
            // Debug: Bind to ALL events on Pusher channel directly
            const pusherChannel = window.Echo.connector.pusher.channel(channelName);
            if (pusherChannel) {
                pusherChannel.bind_global(function(eventName, data) {
                    console.log('[User Chat] 🔔 ANY EVENT:', eventName, data);
                });
            }
            
            // Test: Log ALL events on this channel
            console.log('[User Chat] Setting up listener for MessageSent event...');
            
            channel.listen('.MessageSent', function(e) {
                console.log('[User Chat] 📨 MESSAGE RECEIVED (with dot):', e);
            });
            
            channel.listen('MessageSent', function(e) {
                console.log('[User Chat] 📨 MESSAGE RECEIVED (no dot):', e);
                
                // Find Livewire component
                setTimeout(function() {
                    const el = document.getElementById('chat-messages-{{ $userTask->id }}');
                    console.log('[User Chat] Looking for messages element:', el);
                    const rootEl = el ? el.closest('[wire\\:id]') : null;
                    console.log('[User Chat] Root element:', rootEl);
                    console.log('[User Chat] Looking for element with key:', componentKey, 'Found:', el);
                    
                    if (rootEl && typeof Livewire !== 'undefined') {
                        const wireId = rootEl.getAttribute('wire:id');
                        const component = Livewire.find(wireId);
                        
                        console.log('[User Chat] Livewire component:', component);
                        console.log('[User Chat] Event data being sent:', e);
                        
                            if (component) {
                                console.log('[User Chat] About to call messageReceived...');
                                component.call('messageReceived', e);
                                console.log('[User Chat] ✅ Called messageReceived');
                                // scroll to bottom
                                setTimeout(() => {
                                    if (el) {
                                        el.scrollTop = el.scrollHeight;
                                        console.log('[User Chat] Scrolled to bottom');
                                    }
                                }, 200);
                            } else {
                                console.error('[User Chat] ❌ Component not found!');
                            }
                    } else {
                        console.error('[User Chat] ❌ Missing requirements:', {
                            rootEl: !!rootEl,
                            Livewire: typeof Livewire !== 'undefined'
                        });
                    }
                }, 100);
            });
            
            console.log('[User Chat] ✅ Subscribed! State:', window.Echo.connector?.pusher?.connection?.state);
            
            // Monitor state changes
            if (window.Echo.connector?.pusher?.connection) {
                window.Echo.connector.pusher.connection.bind('state_change', function(states) {
                    console.log('[User Chat] 🔄 State:', states.previous, '->', states.current);
                });
            }
        }
        
        // Start immediately
        console.log('[User Chat] Starting subscribe attempts...');
        trySubscribe();
    })();
    </script>
</body>
</html>
