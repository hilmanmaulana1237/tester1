<?php

use Livewire\Volt\Component;

new class extends Component {
    public function with(): array
    {
        return [
            'tips' => [
                [
                    'category' => 'Persiapan',
                    'color' => 'blue',
                    'items' => [
                        ['title' => 'Lengkapi Profil', 'desc' => 'Pastikan profil Anda lengkap termasuk nomor WhatsApp untuk memudahkan komunikasi dengan admin.'],
                        ['title' => 'Siapkan Perangkat', 'desc' => 'Pastikan HP/laptop Anda dalam kondisi baik dengan koneksi internet stabil.'],
                        ['title' => 'Pelajari Platform', 'desc' => 'Kenali fitur-fitur platform sebelum mulai mengerjakan task.'],
                    ]
                ],
                [
                    'category' => 'Pengerjaan Task',
                    'color' => 'violet',
                    'items' => [
                        ['title' => 'Pilih Task Sesuai Kemampuan', 'desc' => 'Mulai dari task dengan tingkat kesulitan rendah untuk membangun reputasi.'],
                        ['title' => 'Baca Instruksi 2x', 'desc' => 'Pastikan Anda memahami instruksi dengan benar sebelum mulai mengerjakan.'],
                        ['title' => 'Kerjakan dengan Teliti', 'desc' => 'Kualitas lebih penting dari kecepatan. Admin akan mengecek hasil kerja Anda.'],
                        ['title' => 'Dokumentasi Proses', 'desc' => 'Screenshot setiap langkah penting sebagai bukti pengerjaan.'],
                    ]
                ],
                [
                    'category' => 'Upload Bukti',
                    'color' => 'amber',
                    'items' => [
                        ['title' => 'Screenshot Jelas', 'desc' => 'Pastikan screenshot tidak blur dan semua informasi penting terlihat.'],
                        ['title' => 'Sesuai Permintaan', 'desc' => 'Upload bukti sesuai dengan yang diminta di instruksi task.'],
                        ['title' => 'Ukuran File', 'desc' => 'Kompres gambar jika ukuran terlalu besar (max 2MB per file).'],
                    ]
                ],
                [
                    'category' => 'Setelah Submit',
                    'color' => 'purple',
                    'items' => [
                        ['title' => 'Sabar Menunggu', 'desc' => 'Proses verifikasi membutuhkan waktu 1-24 jam kerja.'],
                        ['title' => 'Cek Notifikasi', 'desc' => 'Pantau notifikasi untuk update status task Anda.'],
                        ['title' => 'Siap Revisi', 'desc' => 'Jika ada permintaan revisi, segera perbaiki sesuai feedback admin.'],
                    ]
                ],
            ],
            'donts' => [
                'Menggunakan akun palsu atau bot',
                'Submit bukti palsu atau hasil edit',
                'Mengerjakan task dengan asal-asalan',
                'Mengabaikan deadline yang diberikan',
                'Spam atau menghubungi admin berulang kali',
                'Membagikan informasi task ke orang lain',
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
                        <h1 class="text-xl font-bold text-zinc-900">Tips Sukses</h1>
                        <p class="text-sm text-zinc-600">Rahasia sukses mendapatkan reward</p>
                    </div>
                </div>

                <!-- Tips Cards -->
                @foreach($tips as $section)
                    <div class="bg-white rounded-xl border border-zinc-200 overflow-hidden">
                        <div class="px-4 py-3 bg-{{ $section['color'] }}-50{{ $section['color'] }}-900/20 border-b border-{{ $section['color'] }}-200{{ $section['color'] }}-800">
                            <h3 class="font-bold text-{{ $section['color'] }}-800{{ $section['color'] }}-300">{{ $section['category'] }}</h3>
                        </div>
                        <div class="p-4 space-y-3">
                            @foreach($section['items'] as $item)
                                <div class="flex gap-3">
                                    <div class="w-6 h-6 bg-{{ $section['color'] }}-100{{ $section['color'] }}-900/30 rounded-full flex items-center justify-center shrink-0">
                                        <svg class="w-4 h-4 text-{{ $section['color'] }}-600{{ $section['color'] }}-400" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                        </svg>
                                    </div>
                                    <div>
                                        <h4 class="font-semibold text-zinc-900 text-sm">{{ $item['title'] }}</h4>
                                        <p class="text-xs text-zinc-600 mt-0.5">{{ $item['desc'] }}</p>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endforeach

                <!-- Don'ts -->
                <div class="bg-red-50 rounded-xl border border-red-200 p-4">
                    <h3 class="font-bold text-red-800 flex items-center gap-2">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                        </svg>
                        Yang Harus Dihindari
                    </h3>
                    <ul class="mt-3 space-y-2">
                        @foreach($donts as $dont)
                            <li class="flex items-start gap-2 text-sm text-red-700">
                                <span class="text-red-500">✕</span>
                                <span>{{ $dont }}</span>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>

        <!-- Desktop View -->
        <div class="hidden sm:block">
            <div class="flex flex-col gap-6 p-6">
                <!-- Header -->
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-3xl font-bold text-zinc-900">Tips Sukses</h1>
                        <p class="text-zinc-600 mt-1">Rahasia sukses mendapatkan reward maksimal dari setiap task</p>
                    </div>
                    <a href="{{ route('user.dashboard') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-violet-600 text-white font-semibold rounded-lg hover:bg-violet-700 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                        </svg>
                        Mulai Task
                    </a>
                </div>

                <!-- Tips Grid -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    @foreach($tips as $section)
                        <div class="bg-white rounded-xl border border-zinc-200 overflow-hidden hover:shadow-lg transition-shadow">
                            <div class="px-6 py-4 bg-gradient-to-r 
                                @if($section['color'] === 'blue') from-blue-500 to-blue-600
                                @elseif($section['color'] === 'violet') from-violet-500 to-violet-600
                                @elseif($section['color'] === 'amber') from-amber-500 to-amber-600
                                @else from-purple-500 to-purple-600
                                @endif text-white">
                                <h3 class="font-bold text-lg">{{ $section['category'] }}</h3>
                            </div>
                            <div class="p-6 space-y-4">
                                @foreach($section['items'] as $item)
                                    <div class="flex gap-4">
                                        <div class="w-8 h-8 
                                            @if($section['color'] === 'blue') bg-blue-100
                                            @elseif($section['color'] === 'violet') bg-violet-100
                                            @elseif($section['color'] === 'amber') bg-amber-100
                                            @else bg-purple-100
                                            @endif rounded-full flex items-center justify-center shrink-0">
                                            <svg class="w-5 h-5 
                                                @if($section['color'] === 'blue') text-blue-600
                                                @elseif($section['color'] === 'violet') text-violet-600
                                                @elseif($section['color'] === 'amber') text-amber-600
                                                @else text-purple-600
                                                @endif" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                            </svg>
                                        </div>
                                        <div>
                                            <h4 class="font-semibold text-zinc-900">{{ $item['title'] }}</h4>
                                            <p class="text-sm text-zinc-600 mt-1">{{ $item['desc'] }}</p>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Don'ts Section -->
                <div class="bg-gradient-to-r from-red-50 to-rose-50 rounded-xl border border-red-200 p-6">
                    <h3 class="font-bold text-xl text-red-800 flex items-center gap-3 mb-4">
                        <div class="w-10 h-10 bg-red-100 rounded-full flex items-center justify-center">
                            <svg class="w-6 h-6 text-red-600" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                            </svg>
                        </div>
                        Yang Harus Dihindari
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        @foreach($donts as $dont)
                            <div class="flex items-center gap-3 p-3 bg-white rounded-lg border border-red-200">
                                <span class="text-red-500 font-bold">✕</span>
                                <span class="text-sm text-zinc-700">{{ $dont }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Motivational Quote -->
                <div class="bg-gradient-to-r from-violet-500 to-purple-600 rounded-xl p-6 text-white text-center">
                    <svg class="w-12 h-12 mx-auto mb-4 opacity-50" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M14.017 21v-7.391c0-5.704 3.731-9.57 8.983-10.609l.995 2.151c-2.432.917-3.995 3.638-3.995 5.849h4v10h-9.983zm-14.017 0v-7.391c0-5.704 3.748-9.57 9-10.609l.996 2.151c-2.433.917-3.996 3.638-3.996 5.849h3.983v10h-9.983z"/>
                    </svg>
                    <p class="text-xl font-medium italic">"Konsistensi adalah kunci kesuksesan. Kerjakan task dengan teliti dan hasil akan mengikuti."</p>
                </div>
            </div>
        </div>
    </div>
</div>
