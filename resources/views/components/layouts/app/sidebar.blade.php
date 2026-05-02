<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    @include('partials.head')
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>

    <body class="min-h-screen bg-white">
        <flux:sidebar sticky stashable class="border-e border-white bg-white">
            <flux:sidebar.toggle class="lg:hidden" icon="x-mark" />

            <a href="{{ route('dashboard') }}" class="me-5 flex items-center space-x-2 rtl:space-x-reverse" wire:navigate>
                <x-app-logo />
            </a>

            <flux:navlist variant="outline">
                @php
                    // Get available tasks count with caching
                    // Show count of truly unused tasks (never taken by any user) and still active
                    $availableTasksCount = \App\Services\CacheService::remember(
                        'available_tasks_count',
                        function() {
                            return \App\Models\Task::active()->neverTaken()->count();
                        },
                        5 // Cache for 5 minutes
                    );

                    // Get user's active tasks count with caching - exclude expired tasks
                    $myActiveTasksCount = 0;
                    if (auth()->check()) {
                        $myActiveTasksCount = \App\Services\CacheService::remember(
                            \App\Services\CacheService::userKey(auth()->id(), 'my_active_tasks_count'),
                            function() {
                                return \App\Models\UserTask::where('user_id', auth()->id())
                                    // only active user task statuses
                                    ->active()
                                    // exclude user tasks where the user's own deadline has passed
                                    ->where(function($q) {
                                        $q->whereNull('deadline_at')->orWhere('deadline_at', '>', now());
                                    })
                                    // ensure the underlying Task is still active (not expired in admin/task scope)
                                    ->whereHas('task', function($q) { $q->active(); })
                                    ->count();
                            },
                            5 // Cache for 5 minutes
                        );
                    }
                @endphp
                
                <flux:navlist.group :heading="__('Platform')" class="grid">
                    <flux:navlist.item icon="home" :href="route('dashboard')" :current="request()->routeIs('dashboard')" wire:navigate>
                        {{ __('Dashboard') }}
                    </flux:navlist.item>
                </flux:navlist.group>

                <flux:navlist.group :heading="__('Task Management')" class="grid">
                    <flux:navlist.item icon="clipboard-document-list" :href="route('user.dashboard')" :badge="$availableTasksCount" :current="request()->routeIs('user.dashboard')" wire:navigate>
                        {{ __('Task') }}
                    </flux:navlist.item>
                    <flux:navlist.item icon="clipboard-document-check" :href="route('user.my-tasks')" :badge="$myActiveTasksCount" :current="request()->routeIs('user.my-tasks')" wire:navigate>
                        {{ __('My Tasks') }}
                    </flux:navlist.item>
                    <flux:navlist.item icon="clock" :href="route('user.history')" :current="request()->routeIs('user.history')" wire:navigate>
                        {{ __('History') }}
                    </flux:navlist.item>
                </flux:navlist.group>

                <flux:navlist.group :heading="__('Aturan dan Tips')" expandable class="grid">
                    @if(Route::has('pages.panduan-task'))
                    <flux:navlist.item icon="book-open" :href="route('pages.panduan-task')" :current="request()->routeIs('pages.panduan-task')" wire:navigate>
                        {{ __('Panduan Task') }}
                    </flux:navlist.item>
                    @endif
                    @if(Route::has('pages.tips-sukses'))
                    <flux:navlist.item icon="light-bulb" :href="route('pages.tips-sukses')" :current="request()->routeIs('pages.tips-sukses')" wire:navigate>
                        {{ __('Tips Sukses') }}
                    </flux:navlist.item>
                    @endif
                    @if(Route::has('pages.faq'))
                    <flux:navlist.item icon="question-mark-circle" :href="route('pages.faq')" :current="request()->routeIs('pages.faq')" wire:navigate>
                        {{ __('FAQ') }}
                    </flux:navlist.item>
                    @endif
                    @if(Route::has('pages.tutorial-page'))
                    <flux:navlist.item icon="question-mark-circle" :href="route('pages.tutorial-page')" :current="request()->routeIs('pages.tutorial-page')" wire:navigate>
                        {{ __('Tutorial') }}
                    </flux:navlist.item>
                    @endif
                </flux:navlist.group>
            </flux:navlist>

            <flux:spacer />

            @auth
            <!-- Desktop User Menu -->
            <flux:dropdown class="hidden lg:block" position="bottom" align="start">
                <flux:profile
                    :name="optional(auth()->user())->name"
                    :initials="optional(auth()->user())->initials()"
                    icon:trailing="chevrons-up-down"
                />

                <flux:menu class="w-[220px]">
                    <flux:menu.radio.group>
                        <div class="p-0 text-sm font-normal">
                            <div class="flex items-center gap-2 px-1 py-1.5 text-start text-sm">
                                <span class="relative flex h-8 w-8 shrink-0 overflow-hidden rounded-lg">
                                    <span class="flex h-full w-full items-center justify-center rounded-lg bg-neutral-200 text-black dark:bg-neutral-700 dark:text-white">
                                        {{ optional(auth()->user())->initials() }}
                                    </span>
                                </span>

                                <div class="grid flex-1 text-start text-sm leading-tight">
                                    <span class="truncate font-semibold">{{ optional(auth()->user())->name }}</span>
                                    <span class="truncate text-xs">{{ optional(auth()->user())->email }}</span>
                                </div>
                            </div>
                        </div>
                    </flux:menu.radio.group>

                    <flux:menu.separator />

                    <flux:menu.radio.group>
                        <flux:menu.item :href="route('settings.profile')" icon="cog" wire:navigate>{{ __('Settings') }}</flux:menu.item>
                    </flux:menu.radio.group>

                    <flux:menu.separator />

                    <form method="POST" action="{{ route('logout') }}" class="w-full">
                        @csrf
                        <flux:menu.item as="button" type="submit" icon="arrow-right-start-on-rectangle" class="w-full">
                            {{ __('Log Out') }}
                        </flux:menu.item>
                    </form>
                </flux:menu>
            </flux:dropdown>
            @endauth
        </flux:sidebar>

        <!-- Mobile User Menu -->
        @auth
        <flux:header class="lg:hidden">
            <flux:sidebar.toggle class="lg:hidden" icon="bars-2" inset="left" />
            <flux:spacer />

            <flux:dropdown position="top" align="end">
                <flux:profile
                    :initials="optional(auth()->user())->initials()"
                    icon-trailing="chevron-down"
                />
                <flux:menu>
                    <flux:menu.radio.group>
                        <div class="p-0 text-sm font-normal">
                            <div class="flex items-center gap-2 px-1 py-1.5 text-start text-sm">
                                <span class="relative flex h-8 w-8 shrink-0 overflow-hidden rounded-lg">
                                    <span class="flex h-full w-full items-center justify-center rounded-lg bg-neutral-200 text-black dark:bg-neutral-700 dark:text-white">
                                        {{ optional(auth()->user())->initials() }}
                                    </span>
                                </span>

                                <div class="grid flex-1 text-start text-sm leading-tight">
                                    <span class="truncate font-semibold">{{ optional(auth()->user())->name }}</span>
                                    <span class="truncate text-xs">{{ optional(auth()->user())->email }}</span>
                                </div>
                            </div>
                        </div>
                    </flux:menu.radio.group>

                    <flux:menu.separator />

                    <flux:menu.radio.group>
                        <flux:menu.item :href="route('settings.profile')" icon="cog" wire:navigate>{{ __('Settings') }}</flux:menu.item>
                    </flux:menu.radio.group>

                    <flux:menu.separator />

                    <form method="POST" action="{{ route('logout') }}" class="w-full">
                        @csrf
                        <flux:menu.item as="button" type="submit" icon="arrow-right-start-on-rectangle" class="w-full">
                            {{ __('Log Out') }}
                        </flux:menu.item>
                    </form>
                </flux:menu>
            </flux:dropdown>
        </flux:header>
        @endauth

        {{ $slot }}

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
    </body>
    
</html>
