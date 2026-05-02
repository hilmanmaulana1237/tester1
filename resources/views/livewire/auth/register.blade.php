<?php

use App\Models\User;
use App\Rules\ReCaptcha;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('components.layouts.auth')] class extends Component {
    public string $name = '';
    public string $email = '';
    public string $password = '';
    public string $password_confirmation = '';
    public string $ewallet_type = '';
    public string $ewallet_number = '';
    public string $ewallet_name = '';
    public string $phone = '';
    public string $whatsapp = '';
    public ?string $recaptcha_token = null;

    /**
     * Handle an incoming registration request.
     */
    public function register(): void
    {
        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:' . User::class],
            'password' => ['required', 'string', 'confirmed', Rules\Password::defaults()],
            'ewallet_type' => ['required', 'string', 'in:gopay,ovo,dana,shopeepay,linkaja'],
            'ewallet_number' => ['required', 'string', 'max:20'],
            'ewallet_name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:20'],
            'whatsapp' => ['nullable', 'string', 'max:20'],
            'recaptcha_token' => ['nullable', new ReCaptcha('register')],
        ], [
            'email.unique' => 'Email ini sudah ada. Silakan login.'
        ]);

        unset($validated['recaptcha_token']);

        $validated['password'] = Hash::make($validated['password']);

        event(new Registered(($user = User::create($validated))));

        Auth::login($user);

        // Hard navigation => browser full reload
        $this->redirectIntended(
            default: route('dashboard', absolute: true),
            navigate: false  // atau hapus argumen navigate sama sekali
        );
    }
}; ?>

<div class="flex flex-col gap-6">
    <x-auth-header :title="__('Buat Akun Baru')" :description="__('Daftar sekarang dan mulai hasilkan uang tambahan')" />

    <!-- Session Status -->
    <x-auth-session-status class="text-center" :status="session('status')" />

    <form method="POST" wire:submit="register" class="flex flex-col gap-6">
        <!-- Name -->
        <flux:input wire:model="name" :label="__('Nama Lengkap')" type="text" required autofocus autocomplete="name"
            :placeholder="__('Masukkan nama lengkap')" />

        <!-- Email Address -->
        <flux:input wire:model="email" :label="__('Email')" type="email" required autocomplete="email"
            placeholder="email@example.com" />

        <!-- E-Wallet Type -->
        <div>
            <flux:label>{{ __('Jenis E-Wallet') }}</flux:label>
            <select wire:model="ewallet_type" required
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-violet-500 focus:border-violet-500">
                <option value="">Pilih E-Wallet</option>
                <option value="gopay">GoPay</option>
                <option value="ovo">OVO</option>
                <option value="dana">DANA</option>
                <option value="shopeepay">ShopeePay</option>
                <option value="linkaja">LinkAja</option>
            </select>
            @error('ewallet_type') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
            <p class="mt-2 text-xs text-zinc-600">
                {{ __('Jenis e-wallet ini akan digunakan sebagai tujuan pengiriman uang ketika tugas selesai. Pastikan nomor dan nama e-wallet benar agar pembayaran berhasil.') }}
            </p>
        </div>

        <!-- E-Wallet Number -->
        <flux:input wire:model="ewallet_number" :label="__('Nomor E-Wallet')" type="text" required
            :placeholder="__('Contoh: 081234567890')" inputmode="numeric" />

        <!-- E-Wallet Account Name -->
        <flux:input wire:model="ewallet_name" :label="__('Nama Pemilik E-Wallet')" type="text" required
            :placeholder="__('Sesuai dengan nama di E-Wallet')" />

        <!-- Phone Number -->
        <flux:input wire:model="phone" :label="__('Nomor HP / Telepon')" type="tel" required
            :placeholder="__('Contoh: 081234567890')" inputmode="tel" />

        <!-- WhatsApp Number -->
        <flux:input wire:model="whatsapp" :label="__('Nomor WhatsApp (Opsional)')" type="tel"
            :placeholder="__('Kosongkan jika sama dengan nomor HP')" inputmode="tel" />

        <!-- Password -->
        <flux:input wire:model="password" :label="__('Password')" type="password" required autocomplete="new-password"
            :placeholder="__('Minimal 8 karakter')" viewable />

        <!-- Confirm Password -->
        <flux:input wire:model="password_confirmation" :label="__('Konfirmasi Password')" type="password" required
            autocomplete="new-password" :placeholder="__('Ulangi password')" viewable />

        <!-- Hidden reCAPTCHA Token -->
        <input type="hidden" wire:model="recaptcha_token" id="recaptcha_token_register">

        <div
            class="flex items-center justify-end sticky bottom-0 bg-white backdrop-blur py-3 -mx-6 sm:-mx-10 px-6 sm:px-10 border-t border-zinc-100">
            <flux:button type="submit" variant="primary"
                class="w-full bg-gradient-to-r from-violet-600 to-violet-700 hover:from-violet-700 hover:to-violet-800 text-white font-medium py-3 rounded-lg transition-all duration-200 shadow-lg hover:shadow-xl shadow-violet-500/30 hover:shadow-violet-600/40">
                {{ __('Daftar Gratis') }}
            </flux:button>
        </div>
    </form>

    <!-- reCAPTCHA v3 Script -->
    @if(config('recaptcha.site_key'))
        <script src="https://www.google.com/recaptcha/api.js?render={{ config('recaptcha.site_key') }}"></script>
        <script>
            // Generate token in background saat halaman load
            grecaptcha.ready(function () {
                grecaptcha.execute('{{ config('recaptcha.site_key') }}', { action: 'register' }).then(function (token) {
                    @this.set('recaptcha_token', token);
                });
            });

            // Refresh token setiap 90 detik (token valid 2 menit)
            setInterval(function () {
                grecaptcha.ready(function () {
                    grecaptcha.execute('{{ config('recaptcha.site_key') }}', { action: 'register' }).then(function (token) {
                        @this.set('recaptcha_token', token);
                    });
                });
            }, 90000);
        </script>
    @endif

    <div class="space-x-1 rtl:space-x-reverse text-center text-sm text-zinc-600">
        <span>{{ __('Sudah punya akun?') }}</span>
        <flux:link class="text-violet-600 hover:text-violet-700 font-medium transition" :href="route('login')"
            wire:navigate>{{ __('Masuk di sini') }}</flux:link>
    </div>
</div>