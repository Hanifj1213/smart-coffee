<?php

namespace App\Livewire\Kasir;

use App\Models\User;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class MemberLookup extends Component
{
    public string $search = '';

    public ?int $selectedMemberId = null;

    public function selectMember(int $id): void
    {
        $this->selectedMemberId = $id;
    }

    public function clearSelection(): void
    {
        $this->selectedMemberId = null;
    }

    public function render(): View
    {
        $members = collect();
        if ($this->search !== '') {
            $members = User::where('role', 'member')
                ->where(function ($q) {
                    $q->where('name', 'like', '%'.$this->search.'%')
                        ->orWhere('email', 'like', '%'.$this->search.'%')
                        ->orWhere('no_hp', 'like', '%'.$this->search.'%');
                })
                ->orderBy('name')
                ->limit(10)
                ->get();
        }

        $selectedMember = null;
        $recentTransactions = collect();
        if ($this->selectedMemberId) {
            $selectedMember = User::find($this->selectedMemberId);
            if ($selectedMember) {
                $recentTransactions = $selectedMember->transactions()
                    ->with('details')
                    ->latest()
                    ->limit(5)
                    ->get();
            }
        }

        return view('livewire.kasir.member-lookup', [
            'members' => $members,
            'selectedMember' => $selectedMember,
            'recentTransactions' => $recentTransactions,
        ])->layout('layouts.app');
    }
}
