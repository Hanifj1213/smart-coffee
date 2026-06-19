<?php

namespace App\Models;

use Database\Factories\TransactionFactory;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $user_id
 * @property float $total_bayar
 * @property int $jumlah_item
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property-read User|null $user
 * @property-read Collection<int, TransactionDetail> $details
 */
class Transaction extends Model
{
    /** @use HasFactory<TransactionFactory> */
    use HasFactory;

    /** @var list<string> */
    protected $fillable = [
        'user_id',
        'total_bayar',
        'jumlah_item',
    ];

    /**
     * Get the user who made the transaction.
     *
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the details of the transaction.
     *
     * @return HasMany<TransactionDetail, $this>
     */
    public function details(): HasMany
    {
        return $this->hasMany(TransactionDetail::class);
    }
}
