<div class="p-4 sm:p-6 space-y-6">
    <!-- TICKER -->
    <div class="ticker-bar bg-matcha text-espresso py-2">
        <div class="marquee inline-block text-xs font-extrabold tracking-widest uppercase">
            🎁 KATALOG REWARD — KELOLA HADIAH & VOUCHER LOYALTY — TUKAR POIN PELANGGAN — 🎁 SMART COFFEE CRM —
        </div>
    </div>

    <!-- HEADER -->
    <div class="flex flex-col justify-between gap-4 md:flex-row md:items-center">
        <div>
            <h1 class="text-3xl font-black text-espresso dark:text-cream uppercase tracking-tight">🎁 Katalog Reward</h1>
            <p class="text-coffee-600 dark:text-coffee-300 font-semibold mt-1">Kelola hadiah & voucher yang dapat ditukar pelanggan dengan poin loyalty.</p>
        </div>
        <button wire:click="openCreateModal" class="nb-btn bg-berry text-white flex items-center gap-2">
            <span class="text-lg">➕</span> Tambah Reward
        </button>
    </div>

    @if($successMessage)
        <div class="nb-card bg-matcha/20 p-4 flex items-start gap-3">
            <span class="text-2xl star-pulse">✅</span>
            <p class="flex-1 font-extrabold text-sm text-espresso dark:text-cream">{{ $successMessage }}</p>
            <button wire:click="$set('successMessage', '')" class="text-espresso hover:text-berry font-black text-xl">×</button>
        </div>
    @endif

    <!-- SEARCH -->
    <div class="nb-card bg-cream dark:bg-coffee-900 p-4">
        <input type="text" wire:model.live.debounce.300ms="search" placeholder="🔍 Cari reward..." class="nb-input w-full bg-white dark:bg-coffee-800 px-4 py-2.5 text-sm text-espresso dark:text-cream border-2 border-black">
    </div>

    <!-- TABLE -->
    <div class="nb-card bg-cream dark:bg-coffee-900 p-6 space-y-4">
        <div class="overflow-x-auto nb-table bg-white dark:bg-coffee-800">
            <table class="w-full text-left text-sm">
                <thead class="bg-espresso text-cream text-xs">
                    <tr>
                        <th class="px-4 py-3">Reward</th>
                        <th class="px-4 py-3">Kategori</th>
                        <th class="px-4 py-3 text-center">Biaya Poin</th>
                        <th class="px-4 py-3 text-center">Stok</th>
                        <th class="px-4 py-3 text-center">Status</th>
                        <th class="px-4 py-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="text-xs text-coffee-700 dark:text-coffee-200">
                    @forelse($rewards as $reward)
                        <tr class="hover:bg-caramel/10 border-b border-black/10">
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-2">
                                    <span class="text-2xl">{{ $reward->icon }}</span>
                                    <div>
                                        <p class="font-black text-espresso dark:text-cream">
                                            {{ $reward->nama }}
                                            @if($reward->discount_percent > 0)
                                                <span class="nb-badge bg-purple-y2k text-espresso text-[9px] ml-1">🎟️ {{ $reward->discount_percent }}% Promo</span>
                                            @endif
                                        </p>
                                        <p class="text-[10px] text-coffee-400 line-clamp-1">{{ $reward->deskripsi }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-3"><span class="nb-badge bg-blue-y2k text-espresso">{{ $reward->kategori }}</span></td>
                            <td class="px-4 py-3 text-center font-black text-espresso dark:text-cream">{{ number_format($reward->poin_cost, 0, ',', '.') }} ⭐</td>
                            <td class="px-4 py-3 text-center">{{ $reward->stok === null ? '∞' : $reward->stok }}</td>
                            <td class="px-4 py-3 text-center">
                                <button wire:click="toggleActive({{ $reward->id }})" class="nb-badge {{ $reward->is_active ? 'bg-matcha text-espresso' : 'bg-zinc-300 text-coffee-600' }}">
                                    {{ $reward->is_active ? '● Aktif' : '○ Nonaktif' }}
                                </button>
                            </td>
                            <td class="px-4 py-3 text-right space-x-1 whitespace-nowrap">
                                <button wire:click="openEditModal({{ $reward->id }})" class="nb-btn bg-caramel text-espresso text-[10px] px-2 py-1">✏️ Edit</button>
                                <button wire:click="delete({{ $reward->id }})" wire:confirm="Yakin ingin menghapus reward ini?" class="nb-btn bg-berry text-white text-[10px] px-2 py-1">🗑️</button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-coffee-400 text-sm font-bold">Belum ada reward. Klik "Tambah Reward" untuk membuat katalog.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div>{{ $rewards->links() }}</div>
    </div>

    <!-- MODAL CREATE/EDIT -->
    @if($showModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4" wire:click.self="closeModal">
            <div class="nb-card bg-cream dark:bg-coffee-900 p-6 w-full max-w-lg space-y-4 max-h-[90vh] overflow-y-auto">
                <h2 class="text-xl font-black text-espresso dark:text-cream uppercase">{{ $editId ? '✏️ Edit Reward' : '➕ Tambah Reward' }}</h2>

                <form wire:submit.prevent="save" class="space-y-3">
                    <div>
                        <label class="block text-xs font-black text-coffee-600 uppercase mb-1">Nama Reward</label>
                        <input type="text" wire:model="nama" class="nb-input w-full bg-white dark:bg-coffee-800 px-3 py-2 text-sm text-espresso dark:text-cream border-2 border-black">
                        @error('nama') <p class="text-xs text-berry font-black mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-black text-coffee-600 uppercase mb-1">Deskripsi</label>
                        <textarea wire:model="deskripsi" rows="2" class="nb-input w-full bg-white dark:bg-coffee-800 px-3 py-2 text-sm text-espresso dark:text-cream border-2 border-black"></textarea>
                        @error('deskripsi') <p class="text-xs text-berry font-black mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-black text-coffee-600 uppercase mb-1">Kategori</label>
                            <select wire:model="kategori" class="nb-select w-full bg-white dark:bg-coffee-800 px-3 py-2 text-sm text-espresso dark:text-cream border-2 border-black">
                                <option value="Produk">Produk</option>
                                <option value="Voucher">Voucher</option>
                                <option value="Merchandise">Merchandise</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-black text-coffee-600 uppercase mb-1">Icon (Emoji)</label>
                            <input type="text" wire:model="icon" maxlength="10" class="nb-input w-full bg-white dark:bg-coffee-800 px-3 py-2 text-sm text-espresso dark:text-cream border-2 border-black">
                            @error('icon') <p class="text-xs text-berry font-black mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-black text-coffee-600 uppercase mb-1">Biaya Poin</label>
                            <input type="number" wire:model="poin_cost" min="1" class="nb-input w-full bg-white dark:bg-coffee-800 px-3 py-2 text-sm text-espresso dark:text-cream border-2 border-black">
                            @error('poin_cost') <p class="text-xs text-berry font-black mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-xs font-black text-coffee-600 uppercase mb-1">Stok (kosongkan = tak terbatas)</label>
                            <input type="number" wire:model="stok" min="0" class="nb-input w-full bg-white dark:bg-coffee-800 px-3 py-2 text-sm text-espresso dark:text-cream border-2 border-black">
                            @error('stok') <p class="text-xs text-berry font-black mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-black text-coffee-600 uppercase mb-1">Diskon Voucher (%)</label>
                        <input type="number" wire:model="discount_percent" min="0" max="100" class="nb-input w-full bg-white dark:bg-coffee-800 px-3 py-2 text-sm text-espresso dark:text-cream border-2 border-black">
                        <p class="text-[10px] text-coffee-400 font-bold mt-1">Isi &gt; 0 jika reward ini adalah voucher diskon. Pelanggan bisa memakai kode hasil tukar di kolom kode promo saat pesan menu. Biarkan 0 untuk reward produk/merchandise.</p>
                        @error('discount_percent') <p class="text-xs text-berry font-black mt-1">{{ $message }}</p> @enderror
                    </div>

                    <label class="flex items-center gap-2 text-xs font-black text-espresso dark:text-cream">
                        <input type="checkbox" wire:model="is_active" class="w-4 h-4"> Aktif (tampil di katalog member)
                    </label>

                    <div class="flex gap-2 pt-2">
                        <button type="submit" class="nb-btn bg-matcha text-espresso flex-1">💾 Simpan</button>
                        <button type="button" wire:click="closeModal" class="nb-btn bg-zinc-300 text-espresso">Batal</button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
