<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        @include('partials.head')
        <style>
            /* Tema Hijau Forest untuk Auth Pages */
            .gradient-bg-auth {
                background: linear-gradient(135deg, #007942 0%, #00a854 50%, #00c853 100%);
            }
            
            .gradient-text-auth {
                background: linear-gradient(135deg, #007942 0%, #00a854 50%, #00c853 100%);
                -webkit-background-clip: text;
                -webkit-text-fill-color: transparent;
                background-clip: text;
            }
            
            /* Pattern Background */
            .auth-pattern {
                background-image: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.05'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
            }
            
            /* Floating animation untuk elemen dekoratif */
            @keyframes float-auth {
                0%, 100% { transform: translateY(0px); }
                50% { transform: translateY(-15px); }
            }
            
            .float-auth {
                animation: float-auth 3s ease-in-out infinite;
            }
        </style>
    </head>
    <body class="min-h-screen bg-white antialiased">
        <div class="relative grid h-dvh flex-col items-center justify-center px-8 sm:px-0 lg:max-w-none lg:grid-cols-2 lg:px-0">
            <div class="bg-muted relative hidden h-full flex-col p-10 text-white lg:flex">
                <div class="absolute inset-0 gradient-bg-auth auth-pattern"></div>
                
                <!-- Decorative floating elements -->
                <div class="absolute top-20 right-20 w-32 h-32 bg-white/10 rounded-full blur-2xl float-auth"></div>
                <div class="absolute bottom-20 left-20 w-40 h-40 bg-white/10 rounded-full blur-2xl float-auth" style="animation-delay: 1s;"></div>
                
                <a href="{{ route('home') }}" class="relative z-20 flex items-center text-lg font-medium" wire:navigate>
                    <span class="font-bold text-xl">{{ config('app.name', 'Laravel') }}</span>
                </a>

                @php
                    [$message, $author] = str(Illuminate\Foundation\Inspiring::quotes()->random())->explode('-');
                @endphp

                <div class="relative z-20 mt-auto">
                    <blockquote class="space-y-2">
                        <flux:heading size="lg" class="text-white/90">&ldquo;{{ trim($message) }}&rdquo;</flux:heading>
                        <footer><flux:heading class="text-white/70">{{ trim($author) }}</flux:heading></footer>
                    </blockquote>
                </div>
            </div>
            <div class="w-full lg:p-8">
                <div class="mx-auto flex w-full flex-col justify-center space-y-6 sm:w-[350px]">
                    <a href="{{ route('home') }}" class="z-20 flex flex-col items-center gap-2 font-medium lg:hidden" wire:navigate>
                        <span class="sr-only">{{ config('app.name', 'Laravel') }}</span>
                    </a>
                    {{ $slot }}
                </div>
            </div>
        </div>
        @fluxScripts
    </body>
</html>
