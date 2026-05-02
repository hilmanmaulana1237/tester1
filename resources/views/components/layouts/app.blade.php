@php
    $isAdmin = auth()->check() && auth()->user()->isAdmin();
@endphp

@if($isAdmin)
    {{-- Admin uses sidebar layout --}}
    <x-layouts.app.sidebar :title="$title ?? null">
        <flux:main>
            {{ $slot }}
        </flux:main>
    </x-layouts.app.sidebar>
@else
    {{-- User uses bottom navigation layout --}}
    <x-layouts.app.user :title="$title ?? null">
        {{ $slot }}
    </x-layouts.app.user>
@endif