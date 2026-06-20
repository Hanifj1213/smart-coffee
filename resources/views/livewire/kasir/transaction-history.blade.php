<div class="p-4 sm:p-6 space-y-6">
    <!-- TICKER -->
    <div class="ticker-bar bg-espresso text-cream py-2">
        <div class="marquee inline-block text-xs font-extrabold tracking-widest uppercase">
            📋 RIWAYAT TRANSAKSI — REKAP PENJUALAN HARIAN — SMART COFFEE POS SYSTEM — ☕
        </div>
    </div>

    <!-- HEADER -->
    <div class="flex flex-col justify-between gap-4 md:flex-row md:items-center">
        <div>
            <h1 class="text-3xl font-black text-espresso dark:text-cream uppercase tracking-tight">📋 Riwayat Transaksi</h1>
            <p class="text-coffee-600 dark:text-coffee-300 font-semibold mt-1">Lihat semua transaksi yang telah diproses.</p>
        </div>
    </div>

    <!-- TODAY SUMMARY CARDS -->
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
        <div class="nb-card bg-gradient-to-br from-matcha/20 to-mint-y2k/20 p-5 flex items-center gap-4">
            <span class="text-4xl">💰</span>
            <div>
                <p class="text-[10px] font-black uppercase tracking-widest text-coffee-600">Pendapatan Hari Ini</p>
                <p class="text-2xl font-black text-espresso dark:text-cream">Rp {{ number_format($todayTotal, 0, ',', '.') }}</p>
            </div>
        </div>
        <div class="nb-card bg-gradient-to-br from-caramel/20 to-yellow-y2k/20 p-5 flex items-center gap-4">
            <span class="text-4xl">🧾</span>
            <div>
                <p class="text-[10px] font-black uppercase tracking-widest text-coffee-600">Transaksi Hari Ini</p>
                <p class="text-2xl font-black text-espresso dark:text-cream">{{ $todayCount }} Transaksi</p>
            </div>
        </div>
    </div>

    <!-- FILTERS -->
    <div class="nb-card bg-cream dark:bg-coffee-900 p-4">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center">
            <div class="flex-1">
                <input type="text" wire:model.live="search" placeholder="🔍 Cari nama atau email member..." class="nb-input w-full bg-white dark:bg-coffee-800 px-4 py-2.5 text-sm text-espresso dark:text-cream" />
            </div>
            <div>
                <input type="date" wire:model.live="dateFilter" class="nb-input bg-white dark:bg-coffee-800 px-4 py-2.5 text-sm text-espresso dark:text-cream" />
            </div>
        </div>
    </div>

    <!-- TRANSACTION TABLE -->
    <div class="nb-card bg-cream dark:bg-coffee-900 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-espresso text-cream">
                        <th class="px-4 py-3 text-left text-[10px] font-black uppercase tracking-wider">ID</th>
                        <th class="px-4 py-3 text-left text-[10px] font-black uppercase tracking-wider">Tanggal</th>
                        <th class="px-4 py-3 text-left text-[10px] font-black uppercase tracking-wider">Member</th>
                        <th class="px-4 py-3 text-center text-[10px] font-black uppercase tracking-wider">Item</th>
                        <th class="px-4 py-3 text-right text-[10px] font-black uppercase tracking-wider">Total Bayar</th>
                        <th class="px-4 py-3 text-center text-[10px] font-black uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y-2 divide-black/5">
                    @forelse($transactions as $tx)
                        <tr class="hover:bg-caramel/10 transition duration-100">
                            <td class="px-4 py-3 font-black text-espresso dark:text-cream">#{{ str_pad($tx->id, 5, '0', STR_PAD_LEFT) }}</td>
                            <td class="px-4 py-3 text-coffee-600 dark:text-coffee-300 font-semibold text-xs">{{ $tx->created_at->format('d M Y H:i') }}</td>
                            <td class="px-4 py-3">
                                <p class="font-black text-espresso dark:text-cream">{{ $tx->user?->name ?? '-' }}</p>
                                <p class="text-[10px] text-coffee-500 font-semibold">{{ $tx->user?->email ?? '' }}</p>
                            </td>
                            <td class="px-4 py-3 text-center">
                                <span class="nb-badge bg-yellow-y2k text-espresso">{{ $tx->jumlah_item }} item</span>
                            </td>
                            <td class="px-4 py-3 text-right font-black text-espresso dark:text-cream">Rp {{ number_format($tx->total_bayar, 0, ',', '.') }}</td>
                            <td class="px-4 py-3 text-center">
                                <button wire:click="showDetail({{ $tx->id }})" class="nb-btn bg-caramel text-espresso text-xs px-3 py-1">
                                    👁️ Detail
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-10 text-center">
                                <span class="text-4xl block mb-2">📭</span>
                                <p class="text-sm font-bold text-coffee-400">Belum ada transaksi.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="p-4 border-t-2 border-black/10">
            {{ $transactions->links() }}
        </div>
    </div>

    <!-- DETAIL RECEIPT MODAL -->
    @if($showDetailModal && !empty($detailData))
        <div class="fixed inset-0 z-50 flex items-center justify-center nb-modal-overlay p-4">
            <div class="w-full max-w-sm nb-modal bg-cream dark:bg-coffee-950 p-5 space-y-4">
                <div class="flex justify-between items-center">
                    <h3 class="text-xs font-black text-espresso dark:text-cream uppercase tracking-wider">🧾 Detail Struk</h3>
                    <button wire:click="closeDetail" class="nb-btn bg-berry text-white text-xs px-2 py-1">Tutup</button>
                </div>

                <div class="receipt-paper rounded-lg">
                    <!-- Coffee Shop Header -->
                    <div class="text-center mb-3">
                        <span class="text-3xl">☕</span>
                        <h4 class="font-black text-sm uppercase mt-1 text-espresso">SMART COFFEE</h4>
                        <p class="text-[9px] font-bold text-coffee-700">Jl. Kopi Brutalis No. Y2K</p>
                    </div>

                    <div class="receipt-dashed my-2"></div>

                    <!-- Transaction Meta -->
                    <div class="text-[9px] text-coffee-800 font-mono space-y-0.5">
                        <p>ID STRUK: #{{ str_pad($detailData['id'], 5, '0', STR_PAD_LEFT) }}</p>
                        <p>TANGGAL : {{ $detailData['date'] }}</p>
                        <p>PELANGGAN: {{ $detailData['customer_name'] }} ({{ $detailData['tier'] }})</p>
                    </div>

                    <div class="receipt-dashed my-2"></div>

                    <!-- Items -->
                    <div class="text-[9px] text-coffee-800 font-mono space-y-1">
                        @foreach($detailData['items'] as $item)
                            <div class="flex justify-between">
                                <span>1x {{ $item['nama'] }} (Manis: {{ $item['manis'] }}/5)</span>
                                <span>Rp{{ number_format($item['harga'], 0, ',', '.') }}</span>
                            </div>
                        @endforeach
                    </div>

                    <div class="receipt-dashed my-2"></div>

                    <!-- Total -->
                    <div class="text-[9px] text-coffee-800 font-mono space-y-0.5">
                        <div class="flex justify-between text-xs font-black text-espresso">
                            <span>TOTAL BAYAR</span>
                            <span>Rp{{ number_format($detailData['total_bayar'], 0, ',', '.') }}</span>
                        </div>
                    </div>

                    <div class="receipt-dashed my-2"></div>

                    <div class="text-[9px] text-coffee-800 font-mono text-center">
                        <p class="mt-2 text-xs font-black uppercase tracking-wider">*** TERIMA KASIH ***</p>
                    </div>

                    <!-- Barcode -->
                    <div class="receipt-barcode mt-3"></div>
                </div>

                <div class="flex gap-2 pt-2">
                    <button onclick="window.print()" class="nb-btn flex-1 bg-yellow-y2k text-espresso text-xs">🖨️ Cetak</button>
                    <button wire:click="closeDetail" class="nb-btn flex-1 bg-espresso text-cream text-xs">Kembali</button>
                </div>
            </div>
        </div>
    @endif
</div>
