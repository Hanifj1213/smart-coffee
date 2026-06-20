<div class="p-4 sm:p-6 space-y-6">
    <!-- TICKER -->
    <div class="ticker-bar bg-espresso text-cream py-2">
        <div class="marquee inline-block text-xs font-extrabold tracking-widest uppercase">
            ☕ MANAJEMEN AKUN — KELOLA DATA MEMBER CRM — EDIT TIER & POIN — SMART COFFEE CRM — ☕ MANAJEMEN AKUN —
        </div>
    </div>

    <!-- HEADER -->
    <div class="flex flex-col justify-between gap-4 md:flex-row md:items-center">
        <div>
            <h1 class="text-3xl font-black text-espresso dark:text-cream uppercase tracking-tight">👥 Manajemen Akun Member</h1>
            <p class="text-coffee-600 dark:text-coffee-300 font-semibold mt-1">Kelola seluruh data akun member CRM Smart Coffee.</p>
        </div>
        <button wire:click="openCreateModal" class="nb-btn bg-mint-y2k text-espresso flex items-center gap-2">
            <span class="text-lg">➕</span> Tambah Member Baru
        </button>
    </div>

    <!-- Alert Success -->
    @if($successMessage)
        <div class="nb-card bg-matcha/20 p-4 flex items-start gap-3">
            <span class="text-2xl star-pulse">✅</span>
            <div class="flex-1">
                <p class="font-extrabold text-sm text-espresso dark:text-cream">{{ $successMessage }}</p>
            </div>
            <button wire:click="$set('successMessage', '')" class="text-espresso hover:text-berry font-black text-xl">×</button>
        </div>
    @endif

    <!-- Alert Errors -->
    @if($errors->any())
        <div class="nb-card bg-berry/20 p-4 flex items-start gap-3">
            <span class="text-2xl">❌</span>
            <div class="flex-1">
                @foreach($errors->all() as $error)
                    <p class="font-extrabold text-sm text-berry">{{ $error }}</p>
                @endforeach
            </div>
        </div>
    @endif

    <!-- Search -->
    <div class="nb-card bg-cream dark:bg-coffee-900 p-6">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-lg font-black text-espresso dark:text-cream uppercase">📋 Daftar Member</h2>
            <span class="nb-badge bg-blue-y2k text-espresso">{{ $members->total() }} Member</span>
        </div>
        <div class="relative mb-4">
            <input type="text" wire:model.live.debounce.300ms="search" placeholder="🔍 Cari nama, email, atau no HP member..." class="nb-input w-full bg-white dark:bg-coffee-800 px-4 py-3 text-sm text-espresso dark:text-cream" />
        </div>

        <!-- Table -->
        <div class="overflow-x-auto nb-table bg-white dark:bg-coffee-800">
            <table class="w-full text-left text-sm">
                <thead class="bg-espresso text-cream text-xs">
                    <tr>
                        <th class="px-4 py-3">Nama</th>
                        <th class="px-4 py-3">Kontak</th>
                        <th class="px-4 py-3">Tier Status</th>
                        <th class="px-4 py-3 text-center">Poin</th>
                        <th class="px-4 py-3 text-right">Total Belanja</th>
                        <th class="px-4 py-3 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="text-xs text-coffee-700 dark:text-coffee-200">
                    @forelse($members as $member)
                        <tr class="hover:bg-caramel/10">
                            <td class="px-4 py-3 font-black text-espresso dark:text-cream">{{ $member->name }}</td>
                            <td class="px-4 py-3 text-[11px]">
                                {{ $member->email }}<br>
                                <span class="text-coffee-400">{{ $member->no_hp }}</span>
                            </td>
                            <td class="px-4 py-3">
                                <span class="nb-badge @if($member->tier_status === 'Gold') bg-caramel text-espresso @elseif($member->tier_status === 'Silver') bg-zinc-300 text-espresso @else bg-orange-y2k text-espresso @endif">
                                    @if($member->tier_status === 'Gold') 👑 @elseif($member->tier_status === 'Silver') 🥈 @else 🥉 @endif
                                    {{ $member->tier_status }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-center font-black text-espresso dark:text-cream">{{ number_format($member->total_poin, 0, ',', '.') }}</td>
                            <td class="px-4 py-3 text-right font-black text-espresso dark:text-cream">Rp {{ number_format($member->total_pengeluaran, 0, ',', '.') }}</td>
                            <td class="px-4 py-3 text-center">
                                <div class="flex gap-2 justify-center">
                                    <button wire:click="openEditModal({{ $member->id }})" class="nb-btn bg-blue-y2k text-espresso text-[10px] px-2 py-1">✏️ Edit</button>
                                    <button wire:click="confirmDelete({{ $member->id }}, '{{ addslashes($member->name) }}')" class="nb-btn bg-berry text-white text-[10px] px-2 py-1">🗑️ Hapus</button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-coffee-400 text-sm font-bold">☕ Tidak ada data member yang ditemukan.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $members->links() }}
        </div>
    </div>

    <!-- Modal Edit -->
    @if($showEditModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center nb-modal-overlay p-4">
            <div class="w-full max-w-md nb-modal bg-cream dark:bg-coffee-900 p-6">
                <div class="flex justify-between items-center mb-5">
                    <h3 class="text-xl font-black text-espresso dark:text-cream uppercase">✏️ Edit Member</h3>
                    <button wire:click="closeEditModal" class="nb-btn bg-berry text-white text-xs px-2 py-1">✕</button>
                </div>

                <form wire:submit.prevent="saveEdit" class="space-y-4">
                    <div>
                        <label class="block text-xs font-black text-coffee-600 uppercase mb-1.5 tracking-wider">Nama Lengkap</label>
                        <input type="text" wire:model="editName" class="nb-input w-full bg-white dark:bg-coffee-800 px-3 py-2 text-sm text-espresso dark:text-cream" />
                        @error('editName') <p class="text-xs text-berry font-black mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-black text-coffee-600 uppercase mb-1.5 tracking-wider">Email</label>
                        <input type="email" wire:model="editEmail" class="nb-input w-full bg-white dark:bg-coffee-800 px-3 py-2 text-sm text-espresso dark:text-cream" />
                        @error('editEmail') <p class="text-xs text-berry font-black mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-black text-coffee-600 uppercase mb-1.5 tracking-wider">No WhatsApp / HP</label>
                        <input type="text" wire:model="editPhone" class="nb-input w-full bg-white dark:bg-coffee-800 px-3 py-2 text-sm text-espresso dark:text-cream" />
                        @error('editPhone') <p class="text-xs text-berry font-black mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-black text-coffee-600 uppercase mb-1.5 tracking-wider">Tier Status</label>
                        <select wire:model="editTier" class="nb-input w-full bg-white dark:bg-coffee-800 px-3 py-2 text-sm text-espresso dark:text-cream">
                            <option value="Bronze">🥉 Bronze</option>
                            <option value="Silver">🥈 Silver</option>
                            <option value="Gold">👑 Gold</option>
                        </select>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-black text-coffee-600 uppercase mb-1.5 tracking-wider">Total Poin</label>
                            <input type="number" wire:model="editPoints" class="nb-input w-full bg-white dark:bg-coffee-800 px-3 py-2 text-sm text-espresso dark:text-cream" />
                            @error('editPoints') <p class="text-xs text-berry font-black mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-xs font-black text-coffee-600 uppercase mb-1.5 tracking-wider">Total Belanja (Rp)</label>
                            <input type="number" wire:model="editSpending" class="nb-input w-full bg-white dark:bg-coffee-800 px-3 py-2 text-sm text-espresso dark:text-cream" />
                            @error('editSpending') <p class="text-xs text-berry font-black mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div class="pt-4 flex gap-3">
                        <button type="button" wire:click="closeEditModal" class="nb-btn flex-1 bg-zinc-200 text-espresso">Batal</button>
                        <button type="submit" class="nb-btn flex-1 bg-blue-y2k text-espresso">💾 Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    <!-- Modal Create -->
    @if($showCreateModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center nb-modal-overlay p-4">
            <div class="w-full max-w-md nb-modal bg-cream dark:bg-coffee-900 p-6">
                <div class="flex justify-between items-center mb-5">
                    <h3 class="text-xl font-black text-espresso dark:text-cream uppercase">➕ Tambah Member</h3>
                    <button wire:click="closeCreateModal" class="nb-btn bg-berry text-white text-xs px-2 py-1">✕</button>
                </div>

                <form wire:submit.prevent="createMember" class="space-y-4">
                    <div>
                        <label class="block text-xs font-black text-coffee-600 uppercase mb-1.5 tracking-wider">Nama Lengkap</label>
                        <input type="text" wire:model="newName" placeholder="Contoh: Rian Hidayat" class="nb-input w-full bg-white dark:bg-coffee-800 px-3 py-2 text-sm text-espresso dark:text-cream" required />
                        @error('newName') <p class="text-xs text-berry font-black mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-black text-coffee-600 uppercase mb-1.5 tracking-wider">Email</label>
                        <input type="email" wire:model="newEmail" placeholder="rian@example.com" class="nb-input w-full bg-white dark:bg-coffee-800 px-3 py-2 text-sm text-espresso dark:text-cream" required />
                        @error('newEmail') <p class="text-xs text-berry font-black mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-black text-coffee-600 uppercase mb-1.5 tracking-wider">No WhatsApp / HP</label>
                        <input type="text" wire:model="newPhone" placeholder="081234567890" class="nb-input w-full bg-white dark:bg-coffee-800 px-3 py-2 text-sm text-espresso dark:text-cream" required />
                        @error('newPhone') <p class="text-xs text-berry font-black mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="pt-4 flex gap-3">
                        <button type="button" wire:click="closeCreateModal" class="nb-btn flex-1 bg-zinc-200 text-espresso">Batal</button>
                        <button type="submit" class="nb-btn flex-1 bg-mint-y2k text-espresso">Daftar ☕</button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    <!-- Modal Delete -->
    @if($showDeleteModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center nb-modal-overlay p-4">
            <div class="w-full max-w-sm nb-modal bg-cream dark:bg-coffee-900 p-6">
                <div class="flex items-center gap-3 mb-4">
                    <span class="text-3xl">⚠️</span>
                    <h3 class="text-xl font-black text-berry uppercase">Hapus Member?</h3>
                </div>
                
                <p class="text-sm text-coffee-600 dark:text-coffee-300 mb-6 font-semibold">
                    Apakah Anda yakin ingin menghapus member <strong class="text-espresso dark:text-cream">{{ $deleteUserName }}</strong>? Tindakan ini tidak dapat dibatalkan dan seluruh data transaksi member ini akan ikut terhapus.
                </p>

                <div class="flex gap-3">
                    <button wire:click="closeDeleteModal" class="nb-btn flex-1 bg-zinc-200 text-espresso">Batal</button>
                    <button wire:click="deleteMember" class="nb-btn flex-1 bg-berry text-white">🗑️ Ya, Hapus</button>
                </div>
            </div>
        </div>
    @endif
</div>
