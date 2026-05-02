<!DOCTYPE html>
<html lang="<?php echo e(str_replace('_', '-', app()->getLocale())); ?>">

<head>
    <?php echo $__env->make('partials.head', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">

    <!-- PWA Meta Tags - Purple Theme -->
    <link rel="manifest" href="<?php echo e(asset('manifest.json')); ?>">
    <meta name="theme-color" content="#7c3aed">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">
    <meta name="apple-mobile-web-app-title" content="<?php echo e(config('app.name')); ?>">
</head>

<body class="min-h-screen bg-gradient-to-br from-slate-50 via-violet-50/30 to-purple-50/20">
    <!-- Header - Modern Purple Theme -->
    <header class="sticky top-0 z-40 bg-white/80 backdrop-blur-xl border-b border-violet-100/50">
        <div class="flex items-center justify-between px-4 py-3 max-w-lg mx-auto">
            <!-- Logo -->
            <a href="<?php echo e(route('dashboard')); ?>" class="flex items-center space-x-2" wire:navigate>
                <?php if (isset($component)) { $__componentOriginal7b17d80ff7900603fe9e5f0b453cc7c3 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal7b17d80ff7900603fe9e5f0b453cc7c3 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.app-logo','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('app-logo'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal7b17d80ff7900603fe9e5f0b453cc7c3)): ?>
<?php $attributes = $__attributesOriginal7b17d80ff7900603fe9e5f0b453cc7c3; ?>
<?php unset($__attributesOriginal7b17d80ff7900603fe9e5f0b453cc7c3); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal7b17d80ff7900603fe9e5f0b453cc7c3)): ?>
<?php $component = $__componentOriginal7b17d80ff7900603fe9e5f0b453cc7c3; ?>
<?php unset($__componentOriginal7b17d80ff7900603fe9e5f0b453cc7c3); ?>
<?php endif; ?>
            </a>

            <!-- User Info Badge -->
            <?php if(auth()->guard()->check()): ?>
                <div class="flex items-center gap-3">
                    <!-- Notification Panel -->
                    <?php
$__split = function ($name, $params = []) {
    return [$name, $params];
};
[$__name, $__params] = $__split('notification-panel', []);

$__html = app('livewire')->mount($__name, $__params, 'lw-3995290707-0', $__slots ?? [], get_defined_vars());

echo $__html;

unset($__html);
unset($__name);
unset($__params);
unset($__split);
if (isset($__slots)) unset($__slots);
?>

                    <!-- User Avatar -->
                    <a href="<?php echo e(route('settings.profile')); ?>" wire:navigate
                        class="flex items-center gap-2 py-1 pl-1 pr-3 rounded-full bg-violet-50 hover:bg-violet-100 transition-colors">
                        <span
                            class="flex h-8 w-8 shrink-0 overflow-hidden rounded-full bg-gradient-to-br from-violet-500 to-purple-600 text-white items-center justify-center text-sm font-semibold shadow-md">
                            <?php echo e(optional(auth()->user())->initials()); ?>

                        </span>
                        <span
                            class="text-sm font-medium text-zinc-700 hidden sm:block"><?php echo e(Str::words(optional(auth()->user())->name, 1, '')); ?></span>
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </header>

    <!-- Main Content with padding for bottom nav -->
    <main class="pb-28 min-h-[calc(100vh-64px)]">
        <?php echo e($slot); ?>

    </main>

    <!-- Bottom Navigation -->
    <?php if (isset($component)) { $__componentOriginala63d75e61bed68e54e4eb96168fa8146 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginala63d75e61bed68e54e4eb96168fa8146 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.bottom-navigation','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('bottom-navigation'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginala63d75e61bed68e54e4eb96168fa8146)): ?>
<?php $attributes = $__attributesOriginala63d75e61bed68e54e4eb96168fa8146; ?>
<?php unset($__attributesOriginala63d75e61bed68e54e4eb96168fa8146); ?>
<?php endif; ?>
<?php if (isset($__componentOriginala63d75e61bed68e54e4eb96168fa8146)): ?>
<?php $component = $__componentOriginala63d75e61bed68e54e4eb96168fa8146; ?>
<?php unset($__componentOriginala63d75e61bed68e54e4eb96168fa8146); ?>
<?php endif; ?>
    </div><!-- End Mobile-First Container -->

    <?php if(session('shouldRefresh')): ?>
        <script>
            if (!localStorage.getItem('justRefreshed')) {
                localStorage.setItem('justRefreshed', '1');
                location.reload();
            } else {
                localStorage.removeItem('justRefreshed');
            }
        </script>
    <?php endif; ?>

    <?php app('livewire')->forceAssetInjection(); ?>
<?php echo app('flux')->scripts(); ?>


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

</html><?php /**PATH C:\laragon\www\baru\resources\views/components/layouts/app/user.blade.php ENDPATH**/ ?>