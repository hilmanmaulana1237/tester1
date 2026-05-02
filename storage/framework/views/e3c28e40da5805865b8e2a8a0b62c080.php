<!DOCTYPE html>
<html lang="<?php echo e(str_replace('_', '-', app()->getLocale())); ?>">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>CuanInstan - Kelola Tugas Anda dengan Mudah</title>

    <link rel="icon" href="/favicon.ico" sizes="any">
    <link rel="icon" href="/favicon.svg" type="image/svg+xml">
    <link rel="apple-touch-icon" href="/apple-touch-icon.png">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700" rel="stylesheet" />

    <?php echo app('Illuminate\Foundation\Vite')(['resources/css/app.css', 'resources/js/app.js']); ?>

    <style>
        body {
            font-family: 'Inter', sans-serif;
        }

        /* Warna Baru - Skema Hijau Forest (#7c3aed) */
        .gradient-bg {
            background: linear-gradient(135deg, #7c3aed 0%, #8b5cf6 50%, #a78bfa 100%);
        }

        .gradient-text {
            background: linear-gradient(135deg, #7c3aed 0%, #8b5cf6 50%, #a78bfa 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .bg-violet-50 {
            background-color: #f5f3ff;
        }

        .text-violet-600 {
            color: #7c3aed;
        }

        .border-violet-200 {
            border-color: #ddd6fe;
        }

        /* Animasi dan Transisi */
        .card-hover {
            transition: all 0.3s ease;
        }

        .card-hover:hover {
            transform: translateY(-8px);
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        }

        @keyframes float {

            0%,
            100% {
                transform: translateY(0px);
            }

            50% {
                transform: translateY(-20px);
            }
        }

        .float-animation {
            animation: float 4s ease-in-out infinite;
        }

        @keyframes pulse {

            0%,
            100% {
                opacity: 1;
            }

            50% {
                opacity: 0.7;
            }
        }

        .pulse-animation {
            animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
        }

        /* Hero Pattern */
        .hero-pattern {
            background-image: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%237c3aed' fill-opacity='0.05'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
        }

        /* Glassmorphism Effect */
        .glass {
            background: rgba(255, 255, 255, 0.7);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.3);
        }

        /* Custom Scrollbar */
        ::-webkit-scrollbar {
            width: 8px;
        }

        ::-webkit-scrollbar-track {
            background: #f1f1f1;
        }

        ::-webkit-scrollbar-thumb {
            background: #7c3aed;
            border-radius: 4px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: #8b5cf6;
        }

        /* Button Styles */
        .btn-primary {
            padding: 0.75rem 1.5rem;
            border-radius: 0.5rem;
            font-weight: 500;
            transition: all 0.3s ease;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
        }

        .btn-primary:hover {
            opacity: 0.9;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
            transform: translateY(-2px);
        }

        .btn-secondary {
            padding: 0.75rem 1.5rem;
            background-color: white;
            border: 2px solid;
            border-radius: 0.5rem;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        /* Feature Card */
        .feature-card {
            background-color: white;
            padding: 2rem;
            border-radius: 0.75rem;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            border: 1px solid #f3f4f6;
            overflow: hidden;
            position: relative;
        }

        .feature-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 4px;
            background: linear-gradient(90deg, #7c3aed, #8b5cf6, #a78bfa);
            transform: translateX(-100%);
            transition: transform 0.5s ease;
        }

        .feature-card:hover::before {
            transform: translateX(0);
        }

        /* Step Connector */
        .step-connector {
            position: absolute;
            top: 40px;
            left: 60px;
            right: -60px;
            height: 2px;
            background: linear-gradient(90deg, #7c3aed, #8b5cf6, #a78bfa);
            z-index: -1;
        }

        /* Testimonial Card */
        .testimonial-card {
            background-color: white;
            padding: 1.5rem;
            border-radius: 0.75rem;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            border: 1px solid #f3f4f6;
        }

        /* Responsive adjustments */
        @media (max-width: 768px) {
            .step-connector {
                display: none;
            }
        }
    </style>
</head>

<body class="antialiased bg-gray-50">
    <!-- Navigation -->
    <nav class="fixed w-full glass shadow-sm z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <!-- Logo -->
                <div class="flex items-center">
                    <a href="/" class="flex items-center space-x-2">
                        <div class="w-10 h-10 gradient-bg rounded-lg flex items-center justify-center shadow-md">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                            </svg>
                        </div>
                        <span class="text-xl font-bold gradient-text">CuanInstan</span>
                    </a>
                </div>

                <!-- Desktop Navigation -->
                <div class="hidden md:flex items-center space-x-4">
                    <?php if(Route::has('login')): ?>
                        <?php if(auth()->guard()->check()): ?>
                            <a href="<?php echo e(url('/dashboard')); ?>"
                                class="px-4 py-2 text-gray-700 hover:text-violet-600 transition font-medium">
                                Dashboard
                            </a>
                        <?php else: ?>
                            <a href="<?php echo e(route('login')); ?>"
                                class="px-4 py-2 text-gray-700 hover:text-violet-600 transition font-medium">
                                Masuk
                            </a>
                            <?php if(Route::has('register')): ?>
                                <a href="<?php echo e(route('register')); ?>" class="btn-primary">
                                    Daftar Gratis
                                </a>
                            <?php endif; ?>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>

                <!-- Mobile Menu Button -->
                <div class="md:hidden">
                    <button id="mobile-menu-button" class="p-2 rounded-lg text-gray-700 hover:bg-gray-100 transition">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>

        <!-- Mobile Menu -->
        <div id="mobile-menu" class="hidden md:hidden glass border-t">
            <div class="px-4 py-3 space-y-2">
                <?php if(Route::has('login')): ?>
                    <?php if(auth()->guard()->check()): ?>
                        <a href="<?php echo e(url('/dashboard')); ?>"
                            class="block px-4 py-2 text-gray-700 hover:bg-violet-50 rounded-lg transition font-medium">
                            Dashboard
                        </a>
                    <?php else: ?>
                        <a href="<?php echo e(route('login')); ?>"
                            class="block px-4 py-2 text-gray-700 hover:bg-violet-50 rounded-lg transition font-medium">
                            Masuk
                        </a>
                        <?php if(Route::has('register')): ?>
                            <a href="<?php echo e(route('register')); ?>"
                                class="block px-4 py-2 gradient-bg text-white rounded-lg text-center font-medium hover:opacity-90 transition">
                                Daftar Gratis
                            </a>
                        <?php endif; ?>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="pt-24 pb-12 md:pt-32 md:pb-20 hero-pattern relative overflow-hidden">
        <!-- Background Elements -->
        <div
            class="absolute top-20 right-10 w-64 h-64 bg-violet-200 rounded-full mix-blend-multiply filter blur-xl opacity-20 animate-blob">
        </div>
        <div
            class="absolute bottom-10 left-10 w-64 h-64 bg-violet-300 rounded-full mix-blend-multiply filter blur-xl opacity-20 animate-blob animation-delay-2000">
        </div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            <div class="grid md:grid-cols-2 gap-12 items-center">
                <!-- Hero Content -->
                <div class="text-center md:text-left">
                    <div
                        class="inline-block px-4 py-1 bg-violet-100 text-violet-800 rounded-full text-sm font-medium mb-4">
                        💰 Platform Penghasilan Tambahan Terpercaya
                    </div>
                    <h1 class="text-4xl md:text-5xl lg:text-6xl font-bold text-gray-900 mb-6 leading-tight">
                        Dapatkan <span class="gradient-text">Penghasilan Tanpa Modal.</span> Cukup Selesaikan <span
                            class="gradient-text">Tugas Sederhana</span>
                    </h1>
                    <p class="text-lg md:text-xl text-gray-600 mb-8 max-w-lg">
                        Tidak perlu cari orang! Kontaknya sudah kami siapkan. Kamu hanya tinggal masukkan ke grup
                        WhatsApp → upload bukti → saldo langsung masuk.
                    </p>
                    <div class="flex flex-col sm:flex-row gap-4 justify-center md:justify-start">
                        <?php if(Route::has('register')): ?>
                            <a href="<?php echo e(route('register')); ?>" class="btn-primary text-lg">
                                Daftar Gratis & Mulai Dapatkan Uang Sekarang!
                            </a>
                        <?php endif; ?>
                    </div>

                    <!-- Stats -->
                    <div class="flex flex-wrap gap-6 mt-10 justify-center md:justify-start">
                        <div class="text-center">
                            <div class="text-3xl font-bold gradient-text">10K+</div>
                            <div class="text-gray-600">Worker Aktif</div>
                        </div>
                        <div class="text-center">
                            <div class="text-3xl font-bold gradient-text">Rp 2M+</div>
                            <div class="text-gray-600">Dibayarkan</div>
                        </div>
                        <div class="text-center">
                            <div class="text-3xl font-bold gradient-text">98%</div>
                            <div class="text-gray-600">Pembayaran Tepat Waktu</div>
                        </div>
                    </div>
                </div>

                <!-- Hero Image -->
                <div class="relative">
                    <div class="float-animation">
                        <div class="bg-white rounded-2xl shadow-2xl p-6 md:p-8 border border-gray-100">
                            <!-- Task Card Preview -->
                            <div class="space-y-4">
                                <div class="flex items-center space-x-3">
                                    <div
                                        class="w-12 h-12 gradient-bg rounded-full flex items-center justify-center shadow-md">
                                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M5 13l4 4L19 7" />
                                        </svg>
                                    </div>
                                    <div class="flex-1">
                                        <div class="h-4 bg-gray-200 rounded w-3/4 mb-2"></div>
                                        <div class="h-3 bg-gray-100 rounded w-1/2"></div>
                                    </div>
                                </div>
                                <div class="flex items-center space-x-3 opacity-75">
                                    <div class="w-12 h-12 bg-violet-100 rounded-full flex items-center justify-center">
                                        <svg class="w-6 h-6 text-violet-600" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                    </div>
                                    <div class="flex-1">
                                        <div class="h-4 bg-gray-200 rounded w-2/3 mb-2"></div>
                                        <div class="h-3 bg-gray-100 rounded w-1/3"></div>
                                    </div>
                                </div>
                                <div class="flex items-center space-x-3 opacity-50">
                                    <div class="w-12 h-12 bg-gray-100 rounded-full flex items-center justify-center">
                                        <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                    </div>
                                    <div class="flex-1">
                                        <div class="h-4 bg-gray-200 rounded w-4/5 mb-2"></div>
                                        <div class="h-3 bg-gray-100 rounded w-2/5"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Floating Elements -->
                    <div
                        class="absolute -top-4 -right-4 w-16 h-16 bg-yellow-400 rounded-full flex items-center justify-center shadow-lg pulse-animation">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                    </div>
                    <div
                        class="absolute -bottom-4 -left-4 w-16 h-16 bg-purple-500 rounded-full flex items-center justify-center shadow-lg pulse-animation animation-delay-1000">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section id="fitur" class="py-16 md:py-24 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <div class="inline-block px-4 py-1 bg-violet-100 text-violet-800 rounded-full text-sm font-medium mb-4">
                    Fitur Unggulan
                </div>
                <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">
                    Raih <span class="gradient-text">Penghasilan Tambahan</span> dengan Mudah
                </h2>
                <p class="text-lg text-gray-600 max-w-2xl mx-auto">
                    Platform terpercaya untuk mendapatkan uang dari menyelesaikan tugas-tugas sederhana
                </p>
            </div>

            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
                <!-- Feature 1 -->
                <div class="feature-card card-hover relative">
                    <div class="w-14 h-14 gradient-bg rounded-lg flex items-center justify-center mb-6 shadow-md">
                        <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-3">Bayaran Jelas & Transparan</h3>
                    <p class="text-gray-600">
                        Kamu tahu berapa yang kamu dapat sebelum mulai.
                    </p>
                </div>

                <!-- Feature 2 -->
                <div class="feature-card card-hover relative">
                    <div class="w-14 h-14 gradient-bg rounded-lg flex items-center justify-center mb-6 shadow-md">
                        <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-3">Tugas Beragam & Mudah</h3>
                    <p class="text-gray-600">
                        Kamu hanya perlu memasukkan kontak ke grup WhatsApp. Semua kontak sudah kami siapkan dalam
                        bentuk file — cukup export dan tambahkan ke kontak grup tanpa harus mencari satu per satu.
                    </p>
                </div>

                <!-- Feature 3 -->
                <div class="feature-card card-hover relative">
                    <div class="w-14 h-14 gradient-bg rounded-lg flex items-center justify-center mb-6 shadow-md">
                        <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-3">Waktu Fleksibel</h3>
                    <p class="text-gray-600">
                        Bisa kapan saja, tidak ada target harian.
                    </p>
                </div>

                <!-- Feature 4 -->
                <div class="feature-card card-hover relative">
                    <div class="w-14 h-14 gradient-bg rounded-lg flex items-center justify-center mb-6 shadow-md">
                        <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-3">Penarikan Mudah</h3>
                    <p class="text-gray-600">
                        Tarik penghasilan Anda ke rekening bank, e-wallet, atau pulsa dengan proses yang cepat dan aman.
                    </p>
                </div>

                <!-- Feature 5 -->
                <div class="feature-card card-hover relative">
                    <div class="w-14 h-14 gradient-bg rounded-lg flex items-center justify-center mb-6 shadow-md">
                        <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-3">Bonus & Reward</h3>
                    <p class="text-gray-600">
                        Invite lebih banyak? Cuan lebih banyak!
                    </p>
                </div>

                <!-- Feature 6 -->
                <div class="feature-card card-hover relative">
                    <div class="w-14 h-14 gradient-bg rounded-lg flex items-center justify-center mb-6 shadow-md">
                        <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-3">Aman & Terpercaya</h3>
                    <p class="text-gray-600">
                        Tidak perlu takut tidak dibayar — sistem otomatis kami memastikan setiap tugas valid langsung
                        dibayar.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- Testimonials Section -->
    <section class="py-16 md:py-24 bg-violet-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <div class="inline-block px-4 py-1 bg-violet-100 text-violet-800 rounded-full text-sm font-medium mb-4">
                    Testimoni Pengguna
                </div>
                <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">
                    Apa Kata <span class="gradient-text">Mereka</span>
                </h2>
                <p class="text-lg text-gray-600 max-w-2xl mx-auto">
                    Dengarkan pengalaman langsung dari pengguna CuanInstan
                </p>
            </div>

            <div class="grid md:grid-cols-3 gap-8">
                <!-- Testimonial 1 -->
                <div class="testimonial-card card-hover">
                    <div class="flex items-center mb-4">
                        <div
                            class="w-12 h-12 rounded-full bg-gradient-to-r from-pink-400 to-purple-500 flex items-center justify-center text-white font-bold text-lg mr-4">
                            DN
                        </div>
                        <div>
                            <h4 class="font-bold text-gray-900">Dina Nurhaliza</h4>
                            <p class="text-sm text-gray-600">Ibu Rumah Tangga</p>
                        </div>
                    </div>
                    <div class="flex mb-3 space-x-1">
                        <svg class="w-5 h-5 text-yellow-400 fill-current" viewBox="0 0 20 20">
                            <path
                                d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z" />
                        </svg>
                        <svg class="w-5 h-5 text-yellow-400 fill-current" viewBox="0 0 20 20">
                            <path
                                d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z" />
                        </svg>
                        <svg class="w-5 h-5 text-yellow-400 fill-current" viewBox="0 0 20 20">
                            <path
                                d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z" />
                        </svg>
                        <svg class="w-5 h-5 text-yellow-400 fill-current" viewBox="0 0 20 20">
                            <path
                                d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z" />
                        </svg>
                        <svg class="w-5 h-5 text-yellow-400 fill-current" viewBox="0 0 20 20">
                            <path
                                d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z" />
                        </svg>
                    </div>
                    <p class="text-gray-600">
                        "Alhamdulillah, setiap bulan bisa dapat tambahan Rp 1-2 juta dari mengerjakan tugas di waktu
                        luang. Sangat membantu kebutuhan keluarga!"
                    </p>
                </div>

                <!-- Testimonial 2 -->
                <div class="testimonial-card card-hover">
                    <div class="flex items-center mb-4">
                        <div
                            class="w-12 h-12 rounded-full bg-gradient-to-r from-violet-400 to-violet-600 flex items-center justify-center text-white font-bold text-lg mr-4">
                            AR
                        </div>
                        <div>
                            <h4 class="font-bold text-gray-900">Andi Rizkianto</h4>
                            <p class="text-sm text-gray-600">Mahasiswa</p>
                        </div>
                    </div>
                    <div class="flex mb-3 space-x-1">
                        <svg class="w-5 h-5 text-yellow-400 fill-current" viewBox="0 0 20 20">
                            <path
                                d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z" />
                        </svg>
                        <svg class="w-5 h-5 text-yellow-400 fill-current" viewBox="0 0 20 20">
                            <path
                                d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z" />
                        </svg>
                        <svg class="w-5 h-5 text-yellow-400 fill-current" viewBox="0 0 20 20">
                            <path
                                d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z" />
                        </svg>
                        <svg class="w-5 h-5 text-yellow-400 fill-current" viewBox="0 0 20 20">
                            <path
                                d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z" />
                        </svg>
                        <svg class="w-5 h-5 text-yellow-400 fill-current" viewBox="0 0 20 20">
                            <path
                                d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z" />
                        </svg>
                    </div>
                    <p class="text-gray-600">
                        "Sebagai mahasiswa, ini solusi sempurna untuk dapat uang jajan. Bisa dikerjakan di sela-sela
                        kuliah, bayarannya juga cepat cair!"
                    </p>
                </div>

                <!-- Testimonial 3 -->
                <div class="testimonial-card card-hover">
                    <div class="flex items-center mb-4">
                        <div
                            class="w-12 h-12 rounded-full bg-gradient-to-r from-violet-500 to-violet-700 flex items-center justify-center text-white font-bold text-lg mr-4">
                            SP
                        </div>
                        <div>
                            <h4 class="font-bold text-gray-900">Siti Permata</h4>
                            <p class="text-sm text-gray-600">Karyawan Swasta</p>
                        </div>
                    </div>
                    <div class="flex mb-3 space-x-1">
                        <svg class="w-5 h-5 text-yellow-400 fill-current" viewBox="0 0 20 20">
                            <path
                                d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z" />
                        </svg>
                        <svg class="w-5 h-5 text-yellow-400 fill-current" viewBox="0 0 20 20">
                            <path
                                d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z" />
                        </svg>
                        <svg class="w-5 h-5 text-yellow-400 fill-current" viewBox="0 0 20 20">
                            <path
                                d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z" />
                        </svg>
                        <svg class="w-5 h-5 text-yellow-400 fill-current" viewBox="0 0 20 20">
                            <path
                                d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z" />
                        </svg>
                        <svg class="w-5 h-5 text-yellow-400 fill-current" viewBox="0 0 20 20">
                            <path
                                d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z" />
                        </svg>
                    </div>
                    <p class="text-gray-600">
                        "Penghasilan dari kantor pas-pasan, untung ada CuanInstan. Sekarang bisa nabung untuk liburan
                        keluarga dari penghasilan tambahan ini!"
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- How It Works Section -->
    <section class="py-16 md:py-24 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <div class="inline-block px-4 py-1 bg-violet-100 text-violet-800 rounded-full text-sm font-medium mb-4">
                    Cara Kerja
                </div>
                <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">
                    Mulai dalam <span class="gradient-text">3 Langkah Mudah</span>
                </h2>
                <p class="text-lg text-gray-600 max-w-2xl mx-auto">
                    Proses sederhana untuk mulai menghasilkan uang dari rumah
                </p>
            </div>

            <div class="grid md:grid-cols-3 gap-8 md:gap-12">
                <!-- Step 1 -->
                <div class="text-center relative">
                    <div class="relative mb-6">
                        <div
                            class="w-20 h-20 mx-auto gradient-bg rounded-full flex items-center justify-center shadow-lg">
                            <span class="text-3xl font-bold text-white">1</span>
                        </div>
                        <div class="hidden md:block step-connector"></div>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-3">Daftar Gratis</h3>
                    <p class="text-gray-600">
                        Buat akun Anda dalam hitungan detik. Gratis tanpa biaya pendaftaran.
                    </p>
                </div>

                <!-- Step 2 -->
                <div class="text-center relative">
                    <div class="relative mb-6">
                        <div
                            class="w-20 h-20 mx-auto gradient-bg rounded-full flex items-center justify-center shadow-lg">
                            <span class="text-3xl font-bold text-white">2</span>
                        </div>
                        <div class="hidden md:block step-connector"></div>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-3">Pilih & Kerjakan Tugas</h3>
                    <p class="text-gray-600">
                        Pilih tugas yang tersedia, tambahkan kontak yang sudah disediakan ke grup WhatsApp sesuai
                        instruksi, lalu upload bukti dan tugas selesai.
                    </p>
                </div>

                <!-- Step 3 -->
                <div class="text-center relative">
                    <div class="relative mb-6">
                        <div
                            class="w-20 h-20 mx-auto gradient-bg rounded-full flex items-center justify-center shadow-lg">
                            <span class="text-3xl font-bold text-white">3</span>
                        </div>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-3">Terima Bayaran</h3>
                    <p class="text-gray-600">
                        Jika tugas berhasil diverifikasi dan valid, saldo otomatis dikirim ke rekening atau e-wallet
                        pilihan Anda.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="py-16 md:py-24 gradient-bg relative overflow-hidden">
        <!-- Background Elements -->
        <div class="absolute top-0 left-0 w-full h-full opacity-10">
            <div class="absolute top-10 left-10 w-72 h-72 bg-white rounded-full"></div>
            <div class="absolute bottom-10 right-10 w-72 h-72 bg-white rounded-full"></div>
        </div>

        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center relative z-10">
            <div
                class="inline-block px-4 py-1 bg-white/20 backdrop-blur-sm text-white rounded-full text-sm font-medium mb-4">
                Mulai Sekarang
            </div>
            <h2 class="text-3xl md:text-4xl font-bold text-white mb-6">
                Siap Mulai Hasilkan Uang Tambahan?
            </h2>
            <p class="text-lg md:text-xl text-white/90 mb-8 max-w-2xl mx-auto">
                Bergabunglah dengan ribuan worker yang sudah mendapatkan penghasilan tambahan dari CuanInstan
            </p>
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <?php if(Route::has('register')): ?>
                    <a href="<?php echo e(route('register')); ?>"
                        class="btn-primary text-lg bg-white text-violet-600 hover:bg-gray-100">
                        Mulai Gratis Sekarang
                    </a>
                <?php endif; ?>
                <?php if(Route::has('login')): ?>
                    <a href="<?php echo e(route('login')); ?>" class="btn-primary text-lg bg-white text-violet-600 hover:bg-gray-100">
                        Sudah Punya Akun? Masuk
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-gray-900 text-white py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid md:grid-cols-4 gap-8">
                <!-- Brand -->
                <div class="md:col-span-2">
                    <div class="flex items-center space-x-2 mb-4">
                        <div class="w-10 h-10 gradient-bg rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                            </svg>
                        </div>
                        <span class="text-xl font-bold">CuanInstan</span>
                    </div>
                    <p class="text-gray-400 mb-4 max-w-md">
                        Platform manajemen tugas terpercaya yang membantu Anda tetap produktif dan terorganisir.
                    </p>
                    <div class="flex space-x-4">
                        <a href="#"
                            class="w-10 h-10 rounded-full bg-gray-800 flex items-center justify-center hover:bg-violet-600 transition">
                            <svg class="w-5 h-5 fill-current" viewBox="0 0 24 24">
                                <path
                                    d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z" />
                            </svg>
                        </a>
                        <a href="#"
                            class="w-10 h-10 rounded-full bg-gray-800 flex items-center justify-center hover:bg-violet-600 transition">
                            <svg class="w-5 h-5 fill-current" viewBox="0 0 24 24">
                                <path
                                    d="M23.953 4.57a10 10 0 01-2.825.775 4.958 4.958 0 002.163-2.723c-.951.555-2.005.959-3.127 1.184a4.92 4.92 0 00-8.384 4.482C7.69 8.095 4.067 6.13 1.64 3.162a4.822 4.822 0 00-.666 2.475c0 1.71.87 3.213 2.188 4.096a4.904 4.904 0 01-2.228-.616v.06a4.923 4.923 0 003.946 4.827 4.996 4.996 0 01-2.212.085 4.936 4.936 0 004.604 3.417 9.867 9.867 0 01-6.102 2.105c-.39 0-.779-.023-1.17-.067a13.995 13.995 0 007.557 2.209c9.053 0 13.998-7.496 13.998-13.985 0-.21 0-.42-.015-.63A9.935 9.935 0 0024 4.59z" />
                            </svg>
                        </a>
                        <a href="#"
                            class="w-10 h-10 rounded-full bg-gray-800 flex items-center justify-center hover:bg-violet-600 transition">
                            <svg class="w-5 h-5 fill-current" viewBox="0 0 24 24">
                                <path
                                    d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z" />
                            </svg>
                        </a>
                        <a href="#"
                            class="w-10 h-10 rounded-full bg-gray-800 flex items-center justify-center hover:bg-violet-600 transition">
                            <svg class="w-5 h-5 fill-current" viewBox="0 0 24 24">
                                <path
                                    d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433c-1.144 0-2.063-.926-2.063-2.065 0-1.138.92-2.063 2.063-2.063 1.14 0 2.064.925 2.064 2.063 0 1.139-.925 2.065-2.064 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z" />
                            </svg>
                        </a>
                    </div>
                </div>

                <!-- Quick Links -->
                <div>
                    <h4 class="font-bold mb-4">Platform</h4>
                    <ul class="space-y-2 text-gray-400">
                        <?php if(Route::has('login')): ?>
                            <?php if(auth()->guard()->check()): ?>
                                <li><a href="<?php echo e(url('/dashboard')); ?>" class="hover:text-white transition">Dashboard</a></li>
                            <?php else: ?>
                                <li><a href="<?php echo e(route('login')); ?>" class="hover:text-white transition">Masuk</a></li>
                                <?php if(Route::has('register')): ?>
                                    <li><a href="<?php echo e(route('register')); ?>" class="hover:text-white transition">Daftar</a></li>
                                <?php endif; ?>
                            <?php endif; ?>
                        <?php endif; ?>
                        <li><a href="#fitur" class="hover:text-white transition">Fitur</a></li>
                    </ul>
                </div>

                <!-- Support -->
                <div>
                    <h4 class="font-bold mb-4">Dukungan</h4>
                    <ul class="space-y-2 text-gray-400">
                        <li><a href="#" class="hover:text-white transition">Bantuan</a></li>
                        <li><a href="#" class="hover:text-white transition">Kontak</a></li>
                        <li><a href="#" class="hover:text-white transition">Privacy Policy</a></li>
                        <li><a href="#" class="hover:text-white transition">Terms of Service</a></li>
                    </ul>
                </div>
            </div>

            <div class="border-t border-gray-800 mt-8 pt-8 text-center text-gray-400">
                <p>&copy; <?php echo e(date('Y')); ?> CuanInstan. Semua hak dilindungi.</p>
            </div>
        </div>
    </footer>

    <script>
        // Mobile menu toggle
        const mobileMenuButton = document.getElementById('mobile-menu-button');
        const mobileMenu = document.getElementById('mobile-menu');

        mobileMenuButton.addEventListener('click', () => {
            mobileMenu.classList.toggle('hidden');
        });

        // Smooth scroll
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                    // Close mobile menu if open
                    mobileMenu.classList.add('hidden');
                }
            });
        });

        // Add animation delay to floating elements
        const style = document.createElement('style');
        style.textContent = `
            @keyframes blob {
                0% { transform: translate(0px, 0px) scale(1); }
                33% { transform: translate(30px, -50px) scale(1.1); }
                66% { transform: translate(-20px, 20px) scale(0.9); }
                100% { transform: translate(0px, 0px) scale(1); }
            }
            .animate-blob {
                animation: blob 7s infinite;
            }
            .animation-delay-2000 {
                animation-delay: 2s;
            }
            .animation-delay-1000 {
                animation-delay: 1s;
            }
        `;
        document.head.appendChild(style);
    </script>
</body>

</html><?php /**PATH C:\laragon\www\baru\resources\views/welcome.blade.php ENDPATH**/ ?>