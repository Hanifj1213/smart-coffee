
<div class="p-4 sm:p-6 space-y-6">
    <!-- TICKER -->
    <div class="ticker-bar bg-espresso text-cream py-2">
        <div class="marquee inline-block text-xs font-extrabold tracking-widest uppercase">
            ☕ KASIR POS — PENCATATAN TRANSAKSI REAL-TIME — LOYALTY ENGINE OTOMATIS — POIN & TIER UPDATE LANGSUNG — ☕ KASIR POS —
        </div>
    </div>

    <!-- HEADER -->
    <div class="flex flex-col justify-between gap-4 md:flex-row md:items-center">
        <div>
            <h1 class="text-3xl font-black text-espresso dark:text-cream uppercase tracking-tight">🧾 Smart Coffee POS</h1>
            <p class="text-coffee-600 dark:text-coffee-300 font-semibold mt-1">Pencatatan transaksi real-time untuk pembaruan loyalitas pelanggan.</p>
        </div>
        <button wire:click="$set('showCreateModal', true)" class="nb-btn bg-mint-y2k text-espresso flex items-center gap-2">
            <span class="text-lg">➕</span> Daftar Member Baru
        </button>
    </div>

    <!-- Alert Success -->
    @if($successMessage)
        <div class="nb-card bg-matcha/20 p-4 flex items-start gap-3">
            <span class="text-2xl star-pulse">✅</span>
            <div>
                <h3 class="font-black text-espresso dark:text-cream">{{ $successMessage }}</h3>
                <p class="text-sm font-bold text-coffee-600">Mendapatkan <span class="nb-badge bg-caramel text-espresso">+{{ $pointsEarned }} Poin</span></p>
                @if($tierChanged)
                    <p class="mt-1"><span class="nb-badge bg-pink-y2k text-espresso">🚀 UPGRADE TIER: {{ $newTier }}!</span></p>
                @endif
            </div>
        </div>
    @endif

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
        <!-- KOLOM KIRI: Cari Pelanggan & Katalog Menu -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Cari Pelanggan -->
            <div class="nb-card bg-cream dark:bg-coffee-900 p-6">
                <h2 class="text-lg font-black text-espresso dark:text-cream uppercase mb-4">1️⃣ Hubungkan Member</h2>

                <div class="relative mb-4">
                    <input type="text" wire:model.live="search" placeholder="🔍 Cari nama, email, atau no HP member..." class="nb-input w-full bg-white dark:bg-coffee-800 px-4 py-3 pl-4 text-sm text-espresso dark:text-cream" />
                </div>

                @if($search)
                    <div class="mb-4 max-h-48 overflow-y-auto nb-card-sm bg-white dark:bg-coffee-800 divide-y-2 divide-black/10">
                        @forelse($members as $member)
                            <div wire:click="selectUser({{ $member->id }})" class="flex cursor-pointer items-center justify-between p-3 text-sm hover:bg-caramel/20 transition duration-100">
                                <div>
                                    <p class="font-black text-espresso dark:text-cream">{{ $member->name }}</p>
                                    <p class="text-[11px] text-coffee-500 font-semibold">{{ $member->email }} • {{ $member->no_hp }}</p>
                                </div>
                                <span class="nb-badge @if($member->tier_status === 'Gold') bg-caramel text-espresso @elseif($member->tier_status === 'Silver') bg-zinc-300 text-espresso @else bg-orange-y2k text-espresso @endif">
                                    {{ $member->tier_status }}
                                </span>
                            </div>
                        @empty
                            <p class="p-4 text-center text-xs text-coffee-400 font-bold">Member tidak ditemukan.</p>
                        @endforelse
                    </div>
                @endif

                @if($selectedUser)
                    <div class="nb-card bg-gradient-to-r from-caramel/30 to-pink-y2k/20 p-4 flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="flex h-12 w-12 items-center justify-center rounded-full border-3 border-black bg-caramel text-espresso font-black text-lg shadow-[2px_2px_0px_#1a1a1a]">
                                {{ $selectedUser->initials() }}
                            </div>
                            <div>
                                <h3 class="font-black text-espresso dark:text-cream">{{ $selectedUser->name }}</h3>
                                <p class="text-[11px] text-coffee-500 font-semibold">{{ $selectedUser->email }}</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-3">
                            <div class="text-right">
                                <span class="nb-badge @if($selectedUser->tier_status === 'Gold') bg-caramel text-espresso @elseif($selectedUser->tier_status === 'Silver') bg-zinc-300 text-espresso @else bg-orange-y2k text-espresso @endif">
                                    {{ $selectedUser->tier_status }}
                                </span>
                                <p class="text-xs font-black text-coffee-600 mt-1">Poin: {{ number_format($selectedUser->total_poin, 0, ',', '.') }}</p>
                            </div>
                            <button wire:click="deselectUser" class="nb-btn bg-berry text-white text-xs px-2 py-1">✕</button>
                        </div>
                    </div>
                @else
                    <div class="nb-card-sm bg-white/60 dark:bg-coffee-800/30 p-5 text-center border-dashed">
                        <span class="text-4xl mb-2 block">☕</span>
                        <p class="text-sm font-bold text-coffee-500">Belum ada member terpilih.</p>
                        <p class="text-xs text-coffee-400 mt-1">Gunakan pencarian di atas atau buat member baru.</p>
                    </div>
                @endif
            </div>

            <!-- Katalog Menu -->
            <div class="nb-card bg-cream dark:bg-coffee-900 p-6">
                <h2 class="text-lg font-black text-espresso dark:text-cream uppercase mb-4">2️⃣ Pilih Menu</h2>

                <!-- Coffee -->
                <div class="mb-6">
                    <h3 class="mb-3 text-xs font-black uppercase tracking-widest text-coffee-600 flex items-center gap-1">☕ Coffee Series</h3>
                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
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
                <div class="mb-6">
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
                <div>
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
        </div>

        <!-- KOLOM KANAN: Keranjang -->
        <div>
            <div class="nb-card bg-cream dark:bg-coffee-900 p-6 space-y-5">
                <h2 class="text-lg font-black text-espresso dark:text-cream uppercase">3️⃣ Keranjang 🛒</h2>

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

                    <div class="space-y-2">
                        <div class="flex justify-between text-sm font-bold text-coffee-600">
                            <span>Subtotal Item:</span>
                            <span>Rp {{ number_format($cartTotal, 0, ',', '.') }}</span>
                        </div>
                        @if($discountPercent > 0)
                            <div class="flex justify-between text-sm font-bold text-matcha">
                                <span>🎉 Diskon {{ $discountPercent }}%:</span>
                                <span>−Rp {{ number_format($discountAmount, 0, ',', '.') }}</span>
                            </div>
                        @endif
                        @if($pointsRedeemed > 0)
                            <div class="flex justify-between text-sm font-bold text-purple-y2k">
                                <span>🪙 Potongan Poin ({{ number_format($pointsRedeemed, 0, ',', '.') }} Poin):</span>
                                <span>−Rp {{ number_format($pointsDiscountAmount, 0, ',', '.') }}</span>
                            </div>
                        @endif
                        <div class="flex justify-between text-lg font-black text-espresso dark:text-cream retro-divider pt-3">
                            <span>TOTAL BAYAR:</span>
                            <span class="text-berry">Rp {{ number_format($finalTotal, 0, ',', '.') }}</span>
                        </div>
                        @if($selectedUser)
                            @if($selectedUser->total_poin >= 1000)
                                <div class="nb-card-sm bg-purple-y2k/10 p-3 flex items-center justify-between text-xs">
                                    <div class="flex items-center gap-2">
                                        <input type="checkbox" wire:model.live="usePoints" id="usePointsCheckbox" class="nb-checkbox rounded text-purple-y2k focus:ring-purple-y2k border-2 border-black" />
                                        <label for="usePointsCheckbox" class="font-black text-espresso select-none cursor-pointer">
                                            Tukar Poin Member (Kelipatan 1.000)
                                        </label>
                                    </div>
                                    <span class="font-black text-coffee-600">
                                        Tersedia: {{ number_format($selectedUser->total_poin, 0, ',', '.') }} Poin
                                    </span>
                                </div>
                            @endif
                            <div class="nb-card-sm bg-yellow-y2k/20 p-3 text-xs space-y-1">
                                <div class="flex justify-between font-bold text-espresso">
                                    <span>Estimasi Poin Masuk:</span>
                                    <span class="font-black text-sm">+{{ (int)(floor($finalTotal / 10000) * 100 * ($selectedUser->tier_status === 'Gold' ? 1.5 : ($selectedUser->tier_status === 'Silver' ? 1.2 : 1.0))) }} Poin</span>
                                </div>
                                <div class="flex justify-between text-[10px] text-coffee-500 font-bold">
                                    <span>Multiplier:</span>
                                    <span>{{ $selectedUser->tier_status === 'Gold' ? '1.5x (Gold) 👑' : ($selectedUser->tier_status === 'Silver' ? '1.2x (Silver) 🥈' : '1.0x (Bronze) 🥉') }}</span>
                                </div>
                            </div>
                        @endif
                    </div>

                    <!-- 🎟️ Coupon Selection Widget -->
                    <div class="nb-card-sm bg-purple-y2k/10 p-3.5 space-y-2">
                        <span class="font-black text-espresso dark:text-cream text-[10px] uppercase tracking-wider block">🎟️ Masukkan Kupon Promo</span>
                        <div class="flex gap-2">
                            <input type="text" wire:model="couponCode" placeholder="Contoh: MISSYOU20" class="nb-input flex-1 px-3 py-1.5 text-xs bg-white text-espresso border-2 border-black" />
                            <button type="button" wire:click="applyCoupon" class="nb-btn bg-purple-y2k text-espresso text-xs py-1 px-3">Terapkan</button>
                        </div>
                        @if($couponApplied)
                            <p class="text-[10px] text-emerald-600 dark:text-emerald-400 font-extrabold">🎉 Berhasil: Potongan {{ $couponDiscountPercent }}%!</p>
                        @endif
                        @if($couponError)
                            <p class="text-[10px] text-berry font-extrabold">❌ {{ $couponError }}</p>
                        @endif
                    </div>

                    @error('transaction')
                        <p class="text-xs text-berry font-black text-center">{{ $message }}</p>
                    @enderror

                    <button wire:click="submitTransaction" class="nb-btn w-full text-center bg-espresso text-cream text-sm py-3 @if(!$selectedUserId) opacity-50 cursor-not-allowed @endif" @if(!$selectedUserId) disabled @endif>
                        ☕ BAYAR & SIMPAN TRANSAKSI
                    </button>
                @endif
            </div>
        </div>
    </div>

    <!-- Modal Form Member Baru -->
    @if($showCreateModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center nb-modal-overlay p-4">
            <div class="w-full max-w-md nb-modal bg-cream dark:bg-coffee-900 p-6">
                <div class="flex justify-between items-center mb-5">
                    <h3 class="text-xl font-black text-espresso dark:text-cream uppercase">➕ Daftar Member</h3>
                    <button wire:click="$set('showCreateModal', false)" class="nb-btn bg-berry text-white text-xs px-2 py-1">✕</button>
                </div>

                <form wire:submit.prevent="createMember" class="space-y-4">
                    <div>
                        <label class="block text-xs font-black text-coffee-600 uppercase mb-1.5 tracking-wider">Nama Lengkap</label>
                        <input type="text" wire:model="newMemberName" placeholder="Contoh: Rian Hidayat" class="nb-input w-full bg-white dark:bg-coffee-800 px-3 py-2 text-sm text-espresso dark:text-cream" required />
                        @error('newMemberName') <p class="text-xs text-berry font-black mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-black text-coffee-600 uppercase mb-1.5 tracking-wider">Email</label>
                        <input type="email" wire:model="newMemberEmail" placeholder="rian@example.com" class="nb-input w-full bg-white dark:bg-coffee-800 px-3 py-2 text-sm text-espresso dark:text-cream" required />
                        @error('newMemberEmail') <p class="text-xs text-berry font-black mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-black text-coffee-600 uppercase mb-1.5 tracking-wider">No WhatsApp / HP</label>
                        <input type="text" wire:model="newMemberPhone" placeholder="081234567890" class="nb-input w-full bg-white dark:bg-coffee-800 px-3 py-2 text-sm text-espresso dark:text-cream" required />
                        @error('newMemberPhone') <p class="text-xs text-berry font-black mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="pt-4 flex gap-3">
                        <button type="button" wire:click="$set('showCreateModal', false)" class="nb-btn flex-1 bg-zinc-200 text-espresso">Batal</button>
                        <button type="submit" class="nb-btn flex-1 bg-mint-y2k text-espresso">Daftar ☕</button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    <!-- 🧾 Receipt Simulation Modal -->
    @if($showReceiptModal && !empty($lastTxData))
        <div class="fixed inset-0 z-50 flex items-center justify-center nb-modal-overlay p-4">
            <div class="w-full max-w-sm nb-modal bg-cream dark:bg-coffee-950 p-5 space-y-4">
                <div class="flex justify-between items-center">
                    <h3 class="text-xs font-black text-espresso dark:text-cream uppercase tracking-wider">🧾 Struk Transaksi</h3>
                    <button wire:click="$set('showReceiptModal', false)" class="nb-btn bg-berry text-white text-xs px-2 py-1">Tutup</button>
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
                        <p>PELANGGAN: {{ $lastTxData['customer_name'] }} ({{ $lastTxData['tier'] }})</p>
                        <p>STATUS TIER: {{ $lastTxData['tier'] }} Member</p>
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
                        @if(isset($lastTxData['points_redeemed']) && $lastTxData['points_redeemed'] > 0)
                            <div class="flex justify-between text-espresso font-black">
                                <span>POTONGAN POIN ({{ number_format($lastTxData['points_redeemed'], 0, ',', '.') }})</span>
                                <span>-Rp{{ number_format($lastTxData['points_discount_amount'], 0, ',', '.') }}</span>
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
                        <p class="mt-2 text-xs font-black uppercase tracking-wider">*** TERIMA KASIH ***</p>
                    </div>
                    
                    <!-- Barcode -->
                    <div class="receipt-barcode mt-3"></div>
                </div>
                
                <div class="flex gap-2 pt-2">
                    <button onclick="window.print()" class="nb-btn flex-1 bg-yellow-y2k text-espresso text-xs">🖨️ Cetak Struk</button>
                    <button wire:click="$set('showReceiptModal', false)" class="nb-btn flex-1 bg-espresso text-cream text-xs">Kembali</button>
                </div>
            </div>
        </div>
    @endif
</div>
