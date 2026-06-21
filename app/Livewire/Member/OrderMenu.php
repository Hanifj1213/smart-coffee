<?php

namespace App\Livewire\Member;

use App\Models\CrmNotification;
use App\Models\Menu;
use App\Models\RewardRedemption;
use App\Models\Transaction;
use App\Models\TransactionDetail;
use App\Models\User;
use App\Services\PromoService;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class OrderMenu extends Component
{
    /**
     * @var array<int, array{nama: string, harga: int, kategori: string, manis: int, qty: int}>
     */
    public array $cart = [];

    // Receipt state
    public bool $showReceiptModal = false;

    /** @var array<string, mixed> */
    public array $lastTxData = [];

    public string $successMessage = '';

    // Promo / coupon code (customer can enter codes from their dashboard here)
    public string $couponCode = '';

    public bool $couponApplied = false;

    public string $couponError = '';

    public int $couponDiscountPercent = 0;

    // Set when the applied coupon is a redeemed reward voucher (so it can be consumed)
    public ?int $appliedRedemptionId = null;

    /**
     * Menu Catalog (loaded from the database in mount()).
     *
     * @var list<array{nama: string, harga: int, kategori: string, manis: int, img: string}>
     */
    public array $menuCatalog = [];

    public function mount(): void
    {
        $this->menuCatalog = Menu::query()
            ->where('is_active', true)
            ->orderBy('kategori')
            ->orderBy('nama')
            ->get()
            ->map(fn (Menu $menu): array => [
                'nama' => $menu->nama,
                'harga' => $menu->harga,
                'kategori' => $menu->kategori,
                'manis' => $menu->rasa_manis,
                'img' => $menu->image,
                'icon' => $menu->icon,
            ])
            ->all();
    }

    public function applyCoupon(): void
    {
        $this->couponError = '';
        $this->couponApplied = false;
        $this->couponDiscountPercent = 0;
        $this->appliedRedemptionId = null;

        $code = PromoService::normalize($this->couponCode);
        if ($code === '') {
            return;
        }

        // 1. Fixed promotional codes (segment promos, welcome codes, etc.)
        if (PromoService::isValid($code)) {
            $this->couponDiscountPercent = PromoService::discountPercentFor($code);
            $this->couponApplied = true;

            return;
        }

        // 2. Discount vouchers the member redeemed from the reward catalog
        $user = Auth::user();
        if ($user instanceof User) {
            $voucher = RewardRedemption::findUsableVoucher($user->id, $code);
            if ($voucher !== null) {
                $this->couponDiscountPercent = $voucher->discount_percent;
                $this->couponApplied = true;
                $this->appliedRedemptionId = $voucher->id;

                return;
            }
        }

        $this->couponError = 'Kode promo tidak valid atau sudah terpakai!';
    }

    public function removeCoupon(): void
    {
        $this->couponCode = '';
        $this->couponApplied = false;
        $this->couponDiscountPercent = 0;
        $this->couponError = '';
        $this->appliedRedemptionId = null;
    }

    public function addToCart(int $menuIndex): void
    {
        if (! isset($this->menuCatalog[$menuIndex])) {
            return;
        }

        $menuItem = $this->menuCatalog[$menuIndex];

        foreach ($this->cart as $key => $cartItem) {
            if ($cartItem['nama'] === $menuItem['nama']) {
                $this->cart[$key]['qty']++;

                return;
            }
        }

        $this->cart[] = [
            'nama' => $menuItem['nama'],
            'harga' => $menuItem['harga'],
            'kategori' => $menuItem['kategori'],
            'manis' => $menuItem['manis'],
            'qty' => 1,
        ];

        $this->successMessage = '';
    }

    public function updateSweetness(int $cartIndex, int $val): void
    {
        if (isset($this->cart[$cartIndex])) {
            $this->cart[$cartIndex]['manis'] = $val;
        }
    }

    public function removeFromCart(int $cartIndex): void
    {
        unset($this->cart[$cartIndex]);
        $this->cart = array_values($this->cart);
    }

    public function incrementQty(int $cartIndex): void
    {
        if (isset($this->cart[$cartIndex])) {
            $this->cart[$cartIndex]['qty']++;
        }
    }

    public function decrementQty(int $cartIndex): void
    {
        if (isset($this->cart[$cartIndex])) {
            $this->cart[$cartIndex]['qty']--;
            if ($this->cart[$cartIndex]['qty'] <= 0) {
                $this->removeFromCart($cartIndex);
            }
        }
    }

    public function checkout(): void
    {
        if ($this->cart === []) {
            return;
        }

        $user = Auth::user();
        if (! $user instanceof User) {
            return;
        }

        $totalBayar = 0;
        $jumlahItem = 0;

        foreach ($this->cart as $item) {
            $totalBayar += $item['harga'] * $item['qty'];
            $jumlahItem += $item['qty'];
        }

        // Apply discount based on current Tier + applied promo code
        $tierDiscountPercent = 0;
        if ($user->tier_status === 'Silver') {
            $tierDiscountPercent = 5;
        } elseif ($user->tier_status === 'Gold') {
            $tierDiscountPercent = 10;
        }

        // Re-validate a redeemed reward voucher at checkout so it cannot be reused
        $redemptionToConsume = null;
        if ($this->couponApplied && $this->appliedRedemptionId !== null) {
            $redemptionToConsume = RewardRedemption::findUsableVoucher($user->id, $this->couponCode);
            if ($redemptionToConsume === null) {
                // Voucher no longer usable (already consumed elsewhere) — drop it
                $this->couponApplied = false;
                $this->couponDiscountPercent = 0;
                $this->appliedRedemptionId = null;
            } else {
                $this->couponDiscountPercent = $redemptionToConsume->discount_percent;
            }
        }

        $couponPercent = $this->couponApplied ? $this->couponDiscountPercent : 0;
        $totalDiscountPercent = $tierDiscountPercent + $couponPercent;

        $discountAmount = 0.0;
        $originalTotal = $totalBayar;
        if ($totalDiscountPercent > 0) {
            $discountAmount = ($originalTotal * $totalDiscountPercent) / 100;
            $totalBayar = $originalTotal - $discountAmount;
        }

        // Calculate points
        $basePoints = floor($totalBayar / 10000) * 100;
        $multiplier = 1.0;
        if ($user->tier_status === 'Gold') {
            $multiplier = 1.5;
        } elseif ($user->tier_status === 'Silver') {
            $multiplier = 1.2;
        }
        $pointsEarned = (int) ($basePoints * $multiplier);

        $transaction = Transaction::create([
            'user_id' => $user->id,
            'total_bayar' => $totalBayar,
            'jumlah_item' => $jumlahItem,
        ]);

        foreach ($this->cart as $item) {
            for ($k = 0; $k < $item['qty']; $k++) {
                TransactionDetail::create([
                    'transaction_id' => $transaction->id,
                    'nama_menu' => $item['nama'],
                    'kategori_menu' => $item['kategori'],
                    'rasa_manis' => $item['manis'],
                    'harga' => $item['harga'],
                ]);
            }
        }

        // Update customer points (total spending is recalculated below from transactions)
        $user->total_poin += $pointsEarned;
        $user->save();

        // Recalculate loyalty tier (also refreshes total_pengeluaran from transactions)
        $loyaltyResult = $user->recalculateLoyalty();

        // Consume the redeemed reward voucher so it can't be used again
        if ($redemptionToConsume !== null) {
            $redemptionToConsume->status = 'Used';
            $redemptionToConsume->save();
        }

        // Re-classify the member's behavior segment based on updated history
        $user->classifyBehavior();

        $waMessage = "Halo {$user->name}, pesanan Anda sebesar Rp ".number_format($totalBayar, 0, ',', '.')." berhasil diproses. Anda mendapatkan +{$pointsEarned} poin! Tier saat ini: {$user->tier_status}.";
        if ($loyaltyResult['tier_changed']) {
            $waMessage .= " Selamat! Status tier Anda naik dari {$loyaltyResult['old_tier']} ke {$loyaltyResult['new_tier']}!";
        }

        CrmNotification::create([
            'user_id' => $user->id,
            'type' => 'WhatsApp',
            'message' => $waMessage,
        ]);

        CrmNotification::create([
            'user_id' => $user->id,
            'type' => 'Email',
            'message' => "Detail Pesanan Smart Coffee CRM:\nTotal Bayar: Rp ".number_format($totalBayar, 0, ',', '.')."\nTambahan Poin: {$pointsEarned} poin\nStatus Tier: {$user->tier_status}\nPesanan sedang diproses!",
        ]);

        $this->lastTxData = [
            'id' => $transaction->id,
            'items' => $this->cart,
            'subtotal' => $originalTotal,
            'discount_percent' => $totalDiscountPercent,
            'discount_amount' => $discountAmount,
            'coupon_code' => $couponPercent > 0 ? PromoService::normalize($this->couponCode) : '',
            'coupon_percent' => $couponPercent,
            'final_total' => $totalBayar,
            'points_earned' => $pointsEarned,
            'total_points' => $user->total_poin,
            'date' => now()->format('d M Y H:i:s'),
            'tier_changed' => $loyaltyResult['tier_changed'],
            'new_tier' => $loyaltyResult['new_tier'],
        ];

        $this->showReceiptModal = true;
        $this->successMessage = 'Pesanan berhasil dibuat!';
        $this->cart = [];

        $this->couponCode = '';
        $this->couponApplied = false;
        $this->couponDiscountPercent = 0;
        $this->couponError = '';
        $this->appliedRedemptionId = null;
    }

    public function closeReceipt(): void
    {
        $this->showReceiptModal = false;
        $this->successMessage = '';
    }

    public function render(): View
    {
        $user = Auth::user();

        $cartTotal = 0;
        foreach ($this->cart as $item) {
            $cartTotal += $item['harga'] * $item['qty'];
        }

        $tierDiscountPercent = 0;
        if ($user instanceof User && $user->tier_status === 'Silver') {
            $tierDiscountPercent = 5;
        } elseif ($user instanceof User && $user->tier_status === 'Gold') {
            $tierDiscountPercent = 10;
        }

        $couponPercent = $this->couponApplied ? $this->couponDiscountPercent : 0;
        $totalDiscountPercent = $tierDiscountPercent + $couponPercent;

        $discountAmount = 0;
        $finalTotal = $cartTotal;
        if ($totalDiscountPercent > 0) {
            $discountAmount = ($cartTotal * $totalDiscountPercent) / 100;
            $finalTotal = $cartTotal - $discountAmount;
        }

        return view('livewire.member.order-menu', [
            'cartTotal' => $cartTotal,
            'discountPercent' => $totalDiscountPercent,
            'tierDiscountPercent' => $tierDiscountPercent,
            'couponPercent' => $couponPercent,
            'discountAmount' => $discountAmount,
            'finalTotal' => $finalTotal,
        ])->layout('layouts.app');
    }
}
