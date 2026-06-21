<div class="p-4 sm:p-6 space-y-6">
    <!-- TICKER -->
    <div class="ticker-bar bg-caramel text-espresso py-2">
        <div class="marquee inline-block text-xs font-extrabold tracking-widest uppercase">
            🎁 TUKAR POIN ANDA DENGAN REWARD MENARIK! — VOUCHER, PRODUK & MERCHANDISE EKSKLUSIF — ☕ SMART COFFEE LOYALTY —
        </div>
    </div>

    <!-- HEADER -->
    <div class="flex flex-col justify-between gap-4 md:flex-row md:items-center">
        <div>
            <h1 class="text-3xl font-black text-espresso dark:text-cream uppercase tracking-tight">🎁 Katalog Reward</h1>
            <p class="text-coffee-600 dark:text-coffee-300 font-semibold mt-1">Tukarkan poin loyalty Anda dengan hadiah eksklusif!</p>
        </div>
        <div class="nb-card bg-yellow-y2k px-5 py-3 text-center">
            <p class="text-[9px] uppercase font-black tracking-widest text-espresso/70">Saldo Poin Anda</p>
            <p class="text-3xl font-black text-espresso">{{ number_format($user->total_poin, 0, ',', '.') }} ⭐</p>
        </div>
    </div>

    @if($successMessage)
        <div class="nb-card bg-matcha/20 p-4 flex items-start gap-3">
            <span class="text-2xl star-pulse">🎉</span>
            <div class="flex-1">
                <p class="font-extrabold text-sm text-espresso dark:text-cream">{{ $successMessage }}</p>
                @if($lastVoucherCode)
                    <p class="text-xs text-coffee-600 mt-1 font-bold">Tunjukkan kode ini ke kasir untuk klaim reward Anda.</p>
                @endif
            </div>
            <button wire:click="$set('successMessage', '')" class="text-espresso hover:text-berry font-black text-xl">×</button>
        </div>
    @endif

    @if($errorMessage)
        <div class="nb-card bg-berry/15 p-4 flex items-start gap-3">
            <span class="text-2xl">⚠️</span>
            <p class="flex-1 font-extrabold text-sm text-berry">{{ $errorMessage }}</p>
            <button wire:click="$set('errorMessage', '')" class="text-espresso hover:text-berry font-black text-xl">×</button>
        </div>
    @endif

    <!-- CATALOG GRID -->
    <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-3">
        @forelse($rewards as $reward)
            @php $canAfford = $user->total_poin >= $reward->poin_cost; $inStock = $reward->stok === null || $reward->stok > 0; @endphp
            <div class="nb-card bg-cream dark:bg-coffee-900 p-5 flex flex-col justify-between space-y-3 {{ !$inStock ? 'opacity-60' : '' }}">
                <div>
                    <div class="flex justify-between items-start">
                        <span class="text-5xl float-bean">{{ $reward->icon }}</span>
                        <span class="nb-badge bg-blue-y2k text-espresso text-[9px]">{{ $reward->kategori }}</span>
                    </div>
                    <h3 class="text-lg font-black text-espresso dark:text-cream mt-3">{{ $reward->nama }}</h3>
                    @if($reward->discount_percent > 0)
                        <span class="nb-badge bg-purple-y2k text-espresso text-[9px] mt-1 inline-block">🎟️ Diskon {{ $reward->discount_percent }}% — pakai saat Pesan Menu</span>
                    @endif
                    <p class="text-xs text-coffee-600 dark:text-coffee-300 mt-1 font-semibold line-clamp-2">{{ $reward->deskripsi }}</p>
                </div>

                <div class="space-y-2">
                    <div class="flex justify-between items-center text-xs font-bold">
                        <span class="nb-badge bg-caramel text-espresso text-sm">{{ number_format($reward->poin_cost, 0, ',', '.') }} ⭐</span>
                        <span class="text-coffee-400 text-[10px] font-black">Stok: {{ $reward->stok === null ? '∞' : $reward->stok }}</span>
                    </div>

                    @if(!$inStock)
                        <button disabled class="nb-btn w-full bg-zinc-300 text-coffee-500 cursor-not-allowed text-xs">Stok Habis</button>
                    @elseif(!$canAfford)
                        <button disabled class="nb-btn w-full bg-zinc-300 text-coffee-500 cursor-not-allowed text-xs">Poin Tidak Cukup</button>
                    @else
                        <button wire:click="redeem({{ $reward->id }})" wire:confirm="Tukar {{ number_format($reward->poin_cost, 0, ',', '.') }} poin untuk '{{ $reward->nama }}'?" class="nb-btn w-full bg-berry text-white text-xs">🎁 Tukar Sekarang</button>
                    @endif
                </div>
            </div>
        @empty
            <div class="col-span-full nb-card bg-cream dark:bg-coffee-900 p-12 text-center">
                <span class="text-5xl block mb-3">📭</span>
                <p class="text-sm text-coffee-400 font-bold">Belum ada reward tersedia saat ini. Cek lagi nanti!</p>
            </div>
        @endforelse
    </div>

    <!-- RIWAYAT PENUKARAN -->
    <div class="nb-card bg-cream dark:bg-coffee-900 p-6 space-y-4">
        <h2 class="text-lg font-black text-espresso dark:text-cream uppercase">🧾 Riwayat Penukaran Saya</h2>
        <div class="overflow-x-auto nb-table bg-white dark:bg-coffee-800">
            <table class="w-full text-left text-sm">
                <thead class="bg-espresso text-cream text-xs">
                    <tr>
                        <th class="px-4 py-3">Reward</th>
                        <th class="px-4 py-3 text-center">Poin</th>
                        <th class="px-4 py-3">Kode Voucher</th>
                        <th class="px-4 py-3">Tanggal</th>
                        <th class="px-4 py-3 text-right">Status</th>
                    </tr>
                </thead>
                <tbody class="text-xs text-coffee-700 dark:text-coffee-200">
                    @forelse($myRedemptions as $r)
                        <tr class="hover:bg-caramel/10 border-b border-black/10">
                            <td class="px-4 py-3 font-black text-espresso dark:text-cream">{{ $r->reward_nama }}</td>
                            <td class="px-4 py-3 text-center">{{ number_format($r->poin_spent, 0, ',', '.') }} ⭐</td>
                            <td class="px-4 py-3 font-mono font-black text-purple-y2k">{{ $r->kode_voucher }}</td>
                            <td class="px-4 py-3 text-[11px]">{{ $r->created_at->format('d M Y, H:i') }}</td>
                            <td class="px-4 py-3 text-right"><span class="nb-badge bg-matcha text-espresso">{{ $r->status }}</span></td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-10 text-center text-coffee-400 text-sm font-bold">Anda belum pernah menukar reward.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
