<?php

use App\Rules\ReCaptcha;
use Illuminate\Auth\Events\Lockout;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Validate;
use Livewire\Volt\Component;

new #[Layout('components.layouts.auth')] class extends Component {
    #[Validate('required|string|email')]
    public string $email = '';

    #[Validate('required|string')]
    public string $password = '';

    public bool $remember = false;

    public ?string $recaptcha_token = null;

    /**
     * Handle an incoming authentication request.
     */
    public function login(): void
    {
        $this->validate([
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
            'recaptcha_token' => [new ReCaptcha('login')],
        ]);

        $this->ensureIsNotRateLimited();

        if (!Auth::attempt(['email' => $this->email, 'password' => $this->password], $this->remember)) {
            RateLimiter::hit($this->throttleKey());

            throw ValidationException::withMessages([
                'email' => __('auth.failed'),
            ]);
        }

        RateLimiter::clear($this->throttleKey());
        Session::regenerate();

        // Hard navigation => browser full reload
        // If authenticated user is an admin, redirect to the Filament admin panel
        $defaultRedirect = Auth::user() && isset(Auth::user()->role) && Auth::user()->role === 'admin'
            ? url('/admin')
            : route('dashboard', absolute: true);

        $this->redirectIntended(
            default: $defaultRedirect,
            navigate: false  // or remove the navigate param
        );
    }

    /**
     * Ensure the authentication request is not rate limited.
     */
    protected function ensureIsNotRateLimited(): void
    {
        if (!RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
            return;
        }

        event(new Lockout(request()));

        $seconds = RateLimiter::availableIn($this->throttleKey());

        throw ValidationException::withMessages([
            'email' => __('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ]),
        ]);
    }

    /**
     * Get the authentication rate limiting throttle key.
     */
    protected function throttleKey(): string
    {
        return Str::transliterate(Str::lower($this->email) . '|' . request()->ip());
    }
}; ?>

<div class="flex flex-col gap-8">
    <!-- Modern Header with Icon -->
    <div class="text-center space-y-4">
        <div
            class="mx-auto w-20 h-20 bg-gradient-to-br from-violet-500 to-violet-700 rounded-3xl flex items-center justify-center shadow-xl shadow-violet-500/40 transform hover:scale-105 transition-transform duration-300">
            <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
            </svg>
        </div>
        <div class="space-y-2">
            <h1 class="text-3xl font-bold bg-gradient-to-r from-zinc-900 to-zinc-700 bg-clip-text text-transparent">
                Selamat Datang!</h1>
            <p class="text-sm text-zinc-600">Masuk untuk melanjutkan penghasilan Anda</p>
        </div>
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="text-center" :status="session('status')" />

    <form method="POST" wire:submit="login" class="flex flex-col gap-6">
        <!-- Email Address -->
        <div class="space-y-2">
            <flux:label class="flex items-center gap-2 text-sm font-semibold text-zinc-700">
                <svg class="w-4 h-4 text-violet-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207">
                    </path>
                </svg>
                <span>Email Address</span>
            </flux:label>
            <flux:input wire:model="email" type="email" required autofocus autocomplete="email"
                placeholder="nama@email.com" />
        </div>

        <!-- Password -->
        <div class="space-y-2">
            <div class="flex items-center justify-between">
                <flux:label class="flex items-center gap-2 text-sm font-semibold text-zinc-700">
                    <svg class="w-4 h-4 text-violet-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z">
                        </path>
                    </svg>
                    <span>Password</span>
                </flux:label>
                @if (Route::has('password.request'))
                    <flux:link class="text-xs text-violet-600 hover:text-violet-700 font-medium transition-colors"
                        :href="route('password.request')" wire:navigate>
                        Lupa password?
                    </flux:link>
                @endif
            </div>
            <flux:input wire:model="password" type="password" required autocomplete="current-password"
                placeholder="Masukkan password Anda" viewable />
        </div>

        <!-- Remember Me -->
        <div class="flex items-center">
            <flux:checkbox wire:model="remember" :label="__('Ingat saya selama 30 hari')" />
        </div>

        <!-- Hidden reCAPTCHA Token -->
        <input type="hidden" wire:model="recaptcha_token" id="recaptcha_token_login">

        <!-- Submit Button -->
        <div
            class="pt-3 flex justify-center sticky bottom-0 bg-white backdrop-blur py-3 -mx-6 sm:-mx-10 px-6 sm:px-10 border-t border-zinc-100">
            <flux:button variant="primary" type="submit" class="w-full bg-gradient-to-r from-violet-600 to-violet-700 
                    hover:from-violet-700 hover:to-violet-800 
                    text-white font-semibold py-3.5 rounded-xl 
                    transition-all duration-300 
                    shadow-lg shadow-violet-500/30 
                    hover:shadow-xl hover:shadow-violet-600/40 
                    hover:-translate-y-0.5 active:translate-y-0
                    flex items-center justify-center gap-2 group text-center">

                <span class="leading-none">Masuk Sekarang</span>
            </flux:button>
        </div>
    </form>

    <!-- reCAPTCHA v3 Script -->
    @if(config('recaptcha.site_key'))
        <script src="https://www.google.com/recaptcha/api.js?render={{ config('recaptcha.site_key') }}"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const form = document.querySelector('form');
                form.addEventListener('submit', function (e) {
                    e.preventDefault();
                    grecaptcha.ready(function () {
                        grecaptcha.execute('{{ config('recaptcha.site_key') }}', { action: 'login' }).then(function (token) {
                            @this.set('recaptcha_token', token);
                            @this.login();
                        });
                    });
                });
            });
        </script>
    @endif

    @if (Route::has('register'))
        <div class="relative my-2">
            <div class="absolute inset-0 flex items-center">
                <div class="w-full border-t border-zinc-200"></div>
            </div>
            <div class="relative flex justify-center text-xs uppercase">
                <span class="bg-white px-3 text-zinc-500 font-medium">Atau</span>
            </div>
        </div>
        <div class="text-center space-y-4 sm:flex sm:items-center sm:justify-between sm:gap-4 sm:space-y-0">
            <p class="text-sm text-zinc-600 sm:mb-0">Belum punya akun?</p>
            <flux:link
                class="group inline-flex items-center justify-center gap-2 w-full sm:w-auto px-5 py-3.5 sm:px-4 sm:py-2.5 bg-gradient-to-r from-violet-600 to-violet-700 hover:from-violet-700 hover:to-violet-800 text-white font-semibold rounded-xl transition-all duration-300 shadow-lg shadow-violet-500/30 hover:shadow-xl hover:shadow-violet-600/40 hover:-translate-y-0.5 active:translate-y-0 flex items-center justify-center gap-2"
                :href="route('register')" wire:navigate>
                <svg class="w-5 h-5 group-hover:rotate-90 transition-transform duration-300" fill="none"
                    stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path>
                </svg>
                <span>Daftar Akun Gratis</span>
            </flux:link>
        </div>
    @endif
</div>