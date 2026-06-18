<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
    <head>
        @include('partials.head')
        <style>
            .floating-auth { position: absolute; font-size: 2.5rem; opacity: 0.15; pointer-events: none; z-index: 0; }
            @keyframes floatAuth {
                0%, 100% { transform: translateY(0) rotate(0deg); }
                50% { transform: translateY(-10px) rotate(8deg); }
            }
            .fa-1 { top: 10%; left: 10%; animation: floatAuth 5s ease-in-out infinite; }
            .fa-2 { top: 15%; right: 15%; animation: floatAuth 6s ease-in-out infinite 1s; }
            .fa-3 { bottom: 15%; left: 12%; animation: floatAuth 5.5s ease-in-out infinite 0.5s; }
            .fa-4 { bottom: 12%; right: 10%; animation: floatAuth 4.5s ease-in-out infinite 1.5s; }
        </style>
    </head>
    <body class="min-h-screen bg-cream dark:bg-espresso coffee-pattern antialiased text-espresso dark:text-cream relative overflow-hidden flex items-center justify-center py-12 px-4">
        
        <!-- Background Decorative Emojis -->
        <span class="floating-auth fa-1">☕</span>
        <span class="floating-auth fa-2">🍩</span>
        <span class="floating-auth fa-3">🧠</span>
        <span class="floating-auth fa-4">🧁</span>

        <div class="relative z-10 w-full max-w-md flex flex-col gap-6">
            <!-- App Logo Icon -->
            <div class="text-center">
                <a href="{{ route('home') }}" class="inline-flex flex-col items-center gap-2 font-black group" wire:navigate>
                    <span class="flex h-14 w-14 items-center justify-center rounded-2xl border-3 border-black bg-caramel text-3xl shadow-[3px_3px_0px_#1a1a1a] transition group-hover:scale-105">
                        ☕
                    </span>
                    <span class="text-xl tracking-tight mt-1 text-espresso dark:text-cream">Smart Coffee CRM</span>
                </a>
            </div>

            <!-- Neo Brutalist Auth Card Container -->
            <div class="nb-card bg-white dark:bg-coffee-900 p-8 sm:p-10 border-3 border-black">
                <!-- Y2K Corner Sticker Badge -->
                <div class="absolute -right-10 -top-3 rotate-12 nb-badge bg-pink-y2k text-espresso text-[10px] font-black z-20">
                    MEMBER CLUB ⭐
                </div>

                {{ $slot }}
            </div>

            <!-- Cafe Slogan Ticker -->
            <div class="text-center">
                <p class="text-xs font-bold text-coffee-600 dark:text-coffee-400">
                    "Roasted to perfection, secured with love." 🤎
                </p>
            </div>
        </div>

        @persist('toast')
            <flux:toast.group>
                <flux:toast />
            </flux:toast.group>
        @endpersist

        @fluxScripts
    </body>
</html>
