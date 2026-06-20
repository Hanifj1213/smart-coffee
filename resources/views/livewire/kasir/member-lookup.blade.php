<div class="p-4 sm:p-6 space-y-6">
    <!-- TICKER -->
    <div class="ticker-bar bg-espresso text-cream py-2">
        <div class="marquee inline-block text-xs font-extrabold tracking-widest uppercase">
            🔍 PROFIL MEMBER — CEK INFO LOYALITAS PELANGGAN — Smart Coffee — ☕
        </div>
    </div>

    <!-- HEADER -->
    <div>
        <h1 class="text-3xl font-black text-espresso dark:text-cream uppercase tracking-tight">🔍 Cari Member</h1>
        <p class="text-coffee-600 dark:text-coffee-300 font-semibold mt-1">Lihat profil, tier, poin, dan riwayat transaksi member.</p>
    </div>

    <!-- SEARCH BAR -->
    <div class="nb-card bg-cream dark:bg-coffee-900 p-5">
        <div class="relative">
            <input type="text" wire:model.live="search" placeholder="🔍 Ketik nama, email, atau nomor HP member..." class="nb-input w-full bg-white dark:bg-coffee-800 px-4 py-3 text-sm text-espresso dark:text-cream" />
        </div>

        @if($search && $members->count() > 0 && !$selectedMember)
            <div class="mt-3 max-h-64 overflow-y-auto nb-card-sm bg-white dark:bg-coffee-800 divide-y-2 divide-black/10">
                @foreach($members as $member)
                    <div wire:click="selectMember({{ $member->id }})" class="flex cursor-pointer items-center justify-between p-3 text-sm hover:bg-caramel/20 transition duration-100">
                        <div>
                            <p class="font-black text-espresso dark:text-cream">{{ $member->name }}</p>
                            <p class="text-[11px] text-coffee-500 font-semibold">{{ $member->email }} • {{ $member->no_hp ?? '-' }}</p>
                        </div>
                        <span class="nb-badge @if($member->tier_status === 'Gold') bg-caramel text-espresso @elseif($member->tier_status === 'Silver') bg-zinc-300 text-espresso @else bg-orange-y2k text-espresso @endif">
                            {{ $member->tier_status }}
                        </span>
                    </div>
                @endforeach
            </div>
        @elseif($search && $members->count() === 0 && !$selectedMember)
            <div class="mt-3 nb-card-sm bg-white/60 dark:bg-coffee-800/30 p-5 text-center border-dashed">
                <span class="text-3xl block mb-2">🤷</span>
                <p class="text-sm font-bold text-coffee-400">Member tidak ditemukan.</p>
            </div>
        @endif
    </div>

    <!-- SELECTED MEMBER PROFILE -->
    @if($selectedMember)
        <div class="space-y-5">
            <!-- Profile Card -->
            <div class="nb-card bg-gradient-to-br from-caramel/20 to-pink-y2k/15 p-6">
                <div class="flex items-start justify-between mb-5">
                    <div class="flex items-center gap-4">
                        <div class="flex h-16 w-16 items-center justify-center rounded-full border-3 border-black bg-caramel text-espresso font-black text-xl shadow-[3px_3px_0px_#1a1a1a]">
                            {{ $selectedMember->initials() }}
                        </div>
                        <div>
                            <h2 class="text-xl font-black text-espresso dark:text-cream">{{ $selectedMember->name }}</h2>
                            <p class="text-xs font-semibold text-coffee-500">{{ $selectedMember->email }}</p>
                            <p class="text-xs font-semibold text-coffee-500">📱 {{ $selectedMember->no_hp ?? '-' }}</p>
                        </div>
                    </div>
                    <button wire:click="clearSelection" class="nb-btn bg-berry text-white text-xs px-3 py-1.5">✕ Tutup</button>
                </div>

                <!-- Stats Grid -->
                <div class="grid grid-cols-2 gap-3 sm:grid-cols-4">
                    <div class="nb-card-sm bg-white/70 dark:bg-coffee-800/50 p-3 text-center">
                        <p class="text-[9px] font-black uppercase tracking-wider text-coffee-500">Tier</p>
                        <p class="text-lg font-black text-espresso dark:text-cream mt-1">
                            @if($selectedMember->tier_status === 'Gold') 👑 @elseif($selectedMember->tier_status === 'Silver') 🥈 @else 🥉 @endif
                            {{ $selectedMember->tier_status }}
                        </p>
                    </div>
                    <div class="nb-card-sm bg-white/70 dark:bg-coffee-800/50 p-3 text-center">
                        <p class="text-[9px] font-black uppercase tracking-wider text-coffee-500">Total Poin</p>
                        <p class="text-lg font-black text-espresso dark:text-cream mt-1">{{ number_format($selectedMember->total_poin, 0, ',', '.') }}</p>
                    </div>
                    <div class="nb-card-sm bg-white/70 dark:bg-coffee-800/50 p-3 text-center">
                        <p class="text-[9px] font-black uppercase tracking-wider text-coffee-500">Total Belanja</p>
                        <p class="text-lg font-black text-espresso dark:text-cream mt-1">Rp {{ number_format($selectedMember->total_pengeluaran, 0, ',', '.') }}</p>
                    </div>
                    <div class="nb-card-sm bg-white/70 dark:bg-coffee-800/50 p-3 text-center">
                        <p class="text-[9px] font-black uppercase tracking-wider text-coffee-500">Segmen KNN</p>
                        <p class="text-xs font-black text-espresso dark:text-cream mt-1.5 leading-tight">{{ $selectedMember->behavior_label ?? 'Belum Terklasifikasi' }}</p>
                    </div>
                </div>
            </div>

            <!-- Member Since -->
            <div class="nb-card-sm bg-yellow-y2k/20 p-3 flex items-center gap-2 text-xs font-bold text-espresso">
                <span>📅</span>
                <span>Member sejak {{ $selectedMember->created_at?->format('d M Y') ?? '-' }} ({{ $selectedMember->created_at?->diffForHumans() ?? '-' }})</span>
            </div>

            <!-- Recent Transactions -->
            <div class="nb-card bg-cream dark:bg-coffee-900 overflow-hidden">
                <div class="px-5 py-4 border-b-2 border-black/10">
                    <h3 class="text-sm font-black text-espresso dark:text-cream uppercase tracking-wider">📋 5 Transaksi Terakhir</h3>
                </div>

                @if($recentTransactions->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="bg-espresso/5 dark:bg-coffee-800">
                                    <th class="px-4 py-2.5 text-left text-[10px] font-black uppercase tracking-wider text-coffee-600">Tanggal</th>
                                    <th class="px-4 py-2.5 text-center text-[10px] font-black uppercase tracking-wider text-coffee-600">Item</th>
                                    <th class="px-4 py-2.5 text-left text-[10px] font-black uppercase tracking-wider text-coffee-600">Menu</th>
                                    <th class="px-4 py-2.5 text-right text-[10px] font-black uppercase tracking-wider text-coffee-600">Total</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-black/5">
                                @foreach($recentTransactions as $tx)
                                    <tr class="hover:bg-caramel/10 transition duration-100">
                                        <td class="px-4 py-3 text-xs font-semibold text-coffee-600 dark:text-coffee-300">{{ $tx->created_at->format('d M Y H:i') }}</td>
                                        <td class="px-4 py-3 text-center">
                                            <span class="nb-badge bg-yellow-y2k text-espresso text-[10px]">{{ $tx->jumlah_item }} item</span>
                                        </td>
                                        <td class="px-4 py-3 text-xs text-coffee-600 dark:text-coffee-300">
                                            {{ $tx->details->pluck('nama_menu')->take(3)->join(', ') }}
                                            @if($tx->details->count() > 3)
                                                <span class="text-coffee-400">+{{ $tx->details->count() - 3 }} lagi</span>
                                            @endif
                                        </td>
                                        <td class="px-4 py-3 text-right font-black text-espresso dark:text-cream">Rp {{ number_format($tx->total_bayar, 0, ',', '.') }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="px-5 py-10 text-center">
                        <span class="text-4xl block mb-2">📭</span>
                        <p class="text-sm font-bold text-coffee-400">Belum ada riwayat transaksi.</p>
                    </div>
                @endif
            </div>
        </div>
    @elseif(!$search)
        <!-- Empty State -->
        <div class="nb-card bg-cream dark:bg-coffee-900 p-10 text-center">
            <span class="text-6xl block mb-3 float-bean">🔍</span>
            <h3 class="text-lg font-black text-espresso dark:text-cream">Cari Member</h3>
            <p class="text-sm font-semibold text-coffee-500 mt-1">Ketik nama, email, atau nomor HP member di kolom pencarian di atas.</p>
        </div>
    @endif
</div>
