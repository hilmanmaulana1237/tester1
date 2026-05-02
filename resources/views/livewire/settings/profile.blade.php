<?php

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\Rule;
use Livewire\Volt\Component;

new class extends Component {
    public string $name = '';
    public string $email = '';
    public ?string $phone = null;
    public ?string $whatsapp = null;
    public ?string $ewallet_type = null;
    public ?string $ewallet_number = null;
    public ?string $ewallet_name = null;

    /**
     * Mount the component.
     */
    public function mount(): void
    {
        $this->name = Auth::user()->name;
        $this->email = Auth::user()->email;
        $this->phone = Auth::user()->phone;
        $this->whatsapp = Auth::user()->whatsapp;
        $this->ewallet_type = Auth::user()->ewallet_type;
        $this->ewallet_number = Auth::user()->ewallet_number;
        $this->ewallet_name = Auth::user()->ewallet_name;
    }

    /**
     * Update the profile information for the currently authenticated user.
     */
    public function updateProfileInformation(): void
    {
        $user = Auth::user();

        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255'],

            'email' => [
                'required',
                'string',
                'lowercase',
                'email',
                'max:255',
                Rule::unique(User::class)->ignore($user->id)
            ],
            
            'phone' => ['nullable', 'string', 'max:20'],
            'whatsapp' => ['nullable', 'string', 'max:20'],
            'ewallet_type' => ['nullable', 'string', Rule::in(array_keys(User::EWALLETS))],
            'ewallet_number' => ['nullable', 'string', 'max:20'],
            'ewallet_name' => ['nullable', 'string', 'max:255'],
        ]);

        $user->fill($validated);

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();

        $this->dispatch('profile-updated', name: $user->name);
    }

    /**
     * Send an email verification notification to the current user.
     */
    public function resendVerificationNotification(): void
    {
        $user = Auth::user();

        if ($user->hasVerifiedEmail()) {
            $this->redirectIntended(default: route('dashboard', absolute: false));

            return;
        }

        $user->sendEmailVerificationNotification();

        Session::flash('status', 'verification-link-sent');
    }
}; ?>

<div class="min-h-screen bg-gradient-to-b from-violet-50/50 to-white pb-24">
    <!-- Header -->
    <div class="bg-gradient-to-r from-violet-600 to-purple-600 text-white px-4 pt-6 pb-8">
        <h1 class="text-2xl font-bold mb-1">Profil Saya</h1>
        <p class="text-violet-100 text-sm">Kelola informasi akun Anda</p>
    </div>

    <div class="max-w-2xl mx-auto px-4 -mt-4">
        <form wire:submit="updateProfileInformation" class="space-y-4">
            
            <!-- Personal Info Card -->
            <div class="bg-white rounded-2xl shadow-sm border border-violet-100 overflow-hidden">
                <div class="bg-gradient-to-r from-violet-50 to-purple-50 px-4 py-3 border-b border-violet-100">
                    <h2 class="font-semibold text-zinc-800 flex items-center gap-2">
                        <span>👤</span>
                        <span>Informasi Pribadi</span>
                    </h2>
                </div>
                <div class="p-4 space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-zinc-700 mb-1.5">Nama Lengkap</label>
                        <input type="text" wire:model="name" required
                            class="w-full px-4 py-2.5 border border-zinc-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-violet-500 focus:border-transparent transition-all"
                            placeholder="Masukkan nama lengkap">
                        @error('name') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-zinc-700 mb-1.5">Email</label>
                        <input type="email" wire:model="email" required
                            class="w-full px-4 py-2.5 border border-zinc-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-violet-500 focus:border-transparent transition-all"
                            placeholder="email@example.com">
                        @error('email') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>
            </div>

            <!-- Contact Info Card -->
            <div class="bg-white rounded-2xl shadow-sm border border-violet-100 overflow-hidden">
                <div class="bg-gradient-to-r from-violet-50 to-purple-50 px-4 py-3 border-b border-violet-100">
                    <h2 class="font-semibold text-zinc-800 flex items-center gap-2">
                        <span>📞</span>
                        <span>Kontak</span>
                    </h2>
                    <p class="text-xs text-zinc-600 mt-0.5">Untuk komunikasi terkait task</p>
                </div>
                <div class="p-4 space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-zinc-700 mb-1.5">Nomor Telepon</label>
                        <input type="tel" wire:model="phone"
                            class="w-full px-4 py-2.5 border border-zinc-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-violet-500 focus:border-transparent transition-all"
                            placeholder="08123456789">
                        @error('phone') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-zinc-700 mb-1.5 flex items-center gap-1.5">
                            <svg class="w-4 h-4 text-green-600" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893A11.821 11.821 0 0020.885 3.148z"/></svg>
                            WhatsApp
                        </label>
                        <input type="tel" wire:model="whatsapp"
                            class="w-full px-4 py-2.5 border border-zinc-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-violet-500 focus:border-transparent transition-all"
                            placeholder="628123456789">
                        <p class="text-xs text-zinc-500 mt-1">Format: 628xxx (dengan kode negara)</p>
                        @error('whatsapp') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>
            </div>

            <!-- E-Wallet Card -->
            <div class="bg-white rounded-2xl shadow-sm border border-violet-100 overflow-hidden">
                <div class="bg-gradient-to-r from-violet-50 to-purple-50 px-4 py-3 border-b border-violet-100">
                    <h2 class="font-semibold text-zinc-800 flex items-center gap-2">
                        <span>💰</span>
                        <span>E-Wallet</span>
                    </h2>
                    <p class="text-xs text-zinc-600 mt-0.5">Untuk pencairan pembayaran</p>
                </div>
                <div class="p-4 space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-zinc-700 mb-1.5">Jenis E-Wallet</label>
                        <select wire:model="ewallet_type"
                            class="w-full px-4 py-2.5 border border-zinc-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-violet-500 focus:border-transparent transition-all">
                            <option value="">Pilih e-wallet...</option>
                            @foreach(\App\Models\User::EWALLETS as $key => $label)
                                <option value="{{ $key }}">
                                    @switch($key)
                                        @case('gopay') 💚 @break
                                        @case('ovo') 💜 @break
                                        @case('dana') 💙 @break
                                        @case('shopeepay') 🧡 @break
                                        @case('linkaja') ❤️ @break
                                    @endswitch
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                        @error('ewallet_type') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-zinc-700 mb-1.5">Nomor E-Wallet</label>
                        <input type="text" wire:model="ewallet_number"
                            class="w-full px-4 py-2.5 border border-zinc-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-violet-500 focus:border-transparent transition-all"
                            placeholder="08123456789">
                        @error('ewallet_number') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-zinc-700 mb-1.5">Nama Pemilik</label>
                        <input type="text" wire:model="ewallet_name"
                            class="w-full px-4 py-2.5 border border-zinc-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-violet-500 focus:border-transparent transition-all"
                            placeholder="Nama sesuai e-wallet">
                        @error('ewallet_name') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                    </div>

                    @if($ewallet_type && $ewallet_number)
                        <div class="bg-violet-50 border border-violet-200 rounded-xl p-3">
                            <div class="flex items-start gap-2">
                                <svg class="w-5 h-5 text-violet-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-medium text-violet-900">E-Wallet Terkonfigurasi</p>
                                    <p class="text-xs text-violet-700 mt-0.5">
                                        {{ \App\Models\User::EWALLETS[$ewallet_type] ?? $ewallet_type }} - {{ $ewallet_number }}
                                        @if($ewallet_name)<br>a/n {{ $ewallet_name }}@endif
                                    </p>
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="bg-amber-50 border border-amber-200 rounded-xl p-3">
                            <div class="flex items-start gap-2">
                                <svg class="w-5 h-5 text-amber-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                                </svg>
                                <div class="flex-1">
                                    <p class="text-sm font-medium text-amber-900">E-Wallet Belum Diatur</p>
                                    <p class="text-xs text-amber-700 mt-0.5">Silakan lengkapi untuk menerima pembayaran</p>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Save Button -->
            <div class="sticky bottom-20 pt-2">
                <button type="submit"
                    class="w-full bg-gradient-to-r from-violet-600 to-purple-600 hover:from-violet-700 hover:to-purple-700 text-white font-semibold py-3 px-6 rounded-xl shadow-lg shadow-violet-500/30 transition-all active:scale-95">
                    💾 Simpan Perubahan
                </button>
            </div>

            <!-- Success Message -->
            <div x-data="{ show: false }" 
                 @profile-updated.window="show = true; setTimeout(() => show = false, 3000)"
                 x-show="show"
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 translate-y-2"
                 x-transition:enter-end="opacity-100 translate-y-0"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="opacity-100 translate-y-0"
                 x-transition:leave-end="opacity-0 translate-y-2"
                 class="fixed top-20 left-1/2 -translate-x-1/2 z-50 bg-green-500 text-white px-6 py-3 rounded-xl shadow-lg"
                 style="display: none;">
                <div class="flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    <span class="font-medium">Profil berhasil disimpan!</span>
                </div>
            </div>
        </form>
    </div>
</div>
