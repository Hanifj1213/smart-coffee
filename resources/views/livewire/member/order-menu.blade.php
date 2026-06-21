
<div class="p-4 sm:p-6 space-y-6">
    <!-- TICKER -->
    <div class="ticker-bar bg-caramel text-espresso py-2">
        <div class="marquee inline-block text-xs font-extrabold tracking-widest uppercase">
            ☕ PESAN MENU — ORDER LANGSUNG DARI AKUN MEMBER — POIN OTOMATIS MASUK — DISKON TIER LANGSUNG AKTIF — ☕ PESAN MENU —
        </div>
    </div>

    <!-- HEADER -->
    <div class="flex flex-col justify-between gap-4 md:flex-row md:items-center">
        <div>
            <h1 class="text-3xl font-black text-espresso dark:text-cream uppercase tracking-tight">☕ Pesan Menu</h1>
            <p class="text-coffee-600 dark:text-coffee-300 font-semibold mt-1">Pilih menu favoritmu dan dapatkan poin loyalty otomatis!</p>
        </div>
        <div class="flex items-center gap-3">
            <span class="nb-badge @if(auth()->user()->tier_status === 'Gold') bg-caramel text-espresso @elseif(auth()->user()->tier_status === 'Silver') bg-zinc-300 text-espresso @else bg-orange-y2k text-espresso @endif">
                @if(auth()->user()->tier_status === 'Gold') 👑 @elseif(auth()->user()->tier_status === 'Silver') 🥈 @else 🥉 @endif
                {{ auth()->user()->tier_status }} Member
            </span>
            @if($discountPercent > 0)
                <span class="nb-badge bg-matcha text-espresso">🎉 Diskon {{ $discountPercent }}%</span>
            @endif
        </div>
    </div>

    <!-- Alert Success -->
    @if($successMessage && !$showReceiptModal)
        <div class="nb-card bg-matcha/20 p-4 flex items-start gap-3">
            <span class="text-2xl star-pulse">✅</span>
            <div class="flex-1">
                <p class="font-extrabold text-sm text-espresso dark:text-cream">{{ $successMessage }}</p>
            </div>
        </div>
    @endif

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
        <!-- KOLOM KIRI: Katalog Menu -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Coffee -->
            <div class="nb-card bg-cream dark:bg-coffee-900 p-6">
                <h3 class="mb-3 text-xs font-black uppercase tracking-widest text-coffee-600 flex items-center gap-1">☕ Coffee Series</h3>
                <div class="grid grid-cols-1 gap-3 sm:grid-cols-2 md:grid-cols-3">
                    @foreach($menuCatalog as $index => $item)
                        @if($item['kategori'] === 'Coffee')
                            <div wire:click="addToCart({{ $index }})" class="nb-card-sm bg-latte/20 p-3 cursor-pointer hover:bg-caramel/30 transition duration-100 group flex items-center gap-4">
                                @if($item['img'])
                                    <img src="{{ $item['img'] }}" alt="{{ $item['nama'] }}" class="w-20 h-20 rounded-xl object-cover border-2 border-black shadow-[3px_3px_0px_#1a1a1a] flex-shrink-0">
                                @else
                                    <span class="text-4xl flex-shrink-0">{{ $item['icon'] ?? '☕' }}</span>
                                @endif
                                <div>
                                    <p class="font-black text-espresso dark:text-cream text-base group-hover:text-berry">{{ $item['nama'] }}</p>
                                    <p class="text-sm font-bold text-coffee-500 mt-0.5">Rp {{ number_format($item['harga'], 0, ',', '.') }}</p>
                                </div>
                            </div>
                        @endif
                    @endforeach
                </div>
            </div>

            <!-- Non-Coffee -->
            <div class="nb-card bg-cream dark:bg-coffee-900 p-6">
                <h3 class="mb-3 text-xs font-black uppercase tracking-widest text-coffee-600 flex items-center gap-1">🍵 Non-Coffee Series</h3>
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    @foreach($menuCatalog as $index => $item)
                        @if($item['kategori'] === 'Non-Coffee')
                            <div wire:click="addToCart({{ $index }})" class="nb-card-sm bg-matcha/10 p-3 cursor-pointer hover:bg-mint-y2k/30 transition duration-100 group flex items-center gap-4">
                                @if($item['img'])
                                    <img src="{{ $item['img'] }}" alt="{{ $item['nama'] }}" class="w-20 h-20 rounded-xl object-cover border-2 border-black shadow-[3px_3px_0px_#1a1a1a] flex-shrink-0">
                                @else
                                    <span class="text-4xl flex-shrink-0">{{ $item['icon'] ?? '🍵' }}</span>
                                @endif
                                <div>
                                    <p class="font-black text-espresso dark:text-cream text-base group-hover:text-berry">{{ $item['nama'] }}</p>
                                    <p class="text-sm font-bold text-coffee-500 mt-0.5">Rp {{ number_format($item['harga'], 0, ',', '.') }}</p>
                                </div>
                            </div>
                        @endif
                    @endforeach
                </div>
            </div>

            <!-- Food -->
            <div class="nb-card bg-cream dark:bg-coffee-900 p-6">
                <h3 class="mb-3 text-xs font-black uppercase tracking-widest text-coffee-600 flex items-center gap-1">🍰 Pastry & Cakes</h3>
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    @foreach($menuCatalog as $index => $item)
                        @if($item['kategori'] === 'Food')
                            <div wire:click="addToCart({{ $index }})" class="nb-card-sm bg-pink-y2k/10 p-3 cursor-pointer hover:bg-pink-y2k/25 transition duration-100 group flex items-center gap-4">
                                @if($item['img'])
                                    <img src="{{ $item['img'] }}" alt="{{ $item['nama'] }}" class="w-20 h-20 rounded-xl object-cover border-2 border-black shadow-[3px_3px_0px_#1a1a1a] flex-shrink-0">
                                @else
                                    <span class="text-4xl flex-shrink-0">{{ $item['icon'] ?? '🍰' }}</span>
                                @endif
                                <div>
                                    <p class="font-black text-espresso dark:text-cream text-base group-hover:text-berry">{{ $item['nama'] }}</p>
                                    <p class="text-sm font-bold text-coffee-500 mt-0.5">Rp {{ number_format($item['harga'], 0, ',', '.') }}</p>
                                </div>
                            </div>
                        @endif
                    @endforeach
                </div>
            </div>
        </div>

        <!-- KOLOM KANAN: Keranjang -->
        <div>
            <div class="nb-card bg-cream dark:bg-coffee-900 p-6 space-y-5">
                <h2 class="text-lg font-black text-espresso dark:text-cream uppercase">🛒 Keranjang</h2>

                @if(empty($cart))
                    <div class="flex flex-col items-center justify-center py-10 text-center text-coffee-400">
                        <span class="text-5xl mb-3 float-bean">🛒</span>
                        <p class="text-sm font-bold">Keranjang kosong.</p>
                        <p class="text-xs mt-1">Klik menu di samping untuk menambah.</p>
                    </div>
                @else
                    <div class="space-y-3 max-h-[350px] overflow-y-auto pr-1">
                        @foreach($cart as $index => $item)
                            <div class="nb-card-sm bg-white dark:bg-coffee-800 p-3 space-y-2">
                                <div class="flex justify-between items-start">
                                    <div>
                                        <p class="font-black text-sm text-espresso dark:text-cream">{{ $item['nama'] }}</p>
                                        <p class="text-xs font-bold text-coffee-500">Rp {{ number_format($item['harga'], 0, ',', '.') }}</p>
                                    </div>
                                    <button wire:click="removeFromCart({{ $index }})" class="text-berry font-black text-lg hover:scale-110 transition">✕</button>
                                </div>

                                <!-- Sweetness for KNN -->
                                @if($item['kategori'] !== 'Food')
                                    <div class="nb-card-sm bg-yellow-y2k/20 px-2.5 py-1.5 flex items-center justify-between text-xs">
                                        <span class="font-black text-espresso text-[10px] uppercase">Tingkat Manis:</span>
                                        <select wire:change="updateSweetness({{ $index }}, $event.target.value)" class="bg-transparent border-0 font-black text-espresso text-xs p-0 focus:ring-0 cursor-pointer">
                                            <option value="1" @if($item['manis'] == 1) selected @endif>1 — Pahit</option>
                                            <option value="2" @if($item['manis'] == 2) selected @endif>2 — Sedikit</option>
                                            <option value="3" @if($item['manis'] == 3) selected @endif>3 — Normal</option>
                                            <option value="4" @if($item['manis'] == 4) selected @endif>4 — Manis</option>
                                            <option value="5" @if($item['manis'] == 5) selected @endif>5 — Sangat Manis</option>
                                        </select>
                                    </div>
                                @endif

                                <div class="flex justify-between items-center">
                                    <span class="text-xs font-bold text-coffee-500">Sub: Rp {{ number_format($item['harga'] * $item['qty'], 0, ',', '.') }}</span>
                                    <div class="flex items-center nb-card-sm overflow-hidden bg-white dark:bg-coffee-800">
                                        <button wire:click="decrementQty({{ $index }})" class="px-2.5 py-1 text-espresso hover:bg-berry/20 font-black transition">−</button>
                                        <span class="px-3 text-xs font-black text-espresso dark:text-cream border-x-2 border-black">{{ $item['qty'] }}</span>
                                        <button wire:click="incrementQty({{ $index }})" class="px-2.5 py-1 text-espresso hover:bg-matcha/20 font-black transition">+</button>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="retro-divider"></div>

                    <!-- 🎟️ Kode Promo -->
                    <div class="nb-card-sm bg-purple-y2k/10 p-3 space-y-2">
                        <p class="text-[10px] font-black uppercase tracking-widest text-espresso dark:text-cream">🎟️ Punya Kode Promo / Voucher Reward?</p>
                        @if($couponApplied)
                            <div class="flex items-center justify-between gap-2">
                                <span class="nb-badge bg-matcha text-espresso text-[10px]">✅ {{ strtoupper($couponCode) }} — Diskon {{ $couponPercent }}%</span>
                                <button wire:click="removeCoupon" class="text-berry font-black text-xs hover:underline">Hapus</button>
                            </div>
                        @else
                            <div class="flex items-center gap-2">
                                <input type="text" wire:model="couponCode" wire:keydown.enter="applyCoupon" placeholder="cth: SWEET20"
                                    class="nb-input flex-1 bg-white dark:bg-coffee-800 text-xs p-2 text-espresso dark:text-cream border-2 border-black uppercase" />
                                <button wire:click="applyCoupon" class="nb-btn bg-purple-y2k text-espresso text-xs px-3 py-2">Pakai</button>
                            </div>
                            @if($couponError)
                                <p class="text-[10px] text-berry font-black">{{ $couponError }}</p>
                            @endif
                        @endif
                    </div>

                    <div class="space-y-2">
                        <div class="flex justify-between text-sm font-bold text-coffee-600">
                            <span>Subtotal Item:</span>
                            <span>Rp {{ number_format($cartTotal, 0, ',', '.') }}</span>
                        </div>
                        @if($tierDiscountPercent > 0)
                            <div class="flex justify-between text-sm font-bold text-matcha">
                                <span>🎉 Diskon Tier {{ $tierDiscountPercent }}%:</span>
                                <span>−Rp {{ number_format(($cartTotal * $tierDiscountPercent) / 100, 0, ',', '.') }}</span>
                            </div>
                        @endif
                        @if($couponPercent > 0)
                            <div class="flex justify-between text-sm font-bold text-purple-y2k">
                                <span>🎟️ Promo {{ strtoupper($couponCode) }} {{ $couponPercent }}%:</span>
                                <span>−Rp {{ number_format(($cartTotal * $couponPercent) / 100, 0, ',', '.') }}</span>
                            </div>
                        @endif
                        <div class="flex justify-between text-lg font-black text-espresso dark:text-cream retro-divider pt-3">
                            <span>TOTAL BAYAR:</span>
                            <span class="text-berry">Rp {{ number_format($finalTotal, 0, ',', '.') }}</span>
                        </div>

                        <div class="nb-card-sm bg-yellow-y2k/20 p-3 text-xs space-y-1">
                            <div class="flex justify-between font-bold text-espresso">
                                <span>Estimasi Poin Masuk:</span>
                                <span class="font-black text-sm">+{{ (int)(floor($finalTotal / 10000) * 100 * (auth()->user()->tier_status === 'Gold' ? 1.5 : (auth()->user()->tier_status === 'Silver' ? 1.2 : 1.0))) }} Poin</span>
                            </div>
                            <div class="flex justify-between text-[10px] text-coffee-500 font-bold">
                                <span>Multiplier:</span>
                                <span>{{ auth()->user()->tier_status === 'Gold' ? '1.5x (Gold) 👑' : (auth()->user()->tier_status === 'Silver' ? '1.2x (Silver) 🥈' : '1.0x (Bronze) 🥉') }}</span>
                            </div>
                        </div>
                    </div>

                    <button wire:click="checkout" class="nb-btn w-full text-center bg-espresso text-cream text-sm py-3">
                        ☕ PESAN SEKARANG
                    </button>
                @endif
            </div>
        </div>
    </div>

    <!-- 🧾 Receipt Simulation Modal -->
    @if($showReceiptModal && !empty($lastTxData))
        <div class="fixed inset-0 z-50 flex items-center justify-center nb-modal-overlay p-4">
            <div class="w-full max-w-sm nb-modal bg-cream dark:bg-coffee-950 p-5 space-y-4">
                <div class="flex justify-between items-center">
                    <h3 class="text-xs font-black text-espresso dark:text-cream uppercase tracking-wider">🧾 Struk Pesanan</h3>
                    <button wire:click="closeReceipt" class="nb-btn bg-berry text-white text-xs px-2 py-1">Tutup</button>
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
                        <p>ID STRUK: #{{ str_pad($lastTxData['id'], 5, '0', STR_PAD_LEFT) }}</p>
                        <p>TANGGAL : {{ $lastTxData['date'] }}</p>
                        <p>PELANGGAN: {{ auth()->user()->name }} ({{ auth()->user()->tier_status }})</p>
                    </div>
                    
                    <div class="receipt-dashed my-2"></div>
                    
                    <!-- Items Purchased -->
                    <div class="text-[9px] text-coffee-800 font-mono space-y-1">
                        @foreach($lastTxData['items'] as $item)
                            <div class="flex justify-between">
                                <span>{{ $item['qty'] }}x {{ $item['nama'] }} (Manis: {{ $item['manis'] }}/5)</span>
                                <span>Rp{{ number_format($item['harga'] * $item['qty'], 0, ',', '.') }}</span>
                            </div>
                        @endforeach
                    </div>
                    
                    <div class="receipt-dashed my-2"></div>
                    
                    <!-- Totals Breakdown -->
                    <div class="text-[9px] text-coffee-800 font-mono space-y-0.5">
                        <div class="flex justify-between">
                            <span>SUBTOTAL</span>
                            <span>Rp{{ number_format($lastTxData['subtotal'], 0, ',', '.') }}</span>
                        </div>
                        @if($lastTxData['discount_percent'] > 0)
                            <div class="flex justify-between text-espresso font-black">
                                <span>TOTAL POTONGAN ({{ $lastTxData['discount_percent'] }}%)</span>
                                <span>-Rp{{ number_format($lastTxData['discount_amount'], 0, ',', '.') }}</span>
                            </div>
                        @endif
                        <div class="receipt-dashed my-1"></div>
                        <div class="flex justify-between text-xs font-black text-espresso">
                            <span>TOTAL BAYAR</span>
                            <span>Rp{{ number_format($lastTxData['final_total'], 0, ',', '.') }}</span>
                        </div>
                    </div>
                    
                    <div class="receipt-dashed my-2"></div>
                    
                    <!-- Loyalty Feed -->
                    <div class="text-[9px] text-coffee-800 font-mono text-center space-y-0.5">
                        <p class="font-black">POIN YANG DIDAPAT: +{{ $lastTxData['points_earned'] }} POIN</p>
                        <p>SALDO AKHIR POIN: {{ number_format($lastTxData['total_points'], 0, ',', '.') }} POIN</p>
                        @if($lastTxData['tier_changed'])
                            <p class="mt-1 text-xs font-black text-berry uppercase star-pulse">🎉 NAIK KE TIER {{ strtoupper($lastTxData['new_tier']) }}! 🎉</p>
                        @endif
                        <p class="mt-2 text-xs font-black uppercase tracking-wider">*** TERIMA KASIH ***</p>
                    </div>
                    
                    <!-- Barcode -->
                    <div class="receipt-barcode mt-3"></div>
                </div>
                
                <div class="flex gap-2 pt-2">
                    <button onclick="window.print()" class="nb-btn flex-1 bg-yellow-y2k text-espresso text-xs">🖨️ Cetak Struk</button>
                    <button wire:click="closeReceipt" class="nb-btn flex-1 bg-espresso text-cream text-xs">Kembali</button>
                </div>
            </div>
        </div>
    @endif
</div>
