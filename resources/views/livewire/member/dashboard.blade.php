<div class="p-4 sm:p-6 space-y-6">
    <!-- TICKER -->
    <div class="ticker-bar bg-caramel text-espresso py-2">
        <div class="marquee inline-block text-xs font-extrabold tracking-widest uppercase">
            ☕ SMART COFFEE LOYALTY — KUMPULKAN POIN, NAIK TIER, DAPATKAN PROMO PERSONAL! — POWERED BY KNN MACHINE LEARNING — ☕
        </div>
    </div>

    <!-- HEADER -->
    <div>
        <h1 class="text-3xl font-black text-espresso dark:text-cream uppercase tracking-tight">☕ Dashboard Member</h1>
        <p class="text-coffee-600 dark:text-coffee-300 font-semibold mt-1">Selamat datang kembali, <span class="text-berry font-black">{{ $user->name }}</span>! Kelola keanggotaan Anda.</p>
    </div>

    <!-- TOP GRID: Kartu Tier & Rekomendasi KNN -->
    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">

        <!-- KARTU LOYALTY TIER (NEO BRUTAL EDITION) -->
        <div class="nb-card p-6 flex flex-col justify-between min-h-[340px] relative overflow-hidden
            @if($user->tier_status === 'Gold') bg-caramel @elseif($user->tier_status === 'Silver') bg-zinc-300 @else bg-orange-y2k @endif">

            <!-- Y2K decorative elements -->
            <div class="absolute -right-6 -top-6 w-28 h-28 rounded-full bg-white/20 star-pulse"></div>
            <div class="absolute -left-4 -bottom-4 w-20 h-20 rounded-full bg-black/10 float-bean"></div>
            <div class="absolute right-4 bottom-8 text-6xl opacity-20 star-pulse" style="animation-delay:1s">☕</div>

            <div class="relative z-10 flex justify-between items-start">
                <div>
                    <span class="nb-badge bg-espresso text-cream">
                        @if($user->tier_status === 'Gold') 👑 @elseif($user->tier_status === 'Silver') 🥈 @else 🥉 @endif
                        {{ $user->tier_status }} MEMBER
                    </span>
                    <h2 class="text-2xl font-black mt-3 tracking-tight text-espresso">{{ $user->name }}</h2>
                    <p class="text-xs font-bold text-espresso/60 mt-0.5">{{ $user->email }}</p>
                </div>
                <div class="text-right">
                    <p class="text-[9px] uppercase font-black tracking-widest text-espresso/60">Saldo Poin</p>
                    <p class="text-4xl font-black tracking-tighter text-espresso">{{ number_format($user->total_poin, 0, ',', '.') }}</p>
                </div>
            </div>

            <div class="relative z-10 space-y-3">
                @if($user->tier_status !== 'Gold')
                    <div class="space-y-1.5">
                        <div class="flex justify-between text-xs font-black text-espresso/80">
                            <span>Progress ke <span class="text-espresso">{{ $nextTier }}</span></span>
                            <span>Rp {{ number_format($user->total_pengeluaran, 0, ',', '.') }} / {{ number_format($targetSpending, 0, ',', '.') }}</span>
                        </div>
                        <div class="nb-progress bg-white/50">
                            <div class="nb-progress-fill bg-espresso" style="width: {{ $progressPercent }}%"></div>
                        </div>
                        <p class="text-[10px] text-right font-bold text-espresso/60">Sisa Rp {{ number_format($targetSpending - $user->total_pengeluaran, 0, ',', '.') }} untuk naik tier.</p>
                    </div>
                @else
                    <div class="nb-card-sm bg-espresso/20 p-2 text-center">
                        <p class="text-xs font-black text-espresso">👑 TIER TERTINGGI TELAH DICAPAI!</p>
                        <p class="text-[10px] text-espresso/60 font-bold mt-0.5">Total Belanja: Rp {{ number_format($user->total_pengeluaran, 0, ',', '.') }}</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- REKOMENDASI PROMO PERSONAL (KNN) -->
        <div class="lg:col-span-2 nb-card bg-cream dark:bg-coffee-900 p-6 flex flex-col justify-between space-y-4">
            <div>
                <div class="flex items-center gap-2 flex-wrap">
                    <span class="nb-badge bg-purple-y2k text-espresso">🧠 Smart Personalized Reward</span>
                    <span class="nb-badge bg-blue-y2k text-espresso">KNN Algorithm</span>
                </div>
                <h2 class="text-2xl font-black text-espresso dark:text-cream mt-3 uppercase">Rekomendasi Hari Ini</h2>
                <p class="text-xs text-coffee-600 dark:text-coffee-300 mt-1 leading-relaxed font-semibold">
                    Sistem mendeteksi preferensi Anda sebagai <span class="nb-badge bg-pink-y2k text-espresso">"{{ $userLabel }}"</span> berdasarkan:
                    Sweetness {{ $avgSweetness }}/5 • Coffee Ratio {{ round($coffeeRatio*100) }}% • Avg/Tx Rp {{ number_format($avgSpending, 0, ',', '.') }}
                </p>
            </div>

            <!-- Kupon Voucher (Neo Brutal Ticket Edition) -->
            <div class="ticket-container bg-yellow-y2k p-5 flex flex-col sm:flex-row justify-between items-center gap-4 relative">
                <!-- Y2K stars -->
                <div class="absolute top-1 left-2 flex gap-1">
                    <span class="y2k-star"></span>
                    <span class="y2k-star bg-blue-y2k"></span>
                </div>
                
                <div class="space-y-1 text-center sm:text-left z-10">
                    <p class="text-[9px] font-black text-espresso/70 uppercase tracking-widest">🎫 KUPON KAMPANYE CRM PERSONAL</p>
                    <p class="text-sm font-black text-espresso leading-snug">{{ $recommendedVoucher }}</p>
                    <p class="text-xs font-bold text-espresso/80">Menu Spesial: <strong class="text-espresso font-black underline">{{ $recommendedMenu }}</strong></p>
                </div>
                
                <!-- Ticket Separator / Stub -->
                <div class="hidden sm:block border-l-2 border-dashed border-black h-12 my-auto mx-4 opacity-40"></div>
                
                <div class="text-center z-10 w-full sm:w-auto">
                    <div class="nb-btn bg-espresso text-cream text-sm font-mono cursor-pointer select-all y2k-shimmer py-2 px-4 border-2 border-black inline-block" onclick="navigator.clipboard.writeText('{{ $voucherCode }}'); alert('Kode Voucher {{ $voucherCode }} berhasil disalin!');">
                        {{ $voucherCode }}
                    </div>
                    <span class="text-[9px] text-espresso/70 block mt-1.5 font-black uppercase tracking-wider">Klik untuk salin</span>
                </div>
            </div>

            <!-- Penjelasan Tetangga Terdekat -->
            @if(count($nearestNeighbors) > 0)
                <div class="retro-divider"></div>
                <div>
                    <p class="text-[9px] font-black text-coffee-500 uppercase tracking-widest mb-2">🔬 3 Tetangga Terdekat (Profil Mirip Anda):</p>
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-2">
                        @foreach($nearestNeighbors as $idx => $nb)
                            <div class="nb-card-sm bg-white dark:bg-coffee-800 p-3 text-[10px]">
                                <p class="font-black text-espresso dark:text-cream truncate">{{ $idx + 1 }}. {{ $nb['name'] }}</p>
                                <p class="mt-0.5 font-bold text-coffee-500">Jarak: <span class="text-purple-y2k font-mono font-black">{{ number_format($nb['distance'], 4) }}</span></p>
                                <p class="font-semibold text-coffee-400 truncate">{{ str_replace(['Pecinta', 'Pelanggan'], '', $nb['label']) }}</p>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </div>

    <!-- MID GRID: TASTE PROFILE & WHATSAPP PHONE SIMULATOR -->
    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
        <!-- Taste Profile Card -->
        <div class="lg:col-span-2 nb-card bg-cream dark:bg-coffee-900 p-6 space-y-4">
            <div>
                <h3 class="text-lg font-black text-espresso dark:text-cream uppercase flex items-center gap-1.5">
                    👅 Karakter Selera Citarasa Anda
                </h3>
                <p class="text-xs text-coffee-600 dark:text-coffee-300 mt-1 font-semibold">
                    Profil cita rasa Anda dianalisis berdasarkan kecenderungan pembelian menu kopi vs minuman manis.
                </p>
            </div>
            
            <div class="nb-card-sm bg-white dark:bg-coffee-800 p-5 space-y-4">
                <div class="flex justify-between items-center flex-wrap gap-2">
                    <span class="text-xs font-black text-espresso dark:text-cream">Signature Anda:</span>
                    <span class="nb-badge bg-caramel text-espresso text-[10px] font-black">{{ $flavorProfile }}</span>
                </div>
                
                <div class="space-y-3 pt-2">
                    <!-- Bitter level -->
                    <div class="space-y-1">
                        <div class="flex justify-between text-[10px] font-black text-coffee-600 dark:text-coffee-300 uppercase">
                            <span>☕ Tingkat Kepahitan Kopi (Bitterness)</span>
                            <span>{{ $bitternessPercent }}%</span>
                        </div>
                        <div class="nb-progress bg-zinc-200 dark:bg-zinc-750">
                            <div class="nb-progress-fill bg-espresso" style="width: {{ $bitternessPercent }}%"></div>
                        </div>
                    </div>
                    
                    <!-- Sweetness level -->
                    <div class="space-y-1">
                        <div class="flex justify-between text-[10px] font-black text-coffee-600 dark:text-coffee-300 uppercase">
                            <span>🍩 Tingkat Kemanisan Minuman (Sweetness)</span>
                            <span>{{ $sweetnessPercent }}%</span>
                        </div>
                        <div class="nb-progress bg-zinc-200 dark:bg-zinc-750">
                            <div class="nb-progress-fill bg-pink-y2k" style="width: {{ $sweetnessPercent }}%"></div>
                        </div>
                    </div>
                </div>
                
                <p class="text-[10px] text-coffee-500 font-semibold mt-1">
                    💡 Rekomendasi kupon diskon personal Anda secara otomatis menyesuaikan dengan profil rasa di atas untuk kepuasan belanja Anda.
                </p>
            </div>
        </div>

        <!-- Phone Simulator Mockup -->
        <div class="nb-card bg-cream dark:bg-coffee-900 p-6 flex flex-col items-center justify-between space-y-4">
            <div class="text-center w-full">
                <h3 class="text-sm font-black text-espresso dark:text-cream uppercase tracking-wide">
                    📱 Simulasi Push Notifikasi WA
                </h3>
                <p class="text-[10px] text-coffee-600 dark:text-coffee-400 font-semibold mt-0.5">
                    Tampilan pesan WhatsApp real-time yang dikirim oleh sistem CRM ke HP Anda.
                </p>
            </div>
            
            <div class="phone-mockup">
                <div class="phone-screen custom-scrollbar">
                    <!-- Status Bar -->
                    <div class="phone-header">
                        <span>SMART COFFEE NET</span>
                        <span>17:15 🔋 100%</span>
                    </div>
                    
                    <!-- Phone Chat Area -->
                    <div class="p-3 flex-1 flex flex-col gap-3 justify-end overflow-y-auto">
                        @php
                            $waNotifications = $notifications->where('type', 'WhatsApp')->take(2);
                        @endphp
                        @forelse($waNotifications as $waNotif)
                            <div class="wa-bubble-in text-left">
                                <span class="block text-[8px] font-black text-berry uppercase tracking-wide mb-0.5">Smart Coffee CRM</span>
                                <p class="text-[9px] text-coffee-900 font-semibold">{{ $waNotif->message }}</p>
                                <span class="block text-[7px] text-right text-coffee-400 mt-1 font-bold">{{ $waNotif->created_at->diffForHumans() }}</span>
                            </div>
                        @empty
                            <div class="flex-1 flex items-center justify-center text-center p-4">
                                <p class="text-[10px] text-coffee-400 font-black">Tidak ada pesan WhatsApp masuk.</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- BOTTOM GRID -->
    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
        <!-- Keuntungan Tier -->
        <div class="nb-card bg-cream dark:bg-coffee-900 p-6 space-y-4">
            <h2 class="text-lg font-black text-espresso dark:text-cream uppercase">🏆 Keuntungan Tier</h2>

            <div class="space-y-3">
                <div class="nb-card-sm bg-caramel/20 p-3 flex items-start gap-3">
                    <span class="text-2xl">👑</span>
                    <div>
                        <h4 class="text-xs font-black text-caramel uppercase tracking-wider">Gold (> Rp 1.5M)</h4>
                        <ul class="text-[11px] text-coffee-600 dark:text-coffee-300 mt-1 list-disc pl-4 space-y-0.5 font-semibold">
                            <li>Diskon otomatis 10% setiap transaksi</li>
                            <li>Prioritas antrean pre-order</li>
                            <li>Akses secret menu premium</li>
                        </ul>
                    </div>
                </div>

                <div class="nb-card-sm bg-zinc-200/40 p-3 flex items-start gap-3">
                    <span class="text-2xl">🥈</span>
                    <div>
                        <h4 class="text-xs font-black text-zinc-500 uppercase tracking-wider">Silver (500k - 1.5M)</h4>
                        <ul class="text-[11px] text-coffee-600 dark:text-coffee-300 mt-1 list-disc pl-4 space-y-0.5 font-semibold">
                            <li>Diskon otomatis 5% setiap transaksi</li>
                            <li>Kopi gratis di hari ulang tahun</li>
                        </ul>
                    </div>
                </div>

                <div class="nb-card-sm bg-orange-y2k/15 p-3 flex items-start gap-3">
                    <span class="text-2xl">🥉</span>
                    <div>
                        <h4 class="text-xs font-black text-orange-y2k uppercase tracking-wider">Bronze (Awal)</h4>
                        <ul class="text-[11px] text-coffee-600 dark:text-coffee-300 mt-1 list-disc pl-4 space-y-0.5 font-semibold">
                            <li>Kumpulkan poin belanja standar</li>
                            <li>Tukar poin di katalog reward</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <!-- Log Notifikasi -->
        <div class="nb-card bg-cream dark:bg-coffee-900 p-6 space-y-4">
            <h2 class="text-lg font-black text-espresso dark:text-cream uppercase">📩 Kotak Masuk</h2>
            <p class="text-[10px] font-bold text-coffee-500 uppercase tracking-wider">Notifikasi Simulasi WA & Email</p>

            <div class="space-y-3 max-h-[280px] overflow-y-auto pr-1 text-xs">
                @forelse($notifications as $notif)
                    <div class="nb-card-sm bg-white dark:bg-coffee-800 p-3 space-y-1.5">
                        <div class="flex justify-between items-center">
                            <span class="nb-badge @if($notif->type === 'WhatsApp') bg-matcha text-espresso @else bg-blue-y2k text-espresso @endif text-[8px]">
                                @if($notif->type === 'WhatsApp') 📱 @else 📧 @endif {{ $notif->type }}
                            </span>
                            <span class="text-[9px] text-coffee-400 font-bold">{{ $notif->created_at->diffForHumans() }}</span>
                        </div>
                        <p class="text-coffee-600 dark:text-coffee-300 leading-relaxed font-semibold">{{ $notif->message }}</p>
                    </div>
                @empty
                    <div class="text-center py-8">
                        <span class="text-4xl block mb-2">📭</span>
                        <p class="text-xs text-coffee-400 font-bold">Belum ada notifikasi otomatis.</p>
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Riwayat Transaksi -->
        <div class="nb-card bg-cream dark:bg-coffee-900 p-6 space-y-4">
            <h2 class="text-lg font-black text-espresso dark:text-cream uppercase">🧾 Riwayat Pembelian</h2>

            <div class="space-y-3 max-h-[280px] overflow-y-auto pr-1 text-xs">
                @forelse($transactions as $tx)
                    <div class="nb-card-sm bg-white dark:bg-coffee-800 p-3 space-y-2">
                        <div class="flex justify-between items-center font-black">
                            <span class="text-espresso dark:text-cream">#{{ str_pad($tx->id, 5, '0', STR_PAD_LEFT) }}</span>
                            <span class="text-berry">Rp {{ number_format($tx->total_bayar, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between items-center text-[10px] text-coffee-400 font-bold">
                            <span>{{ $tx->created_at->format('d M Y, H:i') }}</span>
                            <span class="nb-badge bg-blue-y2k text-espresso text-[8px]">{{ $tx->jumlah_item }} Item</span>
                        </div>

                        <div class="retro-divider !opacity-15"></div>
                        <div class="text-[10px] text-coffee-500 space-y-0.5 font-semibold">
                            @foreach($tx->details as $d)
                                <div class="flex justify-between">
                                    <span>{{ $d->nama_menu }}</span>
                                    <span>☕ {{ $d->rasa_manis }}/5 • Rp{{ number_format($d->harga, 0, ',', '.') }}</span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @empty
                    <div class="text-center py-8">
                        <span class="text-4xl block mb-2 float-bean">☕</span>
                        <p class="text-xs text-coffee-400 font-bold">Belum ada riwayat pembelian.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>
