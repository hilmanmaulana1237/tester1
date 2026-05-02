<?php

use Livewire\Volt\Component;

new class extends Component {
    //
}; ?>

<section class="w-full">
    @include('partials.settings-heading')

    <x-settings.layout :heading="__('Appearance')" :subheading=" __('Tampilan dikunci ke mode terang untuk konsistensi.')">
        <div class="p-4 rounded-lg border border-gray-200 bg-white text-gray-800">
            {{ __('Saat ini aplikasi hanya mendukung mode terang. Tidak ada pengaturan tema yang perlu diubah.') }}
        </div>
    </x-settings.layout>
</section>
