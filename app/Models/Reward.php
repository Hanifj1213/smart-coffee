<?php

namespace App\Models;

use Database\Factories\RewardFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property string $nama
 * @property string|null $deskripsi
 * @property string $kategori
 * @property int $poin_cost
 * @property int $discount_percent
 * @property int|null $stok
 * @property string $icon
 * @property bool $is_active
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
class Reward extends Model
{
    /** @use HasFactory<RewardFactory> */
    use HasFactory;

    /** @var list<string> */
    protected $fillable = [
        'nama',
        'deskripsi',
        'kategori',
        'poin_cost',
        'discount_percent',
        'stok',
        'icon',
        'is_active',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'poin_cost' => 'integer',
            'discount_percent' => 'integer',
            'stok' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    /**
     * Whether this reward can currently be redeemed (active and in stock).
     */
    public function isAvailable(): bool
    {
        if (! $this->is_active) {
            return false;
        }

        return $this->stok === null || $this->stok > 0;
    }

    /**
     * @return HasMany<RewardRedemption, $this>
     */
    public function redemptions(): HasMany
    {
        return $this->hasMany(RewardRedemption::class);
    }
}
