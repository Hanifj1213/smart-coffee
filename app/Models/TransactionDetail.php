<?php

namespace App\Models;

use Database\Factories\TransactionDetailFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $transaction_id
 * @property string $nama_menu
 * @property string $kategori_menu
 * @property int $rasa_manis
 * @property float $harga
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property-read Transaction|null $transaction
 */
class TransactionDetail extends Model
{
    /** @use HasFactory<TransactionDetailFactory> */
    use HasFactory;

    /** @var list<string> */
    protected $fillable = [
        'transaction_id',
        'nama_menu',
        'kategori_menu',
        'rasa_manis',
        'harga',
    ];

    /**
     * Get the transaction this detail belongs to.
     *
     * @return BelongsTo<Transaction, $this>
     */
    public function transaction(): BelongsTo
    {
        return $this->belongsTo(Transaction::class);
    }
}
