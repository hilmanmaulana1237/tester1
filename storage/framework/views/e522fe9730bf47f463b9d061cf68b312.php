<meta charset="utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">

<title><?php echo e($title ?? config('app.name')); ?></title>

<link rel="icon" href="/favicon.ico" sizes="any">
<link rel="icon" href="/favicon.svg" type="image/svg+xml">
<link rel="apple-touch-icon" href="/apple-touch-icon.png">


<link rel="preconnect" href="https://fonts.bunny.net">
<link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />

<?php echo app('Illuminate\Foundation\Vite')(['resources/css/app.css', 'resources/js/app.js']); ?>


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


<link rel="manifest" href="<?php echo e(asset('manifest.json')); ?>">
<meta name="theme-color" content="#7c3aed">
<meta name="mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-status-bar-style" content="default">
<meta name="apple-mobile-web-app-title" content="<?php echo e(config('app.name')); ?>">

<?php echo \Livewire\Mechanisms\FrontendAssets\FrontendAssets::scripts(); ?><?php /**PATH C:\laragon\www\baru\resources\views/partials/head.blade.php ENDPATH**/ ?>