<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
    <head>
        @include('partials.head')
    </head>
    <body class="min-h-screen bg-coffee-50 dark:bg-espresso coffee-pattern" style="font-family: 'Nunito Sans', system-ui, sans-serif;">
        <flux:sidebar sticky collapsible="mobile" class="border-e-3 border-black bg-cream dark:border-zinc-700 dark:bg-coffee-900">
            <flux:sidebar.header>
                <x-app-logo :sidebar="true" href="{{ route('home') }}" wire:navigate />
                <flux:sidebar.collapse class="lg:hidden" />
            </flux:sidebar.header>

            @if(auth()->user()->role === 'admin')
                <flux:sidebar.nav>
                    <flux:sidebar.group :heading="__('☕ Admin')" class="grid">
                        <flux:sidebar.item icon="home" :href="route('admin.dashboard')" :current="request()->routeIs('admin.dashboard')" wire:navigate>
                            {{ __('Dashboard Analitik') }}
                        </flux:sidebar.item>
                        <flux:sidebar.item icon="rectangle-stack" :href="route('admin.menus')" :current="request()->routeIs('admin.menus')" wire:navigate>
                            {{ __('Manajemen Menu') }}
                        </flux:sidebar.item>
                        <flux:sidebar.item icon="users" :href="route('admin.users')" :current="request()->routeIs('admin.users')" wire:navigate>
                            {{ __('Manajemen Akun') }}
                        </flux:sidebar.item>
                        <flux:sidebar.item icon="gift" :href="route('admin.rewards')" :current="request()->routeIs('admin.rewards')" wire:navigate>
                            {{ __('Katalog Reward') }}
                        </flux:sidebar.item>
                    </flux:sidebar.group>
                </flux:sidebar.nav>
            @elseif(auth()->user()->role === 'kasir')
                <flux:sidebar.nav>
                    <flux:sidebar.group :heading="__('🖥️ Kasir POS')" class="grid">
                        <flux:sidebar.item icon="credit-card" :href="route('kasir.cashier')" :current="request()->routeIs('kasir.cashier')" wire:navigate>
                            {{ __('Kasir POS') }}
                        </flux:sidebar.item>
                        <flux:sidebar.item icon="clipboard-document-list" :href="route('kasir.transactions')" :current="request()->routeIs('kasir.transactions')" wire:navigate>
                            {{ __('Riwayat Transaksi') }}
                        </flux:sidebar.item>
                        <flux:sidebar.item icon="magnifying-glass" :href="route('kasir.member-lookup')" :current="request()->routeIs('kasir.member-lookup')" wire:navigate>
                            {{ __('Cari Member') }}
                        </flux:sidebar.item>
                    </flux:sidebar.group>
                </flux:sidebar.nav>
            @else
                <flux:sidebar.nav>
                    <flux:sidebar.group :heading="__('☕ Loyalty')" class="grid">
                        <flux:sidebar.item icon="home" :href="route('member.dashboard')" :current="request()->routeIs('member.dashboard')" wire:navigate>
                            {{ __('Loyalty Member') }}
                        </flux:sidebar.item>
                        <flux:sidebar.item icon="shopping-cart" :href="route('member.order')" :current="request()->routeIs('member.order')" wire:navigate>
                            {{ __('Pesan Menu') }}
                        </flux:sidebar.item>
                        <flux:sidebar.item icon="gift" :href="route('member.rewards')" :current="request()->routeIs('member.rewards')" wire:navigate>
                            {{ __('Katalog Reward') }}
                        </flux:sidebar.item>
                    </flux:sidebar.group>
                </flux:sidebar.nav>
            @endif


            <!-- ☕ SMART COFFEE MACHINE STATUS WIDGET -->
            <div class="in-data-flux-sidebar-collapsed-desktop:hidden mx-4 my-4 p-3.5 nb-card bg-yellow-y2k/20 border-2 border-black flex flex-col gap-2">
                <div class="flex items-center justify-between">
                    <span class="text-[9px] font-black uppercase text-espresso/60 tracking-wider">Espresso Machine</span>
                    <span class="flex items-center gap-1.5">
                        <span class="h-2.5 w-2.5 rounded-full bg-emerald-500 animate-pulse border border-black"></span>
                        <span class="text-[9px] font-black text-espresso/80">ONLINE</span>
                    </span>
                </div>
                <div class="retro-divider !opacity-20 my-0.5"></div>
                <div class="flex items-center gap-2">
                    <span class="text-xl float-bean">☕</span>
                    <div class="flex-1 min-w-0">
                        <p class="text-[10px] font-black text-espresso truncate">Pressure: 9 Bar</p>
                        <p class="text-[9px] font-bold text-coffee-600 truncate">Temp: 92.5°C | Ready</p>
                    </div>
                </div>
            </div>

            <flux:spacer />

            <x-desktop-user-menu class="hidden lg:block" :name="auth()->user()->name" />
        </flux:sidebar>

        <!-- Mobile User Menu -->
        <flux:header class="lg:hidden">
            <flux:sidebar.toggle class="lg:hidden" icon="bars-2" inset="left" />

            <flux:spacer />

            <flux:dropdown position="top" align="end">
                <flux:profile
                    :initials="auth()->user()->initials()"
                    icon-trailing="chevron-down"
                />

                <flux:menu>
                    <flux:menu.radio.group>
                        <div class="p-0 text-sm font-normal">
                            <div class="flex items-center gap-2 px-1 py-1.5 text-start text-sm">
                                <flux:avatar
                                    :name="auth()->user()->name"
                                    :initials="auth()->user()->initials()"
                                />

                                <div class="grid flex-1 text-start text-sm leading-tight">
                                    <flux:heading class="truncate">{{ auth()->user()->name }}</flux:heading>
                                    <flux:text class="truncate">{{ auth()->user()->email }}</flux:text>
                                </div>
                            </div>
                        </div>
                    </flux:menu.radio.group>

                    <flux:menu.separator />

                    <flux:menu.radio.group>
                        <flux:menu.item :href="route('profile.edit')" icon="cog" wire:navigate>
                            {{ __('Settings') }}
                        </flux:menu.item>
                    </flux:menu.radio.group>

                    <flux:menu.separator />

                    <form method="POST" action="{{ route('logout') }}" class="w-full">
                        @csrf
                        <flux:menu.item
                            as="button"
                            type="submit"
                            icon="arrow-right-start-on-rectangle"
                            class="w-full cursor-pointer"
                            data-test="logout-button"
                        >
                            {{ __('Log out') }}
                        </flux:menu.item>
                    </form>
                </flux:menu>
            </flux:dropdown>
        </flux:header>

        {{ $slot }}

        @persist('toast')
            <flux:toast.group>
                <flux:toast />
            </flux:toast.group>
        @endpersist

        @fluxScripts
    </body>
</html>
