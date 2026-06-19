<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $user_id
 * @property int $reward_id
 * @property string $reward_nama
 * @property int $poin_spent
 * @property int $discount_percent
 * @property string $kode_voucher
 * @property string $status
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read User $user
 * @property-read Reward|null $reward
 */
class RewardRedemption extends Model
{
    /** @var list<string> */
    protected $fillable = [
        'user_id',
        'reward_id',
        'reward_nama',
        'poin_spent',
        'discount_percent',
        'kode_voucher',
        'status',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'poin_spent' => 'integer',
            'discount_percent' => 'integer',
        ];
    }

    /**
     * Find a redeemed discount voucher that the given user can still use as a
     * promo code (belongs to them, still "Completed", and carries a discount).
     */
    public static function findUsableVoucher(int $userId, string $code): ?self
    {
        $normalized = strtoupper(trim($code));

        return static::query()
            ->where('user_id', $userId)
            ->whereRaw('UPPER(kode_voucher) = ?', [$normalized])
            ->where('status', 'Completed')
            ->where('discount_percent', '>', 0)
            ->first();
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return BelongsTo<Reward, $this>
     */
    public function reward(): BelongsTo
    {
        return $this->belongsTo(Reward::class);
    }
}
