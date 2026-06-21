<?php

namespace App\Livewire\Member;

use App\Models\CrmNotification;
use App\Models\Reward;
use App\Models\RewardRedemption;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Livewire\Component;

class RewardCatalog extends Component
{
    public string $successMessage = '';

    public string $errorMessage = '';

    public string $lastVoucherCode = '';

    /**
     * Redeem a reward, deducting the member's points atomically.
     */
    public function redeem(int $rewardId): void
    {
        $this->successMessage = '';
        $this->errorMessage = '';
        $this->lastVoucherCode = '';

        $authId = Auth::id();
        if ($authId === null) {
            return;
        }

        try {
            $result = DB::transaction(function () use ($rewardId, $authId): array {
                /** @var User $user */
                $user = User::query()->lockForUpdate()->findOrFail($authId);
                /** @var Reward $reward */
                $reward = Reward::query()->lockForUpdate()->findOrFail($rewardId);

                if (! $reward->isAvailable()) {
                    throw new \RuntimeException('Reward ini sedang tidak tersedia atau stok habis.');
                }

                if ($user->total_poin < $reward->poin_cost) {
                    throw new \RuntimeException('Poin Anda tidak mencukupi untuk menukar reward ini.');
                }

                $user->total_poin -= $reward->poin_cost;
                $user->save();

                if ($reward->stok !== null) {
                    $reward->decrement('stok');
                }

                $code = 'RWD-'.strtoupper(Str::random(8));

                $redemption = RewardRedemption::create([
                    'user_id' => $user->id,
                    'reward_id' => $reward->id,
                    'reward_nama' => $reward->nama,
                    'poin_spent' => $reward->poin_cost,
                    'discount_percent' => $reward->discount_percent,
                    'kode_voucher' => $code,
                    'status' => 'Completed',
                ]);

                CrmNotification::create([
                    'user_id' => $user->id,
                    'type' => 'WhatsApp',
                    'message' => "Halo {$user->name}, penukaran reward \"{$reward->nama}\" berhasil! Kode voucher Anda: {$code}. Sisa poin: ".number_format($user->total_poin, 0, ',', '.').'.',
                ]);

                CrmNotification::create([
                    'user_id' => $user->id,
                    'type' => 'Email',
                    'message' => "Penukaran Reward Smart Coffee CRM\nReward: {$reward->nama}\nPoin digunakan: {$reward->poin_cost}\nKode Voucher: {$code}\nTunjukkan kode ini ke kasir untuk klaim reward Anda.",
                ]);

                return [
                    'nama' => $reward->nama,
                    'code' => $redemption->kode_voucher,
                    'discount_percent' => $reward->discount_percent,
                ];
            });

            // Refresh the authenticated user instance so the displayed point
            // balance reflects the deduction immediately (the redemption updated
            // a separately-queried row inside the DB transaction).
            $authUser = Auth::user();
            if ($authUser instanceof User) {
                $authUser->refresh();
            }

            $this->lastVoucherCode = $result['code'];
            $this->successMessage = 'Berhasil menukar "'.$result['nama'].'"! Kode voucher: '.$result['code'];
            if (($result['discount_percent'] ?? 0) > 0) {
                $this->successMessage .= ' (Diskon '.$result['discount_percent'].'% — pakai kode ini di kolom Kode Promo saat Pesan Menu).';
            }
        } catch (\RuntimeException $e) {
            $this->errorMessage = $e->getMessage();
        }
    }

    public function render(): View
    {
        /** @var User $user */
        $user = Auth::user();

        $rewards = Reward::query()
            ->where('is_active', true)
            ->orderBy('poin_cost')
            ->get();

        $myRedemptions = RewardRedemption::query()
            ->where('user_id', $user->id)
            ->latest()
            ->limit(10)
            ->get();

        return view('livewire.member.reward-catalog', [
            'user' => $user,
            'rewards' => $rewards,
            'myRedemptions' => $myRedemptions,
        ])->layout('layouts.app');
    }
}
