<?php

namespace App\Models;

use Database\Factories\CrmNotificationFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $user_id
 * @property string $type
 * @property string $message
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property-read User|null $user
 */
class CrmNotification extends Model
{
    /** @use HasFactory<CrmNotificationFactory> */
    use HasFactory;

    /** @var string */
    protected $table = 'crm_notifications';

    /** @var list<string> */
    protected $fillable = [
        'user_id',
        'type',
        'message',
    ];

    /**
     * Get the user who received this notification.
     *
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
