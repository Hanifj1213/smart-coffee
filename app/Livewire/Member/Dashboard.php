<?php

namespace App\Livewire\Member;

use App\Models\CrmNotification;
use App\Models\Transaction;
use App\Models\User;
use App\Services\KNearestNeighborsService;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Dashboard extends Component
{
    public string $userLabel = '';

    public string $recommendedMenu = '';

    public string $recommendedVoucher = '';

    public string $voucherCode = '';

    // KNN metadata for UI explanation
    public float $avgSweetness = 0;

    public float $coffeeRatio = 0;

    public float $avgSpending = 0;

    /** @var list<array<string, mixed>> */
    public array $nearestNeighbors = [];

    // Flavor profile properties
    public string $flavorProfile = '';

    public int $bitternessPercent = 50;

    public int $sweetnessPercent = 50;

    public function mount(): void
    {
        // Persist the member's behavior segment so they appear classified in the
        // admin cluster plot and KNN evaluation once they have order history.
        $user = Auth::user();
        if ($user instanceof User) {
            $user->classifyBehavior();
        }

        $this->getPersonalizedRecommendation();
    }

    /**
     * Run KNN to get personalized voucher and menu recommendations.
     */
    public function getPersonalizedRecommendation(): void
    {
        $user = Auth::user();
        if (! $user instanceof User) {
            return;
        }

        $features = $user->getKnnFeatures();
        $this->avgSweetness = $features[0];
        $this->coffeeRatio = $features[1];
        $this->avgSpending = $features[2];

        // Calculate Bitterness & Sweetness percentages for visualization
        $this->bitternessPercent = (int) round(($this->coffeeRatio) * 60 + (6 - $this->avgSweetness) * 8);
        $this->bitternessPercent = max(15, min(85, $this->bitternessPercent));
        $this->sweetnessPercent = 100 - $this->bitternessPercent;

        if ($this->coffeeRatio >= 0.55) {
            if ($this->avgSweetness <= 2.5) {
                $this->flavorProfile = 'Specialty Espresso & Kopi Strong Addict 🥃';
            } else {
                $this->flavorProfile = 'Creamy Latte & Cappuccino Lover 🥛';
            }
        } else {
            if ($this->avgSweetness >= 3.5) {
                $this->flavorProfile = 'Signature Sweet & Ice Blend Explorer 🍵';
            } else {
                $this->flavorProfile = 'Balanced Coffee & Pastry Gourmet 🍰';
            }
        }

        $knn = new KNearestNeighborsService(3);

        $trainingCustomers = User::where('role', 'member')
            ->where('id', '!=', $user->id)
            ->whereNotNull('behavior_label')
            ->get();

        foreach ($trainingCustomers as $customer) {
            $label = $customer->behavior_label;
            if ($label === null) {
                continue;
            }
            $knn->train($customer->getKnnFeatures(), $label, $customer->name);
        }

        if ($knn->getDatasetCount() > 0) {
            $result = $knn->classify($features);
            $this->userLabel = $result['label'];
            $this->nearestNeighbors = array_slice($result['neighbors'], 0, 3);
        } else {
            $this->userLabel = 'Pecinta Kopi Strong & Hemat';
        }

        // Determine recommendations based on prediction label
        if ($user->transactions()->count() === 0) {
            $this->userLabel = 'Pelanggan Baru';
            $this->recommendedMenu = 'Kopi Susu Aren / Caffe Latte';
            $this->recommendedVoucher = 'Diskon 10% Voucher Selamat Datang untuk Member Baru!';
            $this->voucherCode = 'COFFEEWELCOME';
        } else {
            switch ($this->userLabel) {
                case 'Pecinta Kopi Strong & Hemat':
                    $this->recommendedMenu = 'Americano Blend / Espresso Double Shot / Flat White';
                    $this->recommendedVoucher = 'Kupon Diskon 15% untuk Americano Blend / Espresso Blend!';
                    $this->voucherCode = 'STRONG15';
                    break;
                case 'Pecinta Minuman Manis/Kekinian':
                    $this->recommendedMenu = 'Matcha Latte Creamy / Caramel Macchiato / Red Velvet Latte';
                    $this->recommendedVoucher = 'Kupon Diskon 20% khusus Minuman Manis & Kekinian!';
                    $this->voucherCode = 'SWEET20';
                    break;
                case 'Pelanggan Premium (Suka Es Krim/Kue Mahal)':
                    $this->recommendedMenu = 'Cheesecake Premium + Butter Croissant + Caffe Latte Combo';
                    $this->recommendedVoucher = 'Diskon 15% Special Combo Cake Premium & Specialty Coffee!';
                    $this->voucherCode = 'PREMIUM15';
                    break;
                default:
                    $this->recommendedMenu = 'Kopi Susu Aren / Caffe Latte';
                    $this->recommendedVoucher = 'Kupon Diskon 10% General Promo!';
                    $this->voucherCode = 'COFFEE10';
                    break;
            }
        }
    }

    public function render(): View
    {
        $user = Auth::user();
        if (! $user instanceof User) {
            abort(403);
        }

        // Calculate progress to next tier
        $currentSpending = $user->total_pengeluaran;
        $progressPercent = 0;

        if ($user->tier_status === 'Gold') {
            $nextTier = 'Gold (Maksimum)';
            $targetSpending = 1500000;
            $progressPercent = 100;
        } elseif ($user->tier_status === 'Silver') {
            $nextTier = 'Gold';
            $targetSpending = 1500000;
            $progressPercent = (int) min(100, round(($currentSpending / $targetSpending) * 100));
        } else { // Bronze
            $nextTier = 'Silver';
            $targetSpending = 500000;
            $progressPercent = (int) min(100, round(($currentSpending / $targetSpending) * 100));
        }

        $notifications = CrmNotification::where('user_id', $user->id)
            ->latest()
            ->limit(10)
            ->get();

        $transactions = Transaction::where('user_id', $user->id)
            ->with('details')
            ->latest()
            ->get();

        return view('livewire.member.dashboard', [
            'user' => $user,
            'nextTier' => $nextTier,
            'targetSpending' => $targetSpending,
            'progressPercent' => $progressPercent,
            'notifications' => $notifications,
            'transactions' => $transactions,
        ])->layout('layouts.app');
    }
}
