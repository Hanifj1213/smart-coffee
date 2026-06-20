<div class="p-4 sm:p-6 space-y-6">
    <!-- TICKER -->
    <div class="ticker-bar bg-caramel text-espresso py-2">
        <div class="marquee inline-block text-xs font-extrabold tracking-widest uppercase">
            ☕ MANAJEMEN MENU — TAMBAH & KELOLA MENU KOPI, NON-KOPI & PASTRY — TAMPIL OTOMATIS DI KASIR & ORDER MEMBER — ☕ SMART COFFEE CRM —
        </div>
    </div>

    <!-- HEADER -->
    <div class="flex flex-col justify-between gap-4 md:flex-row md:items-center">
        <div>
            <h1 class="text-3xl font-black text-espresso dark:text-cream uppercase tracking-tight">☕ Manajemen Menu</h1>
            <p class="text-coffee-600 dark:text-coffee-300 font-semibold mt-1">Tambah, ubah, dan kelola daftar menu. Perubahan langsung tampil di Kasir POS & halaman Pesan Menu member.</p>
        </div>
        <button wire:click="openCreateModal" class="nb-btn bg-berry text-white flex items-center gap-2">
            <span class="text-lg">➕</span> Tambah Menu
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
        <input type="text" wire:model.live.debounce.300ms="search" placeholder="🔍 Cari menu..." class="nb-input w-full bg-white dark:bg-coffee-800 px-4 py-2.5 text-sm text-espresso dark:text-cream border-2 border-black">
    </div>

    <!-- TABLE -->
    <div class="nb-card bg-cream dark:bg-coffee-900 p-6 space-y-4">
        <div class="overflow-x-auto nb-table bg-white dark:bg-coffee-800">
            <table class="w-full text-left text-sm">
                <thead class="bg-espresso text-cream text-xs">
                    <tr>
                        <th class="px-4 py-3">Menu</th>
                        <th class="px-4 py-3">Kategori</th>
                        <th class="px-4 py-3 text-center">Harga</th>
                        <th class="px-4 py-3 text-center">Manis (1-5)</th>
                        <th class="px-4 py-3 text-center">Status</th>
                        <th class="px-4 py-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="text-xs text-coffee-700 dark:text-coffee-200">
                    @forelse($menus as $menu)
                        <tr class="hover:bg-caramel/10 border-b border-black/10">
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-3">
                                    @if($menu->image)
                                        <img src="{{ $menu->image }}" alt="{{ $menu->nama }}" class="w-10 h-10 rounded-lg object-cover border-2 border-black shadow-[2px_2px_0px_#1a1a1a] flex-shrink-0">
                                    @else
                                        <span class="text-2xl">{{ $menu->icon }}</span>
                                    @endif
                                    <p class="font-black text-espresso dark:text-cream">{{ $menu->nama }}</p>
                                </div>
                            </td>
                            <td class="px-4 py-3">
                                <span class="nb-badge @if($menu->kategori === 'Coffee') bg-caramel @elseif($menu->kategori === 'Non-Coffee') bg-matcha @else bg-pink-y2k @endif text-espresso">{{ $menu->kategori }}</span>
                            </td>
                            <td class="px-4 py-3 text-center font-black text-espresso dark:text-cream">Rp {{ number_format($menu->harga, 0, ',', '.') }}</td>
                            <td class="px-4 py-3 text-center">{{ $menu->rasa_manis }}/5</td>
                            <td class="px-4 py-3 text-center">
                                <button wire:click="toggleActive({{ $menu->id }})" class="nb-badge {{ $menu->is_active ? 'bg-matcha text-espresso' : 'bg-zinc-300 text-coffee-600' }}">
                                    {{ $menu->is_active ? '● Aktif' : '○ Nonaktif' }}
                                </button>
                            </td>
                            <td class="px-4 py-3 text-right space-x-1 whitespace-nowrap">
                                <button wire:click="openEditModal({{ $menu->id }})" class="nb-btn bg-caramel text-espresso text-[10px] px-2 py-1">✏️ Edit</button>
                                <button wire:click="delete({{ $menu->id }})" wire:confirm="Yakin ingin menghapus menu ini?" class="nb-btn bg-berry text-white text-[10px] px-2 py-1">🗑️</button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-coffee-400 text-sm font-bold">Belum ada menu. Klik "Tambah Menu" untuk membuat daftar menu.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div>{{ $menus->links() }}</div>
    </div>

    <!-- MODAL CREATE/EDIT -->
    @if($showModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4" wire:click.self="closeModal">
            <div class="nb-card bg-cream dark:bg-coffee-900 p-6 w-full max-w-lg space-y-4 max-h-[90vh] overflow-y-auto">
                <h2 class="text-xl font-black text-espresso dark:text-cream uppercase">{{ $editId ? '✏️ Edit Menu' : '➕ Tambah Menu' }}</h2>

                <form wire:submit.prevent="save" class="space-y-3">
                    <div>
                        <label class="block text-xs font-black text-coffee-600 uppercase mb-1">Nama Menu</label>
                        <input type="text" wire:model="nama" class="nb-input w-full bg-white dark:bg-coffee-800 px-3 py-2 text-sm text-espresso dark:text-cream border-2 border-black">
                        @error('nama') <p class="text-xs text-berry font-black mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-black text-coffee-600 uppercase mb-1">Kategori</label>
                            <select wire:model="kategori" class="nb-select w-full bg-white dark:bg-coffee-800 px-3 py-2 text-sm text-espresso dark:text-cream border-2 border-black">
                                <option value="Coffee">Coffee</option>
                                <option value="Non-Coffee">Non-Coffee</option>
                                <option value="Food">Food (Pastry & Cakes)</option>
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
                            <label class="block text-xs font-black text-coffee-600 uppercase mb-1">Harga (Rp)</label>
                            <input type="number" wire:model="harga" min="0" step="500" class="nb-input w-full bg-white dark:bg-coffee-800 px-3 py-2 text-sm text-espresso dark:text-cream border-2 border-black">
                            @error('harga') <p class="text-xs text-berry font-black mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-xs font-black text-coffee-600 uppercase mb-1">Tingkat Manis Default</label>
                            <select wire:model="rasa_manis" class="nb-select w-full bg-white dark:bg-coffee-800 px-3 py-2 text-sm text-espresso dark:text-cream border-2 border-black">
                                <option value="1">1 — Pahit</option>
                                <option value="2">2 — Sedikit</option>
                                <option value="3">3 — Normal</option>
                                <option value="4">4 — Manis</option>
                                <option value="5">5 — Sangat Manis</option>
                            </select>
                            @error('rasa_manis') <p class="text-xs text-berry font-black mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <label class="flex items-center gap-2 text-xs font-black text-espresso dark:text-cream">
                        <input type="checkbox" wire:model="is_active" class="w-4 h-4"> Aktif (tampil di Kasir & Order Member)
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
