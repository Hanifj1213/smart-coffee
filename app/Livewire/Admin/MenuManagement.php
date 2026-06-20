<?php

namespace App\Livewire\Admin;

use App\Models\Menu;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Str;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

class MenuManagement extends Component
{
    use WithFileUploads, WithPagination;

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

    public $photo = null; // file upload

    public ?string $existingImage = null; // existing image path for edit mode

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
            'icon' => 'nullable|string|max:10',
            'photo' => 'nullable|image|max:2048', // max 2MB
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
        $this->icon = $menu->icon ?? '☕';
        $this->photo = null;
        $this->existingImage = $menu->image;
        $this->is_active = $menu->is_active;
        $this->successMessage = '';
        $this->showModal = true;
    }

    public function closeModal(): void
    {
        $this->showModal = false;
        $this->photo = null;
        $this->existingImage = null;
    }

    public function removePhoto(): void
    {
        $this->photo = null;
    }

    public function save(): void
    {
        $validated = $this->validate();

        // Handle image upload
        $imagePath = $this->existingImage; // keep existing if editing
        if ($this->photo) {
            $slug = Str::slug($this->nama);
            $extension = $this->photo->getClientOriginalExtension() ?: 'png';
            $filename = $slug . '.' . $extension;
            $destination = public_path('images' . DIRECTORY_SEPARATOR . 'menu');

            // Ensure directory exists
            if (!is_dir($destination)) {
                mkdir($destination, 0755, true);
            }

            // Copy from Livewire temp to public folder
            $tempPath = $this->photo->getRealPath();
            $targetPath = $destination . DIRECTORY_SEPARATOR . $filename;
            copy($tempPath, $targetPath);

            $imagePath = '/images/menu/' . $filename;
        }

        $data = [
            'nama' => $validated['nama'],
            'harga' => $validated['harga'],
            'kategori' => $validated['kategori'],
            'rasa_manis' => $validated['rasa_manis'],
            'icon' => $validated['icon'] ?? '☕',
            'image' => $imagePath,
            'is_active' => $validated['is_active'],
        ];

        if ($this->editId) {
            $menu = Menu::findOrFail($this->editId);
            $menu->update($data);
            $this->successMessage = 'Menu berhasil diperbarui!';
        } else {
            Menu::create($data);
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
        $this->photo = null;
        $this->existingImage = null;
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
