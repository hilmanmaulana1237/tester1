<div class="min-h-screen bg-zinc-50 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-5xl mx-auto space-y-12">

        <!-- Header -->
        <div class="text-center space-y-4">
            <h1 class="text-4xl font-extrabold text-zinc-900 sm:text-5xl tracking-tight">
                Panduan Mengerjakan Tugas
            </h1>
            <p class="text-lg text-zinc-600 max-w-2xl mx-auto">
                Tonton video tutorial di bawah ini untuk memahami langkah-langkah mengerjakan tugas dan mendapatkan
                penghasilan.
            </p>
        </div>

        <!-- Video Section -->
        <div
            class="relative w-full max-w-4xl mx-auto rounded-3xl overflow-hidden shadow-2xl shadow-violet-500/10 border border-zinc-200 bg-black">
            <div class="aspect-video w-full">
                <iframe class="w-full h-full" src="https://www.youtube.com/embed/wCwBwLmPqdY?si=4PodaT3NdmMjcX8K&rel=0"
                    title="Tutorial Mengerjakan Tugas" frameborder="0"
                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
                    referrerpolicy="strict-origin-when-cross-origin" allowfullscreen>
                </iframe>
            </div>
        </div>

        <!-- Step-by-Step Guide -->
        <div class="grid gap-8 md:grid-cols-3">
            <!-- Step 1 -->
            <div
                class="bg-white rounded-2xl p-6 shadow-xl border border-zinc-100 hover:border-violet-500/30 transition-all group">
                <div
                    class="w-12 h-12 bg-violet-100 rounded-xl flex items-center justify-center mb-6 text-2xl group-hover:scale-110 transition-transform duration-300">
                    🔍
                </div>
                <h3 class="text-xl font-bold text-zinc-900 mb-2">1. Pilih Tugas</h3>
                <p class="text-zinc-600 leading-relaxed">
                    Masuk ke Dashboard dan pilih tugas yang tersedia. Pastikan membaca deskripsi singkat sebelum
                    mengambil tugas.
                </p>
            </div>

            <!-- Step 2 -->
            <div
                class="bg-white rounded-2xl p-6 shadow-xl border border-zinc-100 hover:border-violet-500/30 transition-all group">
                <div
                    class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center mb-6 text-2xl group-hover:scale-110 transition-transform duration-300">
                    📝
                </div>
                <h3 class="text-xl font-bold text-zinc-900 mb-2">2. Kerjakan & Upload</h3>
                <p class="text-zinc-600 leading-relaxed">
                    Ikuti instruksi dengan teliti. Setelah selesai, upload bukti screenshot atau kirim link bukti
                    pekerjaan Anda.
                </p>
            </div>

            <!-- Step 3 -->
            <div
                class="bg-white rounded-2xl p-6 shadow-xl border border-zinc-100 hover:border-violet-500/30 transition-all group">
                <div
                    class="w-12 h-12 bg-orange-100 rounded-xl flex items-center justify-center mb-6 text-2xl group-hover:scale-110 transition-transform duration-300">
                    💸
                </div>
                <h3 class="text-xl font-bold text-zinc-900 mb-2">3. Tunggu Verifikasi</h3>
                <p class="text-zinc-600 leading-relaxed">
                    Admin akan memeriksa pekerjaan Anda. Jika valid, status akan berubah menjadi "Completed" dan saldo
                    otomatis masuk.
                </p>
            </div>
        </div>

        <!-- CTA -->
        <div class="text-center pt-8">
            <a href="<?php echo e(route('user.dashboard')); ?>"
                class="inline-flex items-center gap-2 px-8 py-4 bg-gradient-to-r from-violet-600 to-purple-600 hover:from-violet-700 hover:to-purple-700 text-white rounded-full font-bold text-lg shadow-lg hover:shadow-violet-500/40 hover:-translate-y-1 transition-all duration-300">
                Mulai Kerjakan Tugas Sekarang
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6">
                    </path>
                </svg>
            </a>
        </div>
    </div>
</div><?php /**PATH C:\laragon\www\baru\resources\views\livewire/pages/tutorial-page.blade.php ENDPATH**/ ?>