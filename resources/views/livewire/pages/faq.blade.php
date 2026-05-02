<?php

use Livewire\Volt\Component;

new class extends Component {
    public function with(): array
    {
        return [
            'faqs' => [
                [
                    'category' => 'Umum',
                    'questions' => [
                        [
                            'q' => 'Apa itu platform Task ini?',
                            'a' => 'Platform ini adalah tempat Anda bisa mengerjakan berbagai task dan mendapatkan reward. Task yang tersedia beragam mulai dari share konten, join grup WhatsApp, hingga promosi produk.'
                        ],
                        [
                            'q' => 'Apakah platform ini gratis?',
                            'a' => 'Ya, platform ini 100% gratis untuk digunakan. Anda tidak perlu membayar apapun untuk bergabung dan mengerjakan task.'
                        ],
                        [
                            'q' => 'Bagaimana cara mendaftar?',
                            'a' => 'Klik tombol "Daftar" di halaman utama, isi formulir pendaftaran dengan data yang valid termasuk nomor WhatsApp aktif, lalu verifikasi email Anda.'
                        ],
                    ]
                ],
                [
                    'category' => 'Task',
                    'questions' => [
                        [
                            'q' => 'Bagaimana cara mengambil task?',
                            'a' => 'Pilih kategori task yang Anda inginkan, pilih task yang tersedia, lalu klik tombol "Ambil Task". Baca instruksi dengan teliti sebelum mulai mengerjakan.'
                        ],
                        [
                            'q' => 'Berapa lama deadline pengerjaan task?',
                            'a' => 'Setiap task memiliki deadline 3 hari kerja sejak Anda mengambil task tersebut. Pastikan menyelesaikan sebelum deadline berakhir.'
                        ],
                        [
                            'q' => 'Apakah bisa mengerjakan lebih dari 1 task sekaligus?',
                            'a' => 'Anda hanya bisa mengerjakan 1 task aktif dalam satu waktu. Selesaikan task yang sedang dikerjakan terlebih dahulu sebelum mengambil task baru.'
                        ],
                        [
                            'q' => 'Apa yang terjadi jika task gagal/ditolak?',
                            'a' => 'Jika bukti Anda ditolak, Anda akan mendapat notifikasi beserta alasan penolakan. Anda bisa memperbaiki dan submit ulang selama masih dalam deadline.'
                        ],
                    ]
                ],
                [
                    'category' => 'Pembayaran & Reward',
                    'questions' => [
                        [
                            'q' => 'Kapan reward akan dibayarkan?',
                            'a' => 'Reward akan dibayarkan setelah task Anda diverifikasi dan disetujui oleh admin. Proses verifikasi membutuhkan waktu 1-24 jam kerja.'
                        ],
                        [
                            'q' => 'Metode pembayaran apa yang tersedia?',
                            'a' => 'Pembayaran dilakukan melalui transfer bank atau e-wallet (GoPay, OVO, DANA, ShopeePay). Pastikan data rekening di profil Anda sudah benar.'
                        ],
                        [
                            'q' => 'Berapa minimum withdraw?',
                            'a' => 'Minimum withdraw adalah Rp 50.000. Saldo di bawah nominal tersebut akan diakumulasikan untuk withdraw berikutnya.'
                        ],
                    ]
                ],
                [
                    'category' => 'Masalah Teknis',
                    'questions' => [
                        [
                            'q' => 'Upload bukti gagal, apa yang harus dilakukan?',
                            'a' => 'Pastikan ukuran file tidak melebihi 2MB dan format file adalah JPG, PNG, atau PDF. Coba kompres file atau gunakan format lain.'
                        ],
                        [
                            'q' => 'Tidak bisa login ke akun, bagaimana?',
                            'a' => 'Gunakan fitur "Lupa Password" untuk reset password. Jika masih bermasalah, hubungi admin melalui WhatsApp yang tertera di halaman kontak.'
                        ],
                        [
                            'q' => 'Task tidak muncul di dashboard, kenapa?',
                            'a' => 'Task yang tersedia bergantung pada ketersediaan dari admin. Jika tidak ada task, tunggu beberapa saat atau cek kategori lain.'
                        ],
                    ]
                ],
            ]
        ];
    }
}; ?>

<div x-data="{ openFaq: null }">
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
                        <h1 class="text-xl font-bold text-zinc-900">FAQ</h1>
                        <p class="text-sm text-zinc-600">Pertanyaan yang sering ditanyakan</p>
                    </div>
                </div>

                <!-- FAQ Accordion -->
                @foreach($faqs as $categoryIndex => $category)
                    <div class="bg-white rounded-xl border border-zinc-200 overflow-hidden">
                        <div class="px-4 py-3 bg-zinc-50 border-b border-zinc-200">
                            <h3 class="font-bold text-zinc-900">{{ $category['category'] }}</h3>
                        </div>
                        <div class="divide-y divide-zinc-200">
                            @foreach($category['questions'] as $qIndex => $faq)
                                @php $faqId = $categoryIndex . '-' . $qIndex; @endphp
                                <div x-data>
                                    <button 
                                        @click="openFaq = openFaq === '{{ $faqId }}' ? null : '{{ $faqId }}'"
                                        class="w-full px-4 py-3 flex items-center justify-between text-left hover:bg-zinc-50 transition-colors"
                                    >
                                        <span class="font-medium text-sm text-zinc-900 pr-4">{{ $faq['q'] }}</span>
                                        <svg class="w-5 h-5 text-zinc-400 shrink-0 transition-transform duration-200"
                                            :class="openFaq === '{{ $faqId }}' ? 'rotate-180' : ''"
                                             fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                        </svg>
                                    </button>
                                    <div x-show="openFaq === '{{ $faqId }}'"
                                         x-collapse
                                         class="px-4 pb-4 text-sm text-zinc-600 bg-zinc-50">
                                        {{ $faq['a'] }}
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endforeach

                <!-- Contact Card -->
                <div class="bg-gradient-to-r from-blue-500 to-indigo-600 rounded-xl p-4 text-white">
                    <h3 class="font-bold text-lg flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        Masih Ada Pertanyaan?
                    </h3>
                    <p class="text-sm opacity-90 mt-2">Hubungi admin melalui WhatsApp untuk bantuan lebih lanjut.</p>
                    <a href="https://wa.me/6281234567890" target="_blank" class="mt-3 inline-flex items-center gap-2 px-4 py-2 bg-white text-blue-600 font-semibold rounded-lg hover:bg-blue-50 transition-colors">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
                        </svg>
                        Hubungi Admin
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
                        <h1 class="text-3xl font-bold text-zinc-900">FAQ</h1>
                        <p class="text-zinc-600 mt-1">Pertanyaan yang sering ditanyakan oleh pengguna</p>
                    </div>
                </div>

                <!-- FAQ Grid -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    @foreach($faqs as $categoryIndex => $category)
                        <div class="bg-white rounded-xl border border-zinc-200 overflow-hidden">
                            <div class="px-6 py-4 bg-gradient-to-r from-zinc-100 to-zinc-50 border-b border-zinc-200">
                                <h3 class="font-bold text-lg text-zinc-900 flex items-center gap-2">
                                    @if($category['category'] === 'Umum')
                                        <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                    @elseif($category['category'] === 'Task')
                                        <svg class="w-5 h-5 text-violet-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                                        </svg>
                                    @elseif($category['category'] === 'Pembayaran & Reward')
                                        <svg class="w-5 h-5 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                    @else
                                        <svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        </svg>
                                    @endif
                                    {{ $category['category'] }}
                                </h3>
                            </div>
                            <div class="divide-y divide-zinc-200">
                                @foreach($category['questions'] as $qIndex => $faq)
                                    @php $faqId = $categoryIndex . '-' . $qIndex; @endphp
                                    <div x-data>
                                        <button 
                                            @click="openFaq = openFaq === '{{ $faqId }}' ? null : '{{ $faqId }}'"
                                            class="w-full px-6 py-4 flex items-center justify-between text-left hover:bg-zinc-50 transition-colors"
                                        >
                                            <span class="font-medium text-zinc-900 pr-4">{{ $faq['q'] }}</span>
                                            <svg class="w-5 h-5 text-zinc-400 shrink-0 transition-transform duration-200"
                                                :class="openFaq === '{{ $faqId }}' ? 'rotate-180' : ''"
                                                 fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                            </svg>
                                        </button>
                                        <div x-show="openFaq === '{{ $faqId }}'"
                                             x-collapse
                                             class="px-6 pb-4 text-zinc-600 bg-zinc-50">
                                            {{ $faq['a'] }}
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Contact Section -->
                <div class="bg-gradient-to-r from-blue-500 to-indigo-600 rounded-xl p-6 text-white flex items-center justify-between">
                    <div class="flex items-center gap-4">
                        <div class="w-14 h-14 bg-white/20 rounded-full flex items-center justify-center">
                            <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <div>
                            <h3 class="font-bold text-xl">Masih Ada Pertanyaan?</h3>
                            <p class="opacity-90">Jangan ragu untuk menghubungi admin melalui WhatsApp untuk bantuan lebih lanjut.</p>
                        </div>
                    </div>
                    <a href="https://wa.me/6281234567890" target="_blank" class="inline-flex items-center gap-2 px-6 py-3 bg-white text-blue-600 font-bold rounded-lg hover:bg-blue-50 transition-colors shrink-0">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
                        </svg>
                        Hubungi Admin
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
