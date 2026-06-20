<?php

namespace App\Livewire\Admin;

use App\Models\Reward;
use Illuminate\Contracts\View\View;
use Livewire\Component;
use Livewire\WithPagination;

class RewardManagement extends Component
{
    use WithPagination;

    public string $search = '';

    // Modal state
    public bool $showModal = false;

    public ?int $editId = null;

    // Form fields
    public string $nama = '';

    public string $deskripsi = '';

    public string $kategori = 'Produk';

    public int $poin_cost = 1000;

    public int $discount_percent = 0;

    public ?int $stok = null;

    public string $icon = '🎁';

    public bool $is_active = true;

    public string $successMessage = '';

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    /**
     * @return array<string, mixed>
     */
    protected function rules(): array
    {
        return [
            'nama' => 'required|string|max:255',
            'deskripsi' => 'nullable|string|max:1000',
            'kategori' => 'required|in:Produk,Voucher,Merchandise',
            'poin_cost' => 'required|integer|min:1',
            'discount_percent' => 'required|integer|min:0|max:100',
            'stok' => 'nullable|integer|min:0',
            'icon' => 'required|string|max:10',
            'is_active' => 'boolean',
        ];
    }

    public function openCreateModal(): void
    {
        $this->resetForm();
        $this->editId = null;
        $this->successMessage = '';
        $this->showModal = true;
    }

    public function openEditModal(int $id): void
    {
        $reward = Reward::findOrFail($id);
        $this->editId = $reward->id;
        $this->nama = $reward->nama;
        $this->deskripsi = $reward->deskripsi ?? '';
        $this->kategori = $reward->kategori;
        $this->poin_cost = $reward->poin_cost;
        $this->discount_percent = $reward->discount_percent;
        $this->stok = $reward->stok;
        $this->icon = $reward->icon;
        $this->is_active = $reward->is_active;
        $this->successMessage = '';
        $this->showModal = true;
    }

    public function closeModal(): void
    {
        $this->showModal = false;
    }

    public function save(): void
    {
        $validated = $this->validate();

        if ($this->editId) {
            $reward = Reward::findOrFail($this->editId);
            $reward->update($validated);
            $this->successMessage = 'Reward berhasil diperbarui!';
        } else {
            Reward::create($validated);
            $this->successMessage = 'Reward baru berhasil ditambahkan ke katalog!';
        }

        $this->showModal = false;
        $this->resetForm();
    }

    public function toggleActive(int $id): void
    {
        $reward = Reward::findOrFail($id);
        $reward->is_active = ! $reward->is_active;
        $reward->save();
        $this->successMessage = 'Status reward "'.$reward->nama.'" diperbarui.';
    }

    public function delete(int $id): void
    {
        $reward = Reward::findOrFail($id);
        $reward->delete();
        $this->successMessage = 'Reward berhasil dihapus dari katalog.';
    }

    private function resetForm(): void
    {
        $this->nama = '';
        $this->deskripsi = '';
        $this->kategori = 'Produk';
        $this->poin_cost = 1000;
        $this->discount_percent = 0;
        $this->stok = null;
        $this->icon = '🎁';
        $this->is_active = true;
    }

    public function render(): View
    {
        $rewards = Reward::query()
            ->where('nama', 'like', '%'.$this->search.'%')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('livewire.admin.reward-management', [
            'rewards' => $rewards,
        ])->layout('layouts.app');
    }
}
