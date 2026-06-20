<?php

namespace App\Livewire\Admin;

use App\Models\Menu;
use Illuminate\Contracts\View\View;
use Livewire\Component;
use Livewire\WithPagination;

class MenuManagement extends Component
{
    use WithPagination;

    public string $search = '';

    // Modal state
    public bool $showModal = false;

    public ?int $editId = null;

    // Form fields
    public string $nama = '';

    public int $harga = 20000;

    public string $kategori = 'Coffee';

    public int $rasa_manis = 3;

    public string $icon = '☕';

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
            'harga' => 'required|integer|min:0',
            'kategori' => 'required|in:Coffee,Non-Coffee,Food',
            'rasa_manis' => 'required|integer|min:1|max:5',
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
        $menu = Menu::findOrFail($id);
        $this->editId = $menu->id;
        $this->nama = $menu->nama;
        $this->harga = $menu->harga;
        $this->kategori = $menu->kategori;
        $this->rasa_manis = $menu->rasa_manis;
        $this->icon = $menu->icon;
        $this->is_active = $menu->is_active;
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
            $menu = Menu::findOrFail($this->editId);
            $menu->update($validated);
            $this->successMessage = 'Menu berhasil diperbarui!';
        } else {
            Menu::create($validated);
            $this->successMessage = 'Menu baru berhasil ditambahkan!';
        }

        $this->showModal = false;
        $this->resetForm();
    }

    public function toggleActive(int $id): void
    {
        $menu = Menu::findOrFail($id);
        $menu->is_active = ! $menu->is_active;
        $menu->save();
        $this->successMessage = 'Status menu "'.$menu->nama.'" diperbarui.';
    }

    public function delete(int $id): void
    {
        $menu = Menu::findOrFail($id);
        $menu->delete();
        $this->successMessage = 'Menu berhasil dihapus.';
    }

    private function resetForm(): void
    {
        $this->nama = '';
        $this->harga = 20000;
        $this->kategori = 'Coffee';
        $this->rasa_manis = 3;
        $this->icon = '☕';
        $this->is_active = true;
    }

    public function render(): View
    {
        $menus = Menu::query()
            ->where('nama', 'like', '%'.$this->search.'%')
            ->orderBy('kategori')
            ->orderBy('nama')
            ->paginate(12);

        return view('livewire.admin.menu-management', [
            'menus' => $menus,
        ])->layout('layouts.app');
    }
}
