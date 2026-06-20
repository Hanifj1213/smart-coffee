<?php

namespace App\Livewire\Kasir;

use App\Models\CrmNotification;
use App\Models\Menu;
use App\Models\RewardRedemption;
use App\Models\Transaction;
use App\Models\TransactionDetail;
use App\Models\User;
use App\Services\PromoService;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Hash;
use Livewire\Component;

class Cashier extends Component
{
    // Search & Selection
    public string $search = '';

    public ?int $selectedUserId = null;

    // New User Form
    public string $newMemberName = '';

    public string $newMemberEmail = '';

    public string $newMemberPhone = '';

    public bool $showCreateModal = false;

    /**
     * Cart details.
     *
     * @var array<int, array{nama: string, harga: int, kategori: string, manis: int, qty: int}>
     */
    public array $cart = [];

    // Coupon Code Wallet
    public string $couponCode = '';

    public bool $couponApplied = false;

    public string $couponError = '';

    public int $couponDiscountPercent = 0;

    // Set when the applied coupon is a customer's redeemed reward voucher
    public ?int $appliedRedemptionId = null;

    // Simulated Receipt Modal State
    public bool $showReceiptModal = false;

    /** @var array<string, mixed> */
    public array $lastTxData = [];

    // Success State
    public string $successMessage = '';

    public int $pointsEarned = 0;

    public bool $tierChanged = false;

    public string $newTier = '';

    // Points Redemption at POS
    public bool $usePoints = false;

    public int $pointsRedeemed = 0;

    public float $pointsDiscountAmount = 0;

    /**
     * Menu Catalog (loaded from the database in mount()).
     *
     * @var list<array{nama: string, harga: int, kategori: string, manis: int, img: string}>
     */
    public array $menuCatalog = [];

    public function mount(): void
    {
        $this->loadMenuCatalog();
    }

    /**
     * Load active menu items from the database into the catalog.
     */
    public function loadMenuCatalog(): void
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

    public function selectUser(int $id): void
    {
        $this->selectedUserId = $id;
        $this->successMessage = '';
    }

    public function deselectUser(): void
    {
        $this->selectedUserId = null;
        $this->successMessage = '';
        $this->couponCode = '';
        $this->couponApplied = false;
        $this->couponDiscountPercent = 0;
        $this->couponError = '';
        $this->appliedRedemptionId = null;
        $this->usePoints = false;
        $this->pointsRedeemed = 0;
        $this->pointsDiscountAmount = 0;
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

        // 1. Fixed promotional codes
        if (PromoService::isValid($code)) {
            $this->couponDiscountPercent = PromoService::discountPercentFor($code);
            $this->couponApplied = true;

            return;
        }

        // 2. Discount voucher the selected member redeemed from the reward catalog
        if ($this->selectedUserId !== null) {
            $voucher = RewardRedemption::findUsableVoucher($this->selectedUserId, $code);
            if ($voucher !== null) {
                $this->couponDiscountPercent = $voucher->discount_percent;
                $this->couponApplied = true;
                $this->appliedRedemptionId = $voucher->id;

                return;
            }
        }

        $this->couponError = 'Kupon tidak valid atau sudah terpakai!';
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

    public function createMember(): void
    {
        $this->validate([
            'newMemberName' => 'required|string|max:255',
            'newMemberEmail' => 'required|email|unique:users,email',
            'newMemberPhone' => 'required|string',
        ]);

        $user = User::create([
            'name' => $this->newMemberName,
            'email' => $this->newMemberEmail,
            'no_hp' => $this->newMemberPhone,
            'password' => Hash::make('password'),
            'role' => 'member',
            'tier_status' => 'Bronze',
            'total_poin' => 0,
            'total_pengeluaran' => 0.00,
        ]);

        CrmNotification::create([
            'user_id' => $user->id,
            'type' => 'WhatsApp',
            'message' => "Halo {$user->name}, selamat! Anda terdaftar sebagai member di Smart Coffee CRM. Kumpulkan transaksi untuk naik ke Silver dan nikmati diskon 5%!",
        ]);

        CrmNotification::create([
            'user_id' => $user->id,
            'type' => 'Email',
            'message' => 'Selamat bergabung di Smart Coffee CRM! Dapatkan update promo personal terbaik Anda di dashboard.',
        ]);

        $this->selectedUserId = $user->id;
        $this->newMemberName = '';
        $this->newMemberEmail = '';
        $this->newMemberPhone = '';
        $this->showCreateModal = false;

        $this->successMessage = 'Member baru berhasil didaftarkan dan terpilih!';
    }

    public function submitTransaction(): void
    {
        if ($this->cart === []) {
            return;
        }

        if (! $this->selectedUserId) {
            $this->addError('transaction', 'Silakan pilih member terlebih dahulu.');

            return;
        }

        $user = User::find($this->selectedUserId);
        if (! $user) {
            return;
        }

        $totalBayar = 0;
        $jumlahItem = 0;

        foreach ($this->cart as $item) {
            $totalBayar += $item['harga'] * $item['qty'];
            $jumlahItem += $item['qty'];
        }

        // Apply discount based on current Tier + Coupon:
        // Silver: 5% discount, Gold: 10% discount, Bronze: 0%
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

        // Apply Points Discount (1.000 poin = Rp 10.000)
        $pointsRedeemed = 0;
        $pointsDiscountAmount = 0.0;
        if ($this->usePoints && $user->total_poin >= 1000) {
            $maxPointsRedeemableByPoin = floor($user->total_poin / 1000) * 1000;
            $maxPointsRedeemableByTotal = floor($totalBayar / 10000) * 1000;
            $pointsRedeemed = (int) min($maxPointsRedeemableByPoin, $maxPointsRedeemableByTotal);
            $pointsDiscountAmount = ($pointsRedeemed / 1000) * 10000;
            $totalBayar = $totalBayar - $pointsDiscountAmount;
        }

        // Calculate points based on the current tier:
        // Every 10,000 kelipatan gets 100 points
        // Gold: multiplier 1.5x, Silver: multiplier 1.2x, Bronze: multiplier 1.0x
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

        $user->total_poin -= $pointsRedeemed;
        $user->total_poin += $pointsEarned;
        $user->save();

        $loyaltyResult = $user->recalculateLoyalty();

        // Consume the redeemed reward voucher so it can't be used again
        if ($redemptionToConsume !== null) {
            $redemptionToConsume->status = 'Used';
            $redemptionToConsume->save();
        }

        // Re-classify the member's behavior segment based on updated history
        $user->classifyBehavior();

        $waMessage = "Halo {$user->name}, transaksi Rp ".number_format($totalBayar, 0, ',', '.')." berhasil dibayar. Anda mendapatkan +{$pointsEarned} poin! Tier saat ini: {$user->tier_status}.";
        if ($loyaltyResult['tier_changed']) {
            $waMessage .= " Selamat! Status tier Anda naik dari {$loyaltyResult['old_tier']} ke {$loyaltyResult['new_tier']}!";
            $this->tierChanged = true;
            $this->newTier = $loyaltyResult['new_tier'];
        } else {
            $this->tierChanged = false;
        }

        CrmNotification::create([
            'user_id' => $user->id,
            'type' => 'WhatsApp',
            'message' => $waMessage,
        ]);

        CrmNotification::create([
            'user_id' => $user->id,
            'type' => 'Email',
            'message' => "Detail Pembelian Smart Coffee CRM:\nTotal Bayar: Rp ".number_format($totalBayar, 0, ',', '.')."\nTambahan Poin: {$pointsEarned} poin\nStatus Tier: {$user->tier_status}\nTerima kasih atas kunjungan Anda!",
        ]);

        $this->lastTxData = [
            'id' => $transaction->id,
            'customer_name' => $user->name,
            'tier' => $user->tier_status,
            'items' => $this->cart,
            'subtotal' => $originalTotal,
            'discount_percent' => $totalDiscountPercent,
            'discount_amount' => $discountAmount,
            'points_redeemed' => $pointsRedeemed,
            'points_discount_amount' => $pointsDiscountAmount,
            'final_total' => $totalBayar,
            'points_earned' => $pointsEarned,
            'total_points' => $user->total_poin,
            'date' => now()->format('d M Y H:i:s'),
        ];
        $this->showReceiptModal = true;

        $this->pointsEarned = $pointsEarned;
        $this->successMessage = 'Transaksi berhasil dicatat!';
        $this->cart = [];

        $this->couponCode = '';
        $this->couponApplied = false;
        $this->couponDiscountPercent = 0;
        $this->appliedRedemptionId = null;
        $this->usePoints = false;
        $this->pointsRedeemed = 0;
        $this->pointsDiscountAmount = 0;
    }

    public function render(): View
    {
        $members = User::where('role', 'member')
            ->where(function ($query): void {
                $query->where('name', 'like', '%'.$this->search.'%')
                    ->orWhere('email', 'like', '%'.$this->search.'%')
                    ->orWhere('no_hp', 'like', '%'.$this->search.'%');
            })
            ->orderBy('name')
            ->limit(8)
            ->get();

        $selectedUser = $this->selectedUserId ? User::find($this->selectedUserId) : null;

        $cartTotal = 0;
        foreach ($this->cart as $item) {
            $cartTotal += $item['harga'] * $item['qty'];
        }

        $tierDiscountPercent = 0;
        if ($selectedUser) {
            if ($selectedUser->tier_status === 'Silver') {
                $tierDiscountPercent = 5;
            } elseif ($selectedUser->tier_status === 'Gold') {
                $tierDiscountPercent = 10;
            }
        }

        $couponPercent = $this->couponApplied ? $this->couponDiscountPercent : 0;
        $totalDiscountPercent = $tierDiscountPercent + $couponPercent;

        $discountAmount = 0;
        $finalTotal = $cartTotal;
        if ($totalDiscountPercent > 0) {
            $discountAmount = ($cartTotal * $totalDiscountPercent) / 100;
            $finalTotal = $cartTotal - $discountAmount;
        }

        // Calculate Points Discount for live preview
        $pointsRedeemed = 0;
        $pointsDiscountAmount = 0.0;
        if ($selectedUser && $this->usePoints && $selectedUser->total_poin >= 1000) {
            $maxPointsRedeemableByPoin = floor($selectedUser->total_poin / 1000) * 1000;
            $maxPointsRedeemableByTotal = floor($finalTotal / 10000) * 1000;
            $pointsRedeemed = (int) min($maxPointsRedeemableByPoin, $maxPointsRedeemableByTotal);
            $pointsDiscountAmount = ($pointsRedeemed / 1000) * 10000;
            $finalTotal = $finalTotal - $pointsDiscountAmount;
        }
        $this->pointsRedeemed = $pointsRedeemed;
        $this->pointsDiscountAmount = $pointsDiscountAmount;

        return view('livewire.kasir.cashier', [
            'members' => $members,
            'selectedUser' => $selectedUser,
            'cartTotal' => $cartTotal,
            'discountPercent' => $totalDiscountPercent,
            'discountAmount' => $discountAmount,
            'pointsRedeemed' => $pointsRedeemed,
            'pointsDiscountAmount' => $pointsDiscountAmount,
            'finalTotal' => $finalTotal,
        ])->layout('layouts.app');
    }
}
