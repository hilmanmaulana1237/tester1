<?php if (isset($component)) { $__componentOriginalb525200bfa976483b4eaa0b7685c6e24 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalb525200bfa976483b4eaa0b7685c6e24 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'filament-widgets::components.widget','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('filament-widgets::widget'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
    <?php if (isset($component)) { $__componentOriginalee08b1367eba38734199cf7829b1d1e9 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalee08b1367eba38734199cf7829b1d1e9 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'filament::components.section.index','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('filament::section'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
         <?php $__env->slot('heading', null, []); ?> 
            <div class="flex items-center gap-2">
                <span class="text-base">🔔</span>
                <span class="font-semibold">Aktivitas Terbaru</span>
            </div>
         <?php $__env->endSlot(); ?>
        <div class="space-y-2 max-h-80 overflow-y-auto">
            <!--[if BLOCK]><![endif]--><?php $__empty_1 = true; $__currentLoopData = $this->getActivities(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $activity): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <div class="flex items-start gap-2 p-2 rounded-lg bg-gray-50 dark:bg-white/5">
                    <div class="<?php echo \Illuminate\Support\Arr::toCssClasses([
                        'flex-shrink-0 w-6 h-6 rounded-full flex items-center justify-center text-xs',
                        'bg-primary-50 text-primary-600 dark:bg-primary-400/10 dark:text-primary-400' => $activity['color'] === 'primary',
                        'bg-success-50 text-success-600 dark:bg-success-400/10 dark:text-success-400' => $activity['color'] === 'success',
                        'bg-warning-50 text-warning-600 dark:bg-warning-400/10 dark:text-warning-400' => $activity['color'] === 'warning',
                        'bg-danger-50 text-danger-600 dark:bg-danger-400/10 dark:text-danger-400' => $activity['color'] === 'danger',
                        'bg-info-50 text-info-600 dark:bg-info-400/10 dark:text-info-400' => $activity['color'] === 'info',
                    ]); ?>">
                        <?php
                            $emoji = match($activity['color']) {
                                'primary' => '🔔',
                                'success' => '✅',
                                'warning' => '⚠️',
                                'danger' => '❌',
                                'info' => 'ℹ️',
                                default => '🔔',
                            };
                        ?>
                        <span aria-hidden="true"><?php echo e($emoji); ?></span>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-xs text-gray-700 dark:text-gray-300 line-clamp-1">
                            <?php echo e($activity['message']); ?>

                        </p>
                        <p class="text-[10px] text-gray-500 dark:text-gray-500">
                            <?php echo e(\Carbon\Carbon::parse($activity['time'])->diffForHumans()); ?>

                        </p>
                    </div>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <div class="text-center py-6 text-gray-400">
                    <div class="mx-auto mb-2 text-2xl opacity-50">📭</div>
                    <p class="text-xs">Belum ada aktivitas</p>
                </div>
            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
        </div>
     <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalee08b1367eba38734199cf7829b1d1e9)): ?>
<?php $attributes = $__attributesOriginalee08b1367eba38734199cf7829b1d1e9; ?>
<?php unset($__attributesOriginalee08b1367eba38734199cf7829b1d1e9); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalee08b1367eba38734199cf7829b1d1e9)): ?>
<?php $component = $__componentOriginalee08b1367eba38734199cf7829b1d1e9; ?>
<?php unset($__componentOriginalee08b1367eba38734199cf7829b1d1e9); ?>
<?php endif; ?>
 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalb525200bfa976483b4eaa0b7685c6e24)): ?>
<?php $attributes = $__attributesOriginalb525200bfa976483b4eaa0b7685c6e24; ?>
<?php unset($__attributesOriginalb525200bfa976483b4eaa0b7685c6e24); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalb525200bfa976483b4eaa0b7685c6e24)): ?>
<?php $component = $__componentOriginalb525200bfa976483b4eaa0b7685c6e24; ?>
<?php unset($__componentOriginalb525200bfa976483b4eaa0b7685c6e24); ?>
<?php endif; ?>
<?php /**PATH C:\laragon\www\baru\resources\views/filament/widgets/recent-activities-widget.blade.php ENDPATH**/ ?>