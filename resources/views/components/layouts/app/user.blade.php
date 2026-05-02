<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    @include('partials.head')
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- PWA Meta Tags - Purple Theme -->
    <link rel="manifest" href="{{ asset('manifest.json') }}">
    <meta name="theme-color" content="#7c3aed">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">
    <meta name="apple-mobile-web-app-title" content="{{ config('app.name') }}">
</head>

<body class="min-h-screen bg-gradient-to-br from-slate-50 via-violet-50/30 to-purple-50/20">
    <!-- Header - Modern Purple Theme -->
    <header class="sticky top-0 z-40 bg-white/80 backdrop-blur-xl border-b border-violet-100/50">
        <div class="flex items-center justify-between px-4 py-3 max-w-lg mx-auto">
            <!-- Logo -->
            <a href="{{ route('dashboard') }}" class="flex items-center space-x-2" wire:navigate>
                <x-app-logo />
            </a>

            <!-- User Info Badge -->
            @auth
                <div class="flex items-center gap-3">
                    <!-- Notification Panel -->
                    <livewire:notification-panel />

                    <!-- User Avatar -->
                    <a href="{{ route('settings.profile') }}" wire:navigate
                        class="flex items-center gap-2 py-1 pl-1 pr-3 rounded-full bg-violet-50 hover:bg-violet-100 transition-colors">
                        <span
                            class="flex h-8 w-8 shrink-0 overflow-hidden rounded-full bg-gradient-to-br from-violet-500 to-purple-600 text-white items-center justify-center text-sm font-semibold shadow-md">
                            {{ optional(auth()->user())->initials() }}
                        </span>
                        <span
                            class="text-sm font-medium text-zinc-700 hidden sm:block">{{ Str::words(optional(auth()->user())->name, 1, '') }}</span>
                    </a>
                </div>
            @endauth
        </div>
    </header>

    <!-- Main Content with padding for bottom nav -->
    <main class="pb-28 min-h-[calc(100vh-64px)]">
        {{ $slot }}
    </main>

    <!-- Bottom Navigation -->
    <x-bottom-navigation />
    </div><!-- End Mobile-First Container -->

    @if (session('shouldRefresh'))
        <script>
            if (!localStorage.getItem('justRefreshed')) {
                localStorage.setItem('justRefreshed', '1');
                location.reload();
            } else {
                localStorage.removeItem('justRefreshed');
            }
        </script>
    @endif

    @fluxScripts

    <!-- Register Service Worker for PWA -->
    <script>
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', () => {
                navigator.serviceWorker.register('/sw.js')
                    .then(registration => {
                        console.log('SW registered:', registration.scope);
                    })
                    .catch(error => {
                        console.log('SW registration failed:', error);
                    });
            });
        }
    </script>
</body>

</html>