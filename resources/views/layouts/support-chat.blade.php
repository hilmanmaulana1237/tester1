<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    @include('partials.head')
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body class="h-screen overflow-hidden bg-gray-100">
    {{ $slot }}

    @fluxScripts

    {{-- Force Service Worker update and clear stale caches on admin pages --}}
    <script>
        (function() {
            if (!('serviceWorker' in navigator)) return;

            // Force update any registered SW so the new v2 sw.js takes over
            navigator.serviceWorker.getRegistrations().then(registrations => {
                registrations.forEach(reg => {
                    reg.update(); // fetch newest sw.js
                    if (reg.waiting) {
                        reg.waiting.postMessage({ type: 'SKIP_WAITING' });
                    }
                });
            });

            // Also clear all old caches to prevent stale content
            if ('caches' in window) {
                caches.keys().then(names => {
                    names.forEach(name => {
                        if (name !== 'taskapp-v2') {
                            caches.delete(name);
                            console.log('[Admin] Deleted stale cache:', name);
                        }
                    });
                });
            }
        })();
    </script>
</body>
</html>
