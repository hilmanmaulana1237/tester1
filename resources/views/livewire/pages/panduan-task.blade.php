<?php

use Livewire\Volt\Component;

new class extends Component {
    public function with(): array
    {
        return [
            'steps' => [
                [
                    'title' => 'Pilih Task yang Sesuai',
                    'description' => 'Pilih task yang sesuai dengan kemampuan dan waktu Anda. Perhatikan tingkat kesulitan dan estimasi reward.',
                    'icon' => 'search',
                    'tips' => ['Baca deskripsi task dengan teliti', 'Perhatikan deadline yang diberikan', 'Pilih task dengan tingkat kesulitan yang Anda kuasai']
                ],
                [
                    'title' => 'Ambil Task',
                    'description' => 'Klik tombol "Ambil Task" untuk memulai mengerjakan. Task akan terkunci selama Anda mengerjakan.',
                    'icon' => 'hand-raised',
                    'tips' => ['Pastikan Anda siap mengerjakan sebelum mengambil', 'Anda hanya bisa mengerjakan 1 task aktif', 'Task yang diambil memiliki deadline 3 hari']
                ],
                [
                    'title' => 'Baca Instruksi',
                    'description' => 'Baca dan pahami instruksi dengan seksama. Centang checkbox bahwa Anda sudah memahami instruksi.',
                    'icon' => 'document-text',
                    'tips' => ['Baca instruksi minimal 2x untuk memastikan paham', 'Catat poin-poin penting', 'Jangan ragu bertanya jika ada yang tidak jelas']
                ],
                [
                    'title' => 'Kerjakan Task',
                    'description' => 'Kerjakan task sesuai instruksi yang diberikan. Pastikan mengikuti semua langkah dengan benar.',
                    'icon' => 'play',
                    'tips' => ['Ikuti instruksi step by step', 'Jangan skip langkah apapun', 'Dokumentasikan proses pengerjaan']
                ],
                [
                    'title' => 'Upload Bukti',
                    'description' => 'Upload bukti pengerjaan berupa screenshot atau foto. Bukti harus jelas dan sesuai dengan yang diminta.',
                    'icon' => 'camera',
                    'tips' => ['Pastikan screenshot jelas dan tidak blur', 'Ambil screenshot sesuai yang diminta', 'Pastikan semua elemen penting terlihat']
                ],
                [
                    'title' => 'Tunggu Verifikasi',
                    'description' => 'Admin akan memverifikasi bukti Anda. Proses verifikasi biasanya memakan waktu 1-24 jam.',
                    'icon' => 'clock',
                    'tips' => ['Sabar menunggu proses verifikasi', 'Cek status task secara berkala', 'Jika ada revisi, segera perbaiki']
                ],
            ]
        ];
    }
}; ?>

<div>
    <!-- Mobile View -->
    <div class="block sm:hidden">
            <div class="flex flex-col gap-4 p-4">
                <!-- Header -->
                <div class="flex items-center gap-3">
                    <a href="{{ route('dashboard') }}" class="p-2 bg-white border border-zinc-200 rounded-lg">
                        <svg class="w-5 h-5 text-zinc-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                        </svg>
                    </a>
                    <div>
                        <h1 class="text-xl font-bold text-zinc-900">Panduan Task</h1>
                        <p class="text-sm text-zinc-600">Cara mengerjakan task dengan benar</p>
                    </div>
                </div>

                <!-- Steps -->
                <div class="space-y-4">
                    @foreach($steps as $index => $step)
                        <div class="bg-white rounded-xl border border-zinc-200 p-4">
                            <div class="flex items-start gap-3">
                                <div class="w-10 h-10 bg-gradient-to-br from-violet-500 to-purple-600 rounded-lg flex items-center justify-center text-white font-bold shrink-0">
                                    {{ $index + 1 }}
                                </div>
                                <div class="flex-1">
                                    <h3 class="font-bold text-zinc-900">{{ $step['title'] }}</h3>
                                    <p class="text-sm text-zinc-600 mt-1">{{ $step['description'] }}</p>
                                    
                                    <div class="mt-3 space-y-2">
                                        @foreach($step['tips'] as $tip)
                                            <div class="flex items-start gap-2 text-xs text-zinc-500">
                                                <svg class="w-4 h-4 text-violet-500 shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                                </svg>
                                                <span>{{ $tip }}</span>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- CTA -->
                <div class="bg-gradient-to-r from-violet-500 to-purple-600 rounded-xl p-4 text-white">
                    <h3 class="font-bold text-lg">Siap Mulai?</h3>
                    <p class="text-sm opacity-90 mt-1">Temukan task yang cocok untuk Anda dan mulai dapatkan reward!</p>
                    <a href="{{ route('user.dashboard') }}" class="mt-3 inline-flex items-center gap-2 px-4 py-2 bg-white text-violet-600 font-semibold rounded-lg hover:bg-violet-50 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
                        </svg>
                        Lihat Task Tersedia
                    </a>
                </div>
            </div>
        </div>

        <!-- Desktop View -->
        <div class="hidden sm:block">
            <div class="flex flex-col gap-6 p-6">
                <!-- Header -->
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-3xl font-bold text-zinc-900">Panduan Task</h1>
                        <p class="text-zinc-600 mt-1">Ikuti langkah-langkah berikut untuk mengerjakan task dengan benar</p>
                    </div>
                    <a href="{{ route('user.dashboard') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-violet-600 text-white font-semibold rounded-lg hover:bg-violet-700 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                        </svg>
                        Lihat Task
                    </a>
                </div>

                <!-- Steps Timeline -->
                <div class="relative">
                    <!-- Timeline Line -->
                    <div class="absolute left-6 top-0 bottom-0 w-0.5 bg-gradient-to-b from-violet-500 to-purple-600"></div>
                    
                    <div class="space-y-6">
                        @foreach($steps as $index => $step)
                            <div class="relative flex gap-6">
                                <!-- Step Number -->
                                <div class="relative z-10 w-12 h-12 bg-gradient-to-br from-violet-500 to-purple-600 rounded-full flex items-center justify-center text-white font-bold text-lg shadow-lg">
                                    {{ $index + 1 }}
                                </div>
                                
                                <!-- Step Content -->
                                <div class="flex-1 bg-white rounded-xl border border-zinc-200 p-6 hover:shadow-lg transition-shadow">
                                    <h3 class="text-xl font-bold text-zinc-900">{{ $step['title'] }}</h3>
                                    <p class="text-zinc-600 mt-2">{{ $step['description'] }}</p>
                                    
                                    <div class="mt-4 grid grid-cols-1 md:grid-cols-3 gap-3">
                                        @foreach($step['tips'] as $tip)
                                            <div class="flex items-start gap-2 p-3 bg-violet-50 rounded-lg">
                                                <svg class="w-5 h-5 text-violet-500 shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                                </svg>
                                                <span class="text-sm text-zinc-700">{{ $tip }}</span>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Bottom CTA -->
                <div class="bg-gradient-to-r from-violet-500 to-purple-600 rounded-xl p-6 text-white flex items-center justify-between">
                    <div>
                        <h3 class="font-bold text-2xl">Siap untuk Memulai?</h3>
                        <p class="opacity-90 mt-1">Temukan task yang cocok untuk Anda dan mulai dapatkan reward sekarang!</p>
                    </div>
                    <a href="{{ route('user.dashboard') }}" class="inline-flex items-center gap-2 px-6 py-3 bg-white text-violet-600 font-bold rounded-lg hover:bg-violet-50 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
                        </svg>
                        Lihat Task Tersedia
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
