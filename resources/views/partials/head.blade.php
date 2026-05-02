<meta charset="utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<meta name="csrf-token" content="{{ csrf_token() }}">

<title>{{ $title ?? config('app.name') }}</title>

<link rel="icon" href="/favicon.ico" sizes="any">
<link rel="icon" href="/favicon.svg" type="image/svg+xml">
<link rel="apple-touch-icon" href="/apple-touch-icon.png">


<link rel="preconnect" href="https://fonts.bunny.net">
<link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />

@vite(['resources/css/app.css', 'resources/js/app.js'])

{{-- Ensure Echo loaded before Livewire for real-time chat --}}
<script>
    // Global retry helper untuk Echo
    window.waitForEcho = function (callback, maxRetries = 20) {
        let retries = 0;
        const interval = setInterval(() => {
            if (typeof window.Echo !== 'undefined') {
                clearInterval(interval);
                callback();
            } else if (++retries >= maxRetries) {
                clearInterval(interval);
                console.error('Echo failed to load after', maxRetries, 'retries');
            }
        }, 200);
    };
</script>

{{-- PWA Support --}}
<link rel="manifest" href="{{ asset('manifest.json') }}">
<meta name="theme-color" content="#7c3aed">
<meta name="mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-status-bar-style" content="default">
<meta name="apple-mobile-web-app-title" content="{{ config('app.name') }}">

@livewireScripts