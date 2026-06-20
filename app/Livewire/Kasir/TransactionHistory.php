<?php

namespace App\Livewire\Kasir;

use App\Models\Transaction;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Livewire\Component;
use Livewire\WithPagination;

class TransactionHistory extends Component
{
    use WithPagination;

    public string $search = '';

    public string $dateFilter = '';

    // Receipt detail modal
    public bool $showDetailModal = false;

    /** @var array<string, mixed> */
    public array $detailData = [];

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingDateFilter(): void
    {
        $this->resetPage();
    }

    /**
     * Show transaction detail in a receipt-style modal.
     */
    public function showDetail(int $transactionId): void
    {
        $tx = Transaction::with(['user', 'details'])->find($transactionId);
        if (! $tx) {
            return;
        }

        $items = [];
        foreach ($tx->details as $detail) {
            $items[] = [
                'nama' => $detail->nama_menu,
                'kategori' => $detail->kategori_menu,
                'manis' => $detail->rasa_manis,
                'harga' => $detail->harga,
            ];
        }

        $this->detailData = [
            'id' => $tx->id,
            'customer_name' => $tx->user?->name ?? 'Unknown',
            'tier' => $tx->user?->tier_status ?? '-',
            'items' => $items,
            'total_bayar' => $tx->total_bayar,
            'jumlah_item' => $tx->jumlah_item,
            'date' => $tx->created_at->format('d M Y H:i:s'),
        ];

        $this->showDetailModal = true;
    }

    public function closeDetail(): void
    {
        $this->showDetailModal = false;
        $this->detailData = [];
    }

    public function render(): View
    {
        $query = Transaction::with('user')
            ->latest();

        if ($this->search !== '') {
            $query->whereHas('user', function ($q) {
                $q->where('name', 'like', '%'.$this->search.'%')
                    ->orWhere('email', 'like', '%'.$this->search.'%');
            });
        }

        if ($this->dateFilter !== '') {
            $query->whereDate('created_at', $this->dateFilter);
        }

        $transactions = $query->paginate(15);

        // Today summary
        $todayTotal = Transaction::whereDate('created_at', today())->sum('total_bayar');
        $todayCount = Transaction::whereDate('created_at', today())->count();

        return view('livewire.kasir.transaction-history', [
            'transactions' => $transactions,
            'todayTotal' => $todayTotal,
            'todayCount' => $todayCount,
        ])->layout('layouts.app');
    }
}
