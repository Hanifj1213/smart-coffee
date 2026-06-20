<div class="p-4 sm:p-6 space-y-6" x-data="{ activeTab: 'analytics' }">
    <!-- ☕ TICKER BAR -->
    <div class="ticker-bar bg-espresso text-cream py-2">
        <div class="marquee inline-block text-xs font-extrabold tracking-widest uppercase">
            ☕ Smart Coffee — OPERATIONAL & ANALYTICAL DASHBOARD — DATA-DRIVEN DECISION MAKING — CUSTOMER LOYALTY ENGINE — KNN PERSONALIZATION — ☕ Smart Coffee —
        </div>
    </div>

    <!-- HEADER -->
    <div class="flex flex-col justify-between gap-4 md:flex-row md:items-center">
        <div>
            <h1 class="text-3xl font-black text-espresso dark:text-cream uppercase tracking-tight">☕ Dashboard Analitik</h1>
            <p class="text-coffee-600 dark:text-coffee-300 font-semibold mt-1">Analisis loyalitas pelanggan & klasterisasi preferensi berbasis algoritma KNN.</p>
        </div>
        <div class="flex flex-wrap gap-2">
            <button wire:click="scanChurnPrevention" class="nb-btn bg-berry text-white flex items-center gap-2">
                <span class="text-lg">🚨</span>
                Jalankan Churn Prevention
            </button>
        </div>
    </div>

    <!-- EXPORT LAPORAN -->
    <div class="nb-card bg-cream dark:bg-coffee-900 p-4 flex flex-col sm:flex-row sm:items-center gap-3 justify-between">
        <div>
            <h3 class="text-sm font-black text-espresso dark:text-cream uppercase">📤 Ekspor Laporan</h3>
            <p class="text-[11px] text-coffee-500 font-semibold">Unduh data member & transaksi, atau cetak laporan penjualan sebagai PDF.</p>
        </div>
        <div class="flex flex-wrap gap-2">
            <a href="{{ route('admin.reports.members') }}" class="nb-btn bg-matcha text-espresso text-xs flex items-center gap-1.5">📄 Member (CSV)</a>
            <a href="{{ route('admin.reports.transactions') }}" class="nb-btn bg-blue-y2k text-espresso text-xs flex items-center gap-1.5">📊 Transaksi (Excel)</a>
            <a href="{{ route('admin.reports.sales-pdf') }}" target="_blank" class="nb-btn bg-caramel text-espresso text-xs flex items-center gap-1.5">🧾 Penjualan (PDF)</a>
        </div>
    </div>

    <!-- Alert Message -->
    @if($successMsg)
        <div class="nb-card bg-matcha/20 p-4 flex items-start gap-3">
            <span class="text-2xl star-pulse">✅</span>
            <div class="flex-1">
                <p class="font-extrabold text-sm text-espresso dark:text-cream">{{ $successMsg }}</p>
            </div>
            <button @click="$wire.set('successMsg', '')" class="text-espresso hover:text-berry font-black text-xl">×</button>
        </div>
    @endif

    <!-- METRICS GRID -->
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
        <!-- Sales -->
        <div class="nb-card bg-caramel p-5 flex items-center gap-4">
            <span class="text-4xl float-bean">💰</span>
            <div>
                <p class="text-[10px] font-black text-espresso uppercase tracking-widest">Total Penjualan</p>
                <p class="text-xl font-black text-espresso mt-1">Rp {{ number_format($totalSales, 0, ',', '.') }}</p>
            </div>
        </div>

        <!-- Members -->
        <div class="nb-card bg-blue-y2k p-5 flex items-center gap-4">
            <span class="text-4xl float-bean" style="animation-delay: 0.5s;">👥</span>
            <div>
                <p class="text-[10px] font-black text-espresso uppercase tracking-widest">Total Member</p>
                <p class="text-xl font-black text-espresso mt-1">{{ $totalMembers }} Pelanggan</p>
            </div>
        </div>

        <!-- Avg Spending -->
        <div class="nb-card bg-mint-y2k p-5 flex items-center gap-4">
            <span class="text-4xl float-bean" style="animation-delay: 1s;">☕</span>
            <div>
                <p class="text-[10px] font-black text-espresso uppercase tracking-widest">Rata-rata Belanja</p>
                <p class="text-xl font-black text-espresso mt-1">Rp {{ number_format($avgSpending, 0, ',', '.') }}</p>
            </div>
        </div>

        <!-- Transactions -->
        <div class="nb-card bg-pink-y2k p-5 flex items-center gap-4">
            <span class="text-4xl float-bean" style="animation-delay: 1.5s;">🧾</span>
            <div>
                <p class="text-[10px] font-black text-espresso uppercase tracking-widest">Total Transaksi</p>
                <p class="text-xl font-black text-espresso mt-1">{{ $totalTransactions }} Pesanan</p>
            </div>
        </div>
    </div>

    <!-- TAB NAVIGATION (Neo Brutal) -->
    <div class="flex gap-2 flex-wrap">
        <button @click="activeTab = 'analytics'" :class="activeTab === 'analytics' ? 'bg-caramel text-espresso shadow-[3px_3px_0px_#1a1a1a]' : 'bg-cream text-coffee-600'" class="nb-btn text-xs">📊 Visualisasi Klaster</button>
        <button @click="activeTab = 'churn'" :class="activeTab === 'churn' ? 'bg-berry text-white shadow-[3px_3px_0px_#1a1a1a]' : 'bg-cream text-coffee-600'" class="nb-btn text-xs relative">
            🚨 Potensi Churn
            @if($churnCount > 0)
                <span class="absolute -right-2 -top-2 flex h-5 w-5 items-center justify-center rounded-full bg-espresso text-[9px] font-black text-cream border-2 border-black">{{ $churnCount }}</span>
            @endif
        </button>
        <button @click="activeTab = 'transactions'" :class="activeTab === 'transactions' ? 'bg-blue-y2k text-espresso shadow-[3px_3px_0px_#1a1a1a]' : 'bg-cream text-coffee-600'" class="nb-btn text-xs">🧾 Transaksi Terbaru</button>
    </div>

    <!-- TAB: Visualisasi Klaster -->
    <div x-show="activeTab === 'analytics'" class="grid grid-cols-1 gap-6 lg:grid-cols-3">
        <!-- Scatter Plot -->
        <div class="lg:col-span-2 nb-card bg-cream dark:bg-coffee-900 p-6 space-y-4">
            <div>
                <h2 class="text-xl font-black text-espresso dark:text-cream uppercase">📊 Klaster Pelanggan</h2>
                <p class="text-xs text-coffee-600 dark:text-coffee-300 mt-1 font-semibold">Sweetness vs Coffee Ratio — Data Latih Member untuk Algoritma KNN.</p>
            </div>
            <div class="relative w-full h-[400px] nb-card-sm bg-white dark:bg-coffee-800 p-2">
                <canvas id="clusterScatterPlot"></canvas>
            </div>
        </div>

        <!-- Segment Stats -->
        <div class="nb-card bg-cream dark:bg-coffee-900 p-6 space-y-5">
            <h2 class="text-lg font-black text-espresso dark:text-cream uppercase">🏆 Tier Keanggotaan</h2>

            <div class="space-y-4">
                <!-- Gold -->
                <div class="nb-card-sm bg-caramel/30 p-3 space-y-2">
                    <div class="flex justify-between text-xs font-black">
                        <span class="text-espresso">👑 Gold (> Rp 1.5M)</span>
                        <span class="nb-badge bg-caramel text-espresso">{{ $goldCount }} ({{ $totalMembers > 0 ? round(($goldCount/$totalMembers)*100) : 0 }}%)</span>
                    </div>
                    <div class="nb-progress bg-white">
                        <div class="nb-progress-fill bg-caramel" style="width: {{ $totalMembers > 0 ? ($goldCount/$totalMembers)*100 : 0 }}%"></div>
                    </div>
                </div>

                <!-- Silver -->
                <div class="nb-card-sm bg-zinc-200/50 p-3 space-y-2">
                    <div class="flex justify-between text-xs font-black">
                        <span class="text-espresso">🥈 Silver (500k - 1.5M)</span>
                        <span class="nb-badge bg-zinc-300 text-espresso">{{ $silverCount }} ({{ $totalMembers > 0 ? round(($silverCount/$totalMembers)*100) : 0 }}%)</span>
                    </div>
                    <div class="nb-progress bg-white">
                        <div class="nb-progress-fill bg-zinc-400" style="width: {{ $totalMembers > 0 ? ($silverCount/$totalMembers)*100 : 0 }}%"></div>
                    </div>
                </div>

                <!-- Bronze -->
                <div class="nb-card-sm bg-orange-y2k/20 p-3 space-y-2">
                    <div class="flex justify-between text-xs font-black">
                        <span class="text-espresso">🥉 Bronze (< 500k)</span>
                        <span class="nb-badge bg-orange-y2k text-espresso">{{ $bronzeCount }} ({{ $totalMembers > 0 ? round(($bronzeCount/$totalMembers)*100) : 0 }}%)</span>
                    </div>
                    <div class="nb-progress bg-white">
                        <div class="nb-progress-fill bg-orange-y2k" style="width: {{ $totalMembers > 0 ? ($bronzeCount/$totalMembers)*100 : 0 }}%"></div>
                    </div>
                </div>
            </div>

            <div class="retro-divider my-3"></div>

            <div class="space-y-3">
                <h3 class="text-xs font-black uppercase text-espresso dark:text-cream tracking-wider">Keterangan Klaster KNN:</h3>
                <div class="space-y-2 text-xs font-bold">
                    <div class="flex items-center gap-2">
                        <div class="h-4 w-4 rounded-full border-2 border-black bg-orange-y2k"></div>
                        <span class="text-coffee-700 dark:text-coffee-300">Kopi Strong & Hemat</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <div class="h-4 w-4 rounded-full border-2 border-black bg-matcha"></div>
                        <span class="text-coffee-700 dark:text-coffee-300">Minuman Manis/Kekinian</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <div class="h-4 w-4 rounded-full border-2 border-black bg-purple-y2k"></div>
                        <span class="text-coffee-700 dark:text-coffee-300">Pelanggan Premium</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ROW 2: BROADCASTER & LIVE ACTIVITY FEED -->
    <div x-show="activeTab === 'analytics'" class="grid grid-cols-1 gap-6 lg:grid-cols-3 pt-6">
        <!-- Campaign Broadcaster -->
        <div class="nb-card bg-cream dark:bg-coffee-900 p-6 flex flex-col justify-between space-y-4">
            <div>
                <h3 class="text-lg font-black text-espresso dark:text-cream uppercase flex items-center gap-1.5">
                    📢 Broadcast Kampanye
                </h3>
                <p class="text-xs text-coffee-600 dark:text-coffee-300 mt-1 font-semibold">
                    Kirim pesan WhatsApp & Email promo serentak ke seluruh member terdaftar.
                </p>
            </div>
            
            @if($campaignSuccessMsg)
                <div class="nb-card-sm bg-matcha/20 p-3 text-xs text-emerald-700 font-extrabold">
                    {{ $campaignSuccessMsg }}
                </div>
            @endif

            <form wire:submit.prevent="broadcastCampaign" class="space-y-3">
                <textarea wire:model="campaignText" placeholder="Tulis pesan promo di sini... (Contoh: Dapatkan gratis donat untuk setiap pembelian Americano di hari ini saja!)" class="nb-input w-full bg-white dark:bg-coffee-800 text-xs p-3 text-espresso dark:text-cream border-2 border-black" rows="4" required></textarea>
                @error('campaignText') <p class="text-xs text-berry font-black mt-1">{{ $message }}</p> @enderror
                
                <button type="submit" class="nb-btn w-full bg-berry text-white text-xs py-2 flex items-center justify-center gap-2">
                    ⚡ Kirim Kampanye Sekarang
                </button>
            </form>
        </div>

        <!-- Live Activity Feed -->
        <div class="lg:col-span-2 nb-card bg-cream dark:bg-coffee-900 p-6 space-y-4">
            <div>
                <h3 class="text-lg font-black text-espresso dark:text-cream uppercase flex items-center gap-1.5">
                    ⚡ Feed Aktivitas Terkini
                </h3>
                <p class="text-xs text-coffee-600 dark:text-coffee-300 mt-1 font-semibold">
                    Simulasi log push notifikasi otomatis & aktivitas loyalty member yang real-time.
                </p>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-3 max-h-[220px] overflow-y-auto pr-1 custom-scrollbar text-xs">
                @forelse($recentNotifications as $notif)
                    <div class="nb-card-sm bg-white dark:bg-coffee-800 p-3 space-y-1">
                        <div class="flex justify-between items-center">
                            <span class="nb-badge @if($notif->type === 'WhatsApp') bg-matcha text-espresso @else bg-blue-y2k text-espresso @endif text-[8px]">
                                {{ $notif->type }}
                            </span>
                            <span class="text-[9px] text-coffee-400 font-black">{{ $notif->created_at->diffForHumans() }}</span>
                        </div>
                        <p class="font-bold text-[10px] text-espresso dark:text-cream">
                            Untuk: <span class="text-berry font-black">{{ $notif->user->name ?? 'Member' }}</span>
                        </p>
                        <p class="text-coffee-600 dark:text-coffee-300 text-[10px] line-clamp-2">{{ $notif->message }}</p>
                    </div>
                @empty
                    <p class="col-span-2 text-center text-xs text-coffee-400 py-10 font-bold">Belum ada aktivitas push notifikasi.</p>
                @endforelse
            </div>
        </div>
    </div>

    <!-- TAB: Potensi Churn -->
    <div x-show="activeTab === 'churn'" class="nb-card bg-cream dark:bg-coffee-900 p-6 space-y-4">
        <div>
            <h2 class="text-xl font-black text-espresso dark:text-cream uppercase">🚨 Deteksi Pelanggan Churn</h2>
            <p class="text-xs text-coffee-600 dark:text-coffee-300 mt-1 font-semibold">Pelanggan tidak bertransaksi > 30 hari. Sistem akan kirim voucher MISSYOU20 otomatis.</p>
        </div>

        <div class="overflow-x-auto nb-table bg-white dark:bg-coffee-800">
            <table class="w-full text-left text-sm">
                <thead class="bg-berry text-white text-xs">
                    <tr>
                        <th class="px-4 py-3">Nama Member</th>
                        <th class="px-4 py-3">Kontak</th>
                        <th class="px-4 py-3">Kunjungan Terakhir</th>
                        <th class="px-4 py-3 text-center">Durasi Pasif</th>
                        <th class="px-4 py-3 text-right">Status</th>
                    </tr>
                </thead>
                <tbody class="text-xs text-coffee-700 dark:text-coffee-200">
                    @forelse($churnMembers as $member)
                        <tr class="hover:bg-berry/5">
                            <td class="px-4 py-3 font-black text-espresso dark:text-cream">{{ $member['name'] }}</td>
                            <td class="px-4 py-3 text-[11px]">{{ $member['email'] }}<br>{{ $member['no_hp'] }}</td>
                            <td class="px-4 py-3">{{ $member['last_visited'] }}</td>
                            <td class="px-4 py-3 text-center"><span class="nb-badge bg-berry text-white">{{ $member['days_inactive'] }} hari</span></td>
                            <td class="px-4 py-3 text-right">
                                @if($member['voucher_sent'])
                                    <span class="nb-badge bg-matcha text-espresso">📩 Voucher Terkirim</span>
                                @else
                                    <span class="nb-badge bg-zinc-300 text-coffee-600">⏳ Belum Dikirim</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center text-coffee-400 text-sm font-bold">✨ Semua member aktif! Tidak ada pelanggan churn.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- TAB: Transaksi Terbaru -->
    <div x-show="activeTab === 'transactions'" class="nb-card bg-cream dark:bg-coffee-900 p-6 space-y-4">
        <div>
            <h2 class="text-xl font-black text-espresso dark:text-cream uppercase">🧾 Riwayat Transaksi Terbaru</h2>
            <p class="text-xs text-coffee-600 dark:text-coffee-300 mt-1 font-semibold">Data operasional coffee shop yang masuk melalui kasir POS.</p>
        </div>

        <div class="overflow-x-auto nb-table bg-white dark:bg-coffee-800">
            <table class="w-full text-left text-sm">
                <thead class="bg-espresso text-cream text-xs">
                    <tr>
                        <th class="px-4 py-3">ID</th>
                        <th class="px-4 py-3">Member</th>
                        <th class="px-4 py-3">Waktu</th>
                        <th class="px-4 py-3 text-center">Item</th>
                        <th class="px-4 py-3 text-right">Total</th>
                    </tr>
                </thead>
                <tbody class="text-xs text-coffee-700 dark:text-coffee-200">
                    @forelse($recentTransactions as $tx)
                        <tr class="hover:bg-caramel/10">
                            <td class="px-4 py-3 font-mono font-black text-espresso dark:text-cream">#{{ str_pad($tx->id, 5, '0', STR_PAD_LEFT) }}</td>
                            <td class="px-4 py-3 font-bold">{{ $tx->user->name }}</td>
                            <td class="px-4 py-3 text-[11px]">{{ $tx->created_at->format('d M Y, H:i') }}</td>
                            <td class="px-4 py-3 text-center"><span class="nb-badge bg-blue-y2k text-espresso">{{ $tx->jumlah_item }} pcs</span></td>
                            <td class="px-4 py-3 text-right font-black text-espresso dark:text-cream">Rp {{ number_format($tx->total_bayar, 0, ',', '.') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center text-coffee-400 text-sm font-bold">Belum ada transaksi.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Chart.js Script -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('livewire:navigated', () => { initializePlot(); });
        document.addEventListener('livewire:update', () => { initializePlot(); });
        window.addEventListener('DOMContentLoaded', () => { initializePlot(); });

        function initializePlot() {
            const ctx = document.getElementById('clusterScatterPlot');
            if (!ctx || typeof Chart === 'undefined') return;
            const rawPoints = {!! $scatterPointsJson !!};
            const groups = { strong: [], manis: [], premium: [], unknown: [] };
            rawPoints.forEach(pt => {
                const mappedPt = { x: pt.x, y: pt.y, name: pt.name, spending: pt.spending };
                if (pt.label === 'Pecinta Kopi Strong & Hemat') groups.strong.push(mappedPt);
                else if (pt.label === 'Pecinta Minuman Manis/Kekinian') groups.manis.push(mappedPt);
                else if (pt.label === 'Pelanggan Premium (Suka Es Krim/Kue Mahal)') groups.premium.push(mappedPt);
                else groups.unknown.push(mappedPt);
            });
            if (window.scatterChartInstance) window.scatterChartInstance.destroy();
            window.scatterChartInstance = new Chart(ctx, {
                type: 'scatter',
                data: {
                    datasets: [
                        { label: '☕ Kopi Strong & Hemat', data: groups.strong, backgroundColor: '#FF8C42', borderColor: '#1a1a1a', borderWidth: 2, pointRadius: 8, pointHoverRadius: 11 },
                        { label: '🍵 Minuman Manis/Kekinian', data: groups.manis, backgroundColor: '#7DB954', borderColor: '#1a1a1a', borderWidth: 2, pointRadius: 8, pointHoverRadius: 11 },
                        { label: '💎 Pelanggan Premium', data: groups.premium, backgroundColor: '#C77DFF', borderColor: '#1a1a1a', borderWidth: 2, pointRadius: 8, pointHoverRadius: 11 },
                        { label: '❓ Belum Terklasifikasi', data: groups.unknown, backgroundColor: '#a3a3a3', borderColor: '#1a1a1a', borderWidth: 2, pointRadius: 6, pointHoverRadius: 9 }
                    ]
                },
                options: {
                    responsive: true, maintainAspectRatio: false,
                    scales: {
                        x: { title: { display: true, text: 'Preferensi Rasa Manis (1-5)', color: '#69432B', font: { weight: 'bold', size: 12 } }, grid: { color: 'rgba(60,21,24,0.08)' }, ticks: { color: '#69432B' }, min: 0.5, max: 5.5 },
                        y: { title: { display: true, text: 'Rasio Coffee (0-1)', color: '#69432B', font: { weight: 'bold', size: 12 } }, grid: { color: 'rgba(60,21,24,0.08)' }, ticks: { color: '#69432B' }, min: -0.05, max: 1.05 }
                    },
                    plugins: {
                        legend: { labels: { color: '#3C1518', font: { weight: 'bold', size: 11 }, usePointStyle: true, padding: 16 } },
                        tooltip: { callbacks: { label: ctx => `${ctx.raw.name} | Manis: ${ctx.raw.x} | Kopi: ${ctx.raw.y} | Belanja: Rp ${new Intl.NumberFormat('id-ID').format(ctx.raw.spending)}` } }
                    }
                }
            });
        }

        // Run immediately in case the document already finished loading
        // (e.g. when navigating here via Livewire SPA navigation).
        initializePlot();
    </script>
</div>
