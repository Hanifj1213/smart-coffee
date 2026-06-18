<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Concerns\HasTeams;
use App\Services\KNearestNeighborsService;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use Laravel\Fortify\Contracts\PasskeyUser;
use Laravel\Fortify\PasskeyAuthenticatable;
use Laravel\Fortify\TwoFactorAuthenticatable;

/**
 * @property int $id
 * @property string $name
 * @property string $email
 * @property Carbon|null $email_verified_at
 * @property string $password
 * @property string|null $two_factor_secret
 * @property string|null $two_factor_recovery_codes
 * @property Carbon|null $two_factor_confirmed_at
 * @property string|null $remember_token
 * @property int|null $current_team_id
 * @property string|null $no_hp
 * @property int $total_poin
 * @property float $total_pengeluaran
 * @property string $tier_status
 * @property string $role
 * @property string|null $behavior_label
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Team|null $currentTeam
 * @property-read Collection<int, Team> $ownedTeams
 * @property-read Collection<int, Membership> $teamMemberships
 * @property-read Collection<int, Team> $teams
 * @property-read Collection<int, Transaction> $transactions
 * @property-read Collection<int, CrmNotification> $crmNotifications
 * @property-read Collection<int, RewardRedemption> $rewardRedemptions
 */
#[Fillable(['name', 'email', 'password', 'current_team_id', 'no_hp', 'total_poin', 'total_pengeluaran', 'tier_status', 'role', 'behavior_label'])]
#[Hidden(['password', 'two_factor_secret', 'two_factor_recovery_codes', 'remember_token'])]
class User extends Authenticatable implements PasskeyUser
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, HasTeams, Notifiable, PasskeyAuthenticatable, TwoFactorAuthenticatable;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'two_factor_confirmed_at' => 'datetime',
        ];
    }

    /**
     * Get the user's initials
     */
    public function initials(): string
    {
        return Str::of($this->name)
            ->explode(' ')
            ->take(2)
            ->map(fn ($word) => Str::substr($word, 0, 1))
            ->implode('');
    }

    /**
     * @return HasMany<Transaction, $this>
     */
    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }

    /**
     * @return HasMany<CrmNotification, $this>
     */
    public function crmNotifications(): HasMany
    {
        return $this->hasMany(CrmNotification::class);
    }

    /**
     * @return HasMany<RewardRedemption, $this>
     */
    public function rewardRedemptions(): HasMany
    {
        return $this->hasMany(RewardRedemption::class);
    }

    /**
     * Get KNN feature array: [avg_sweetness, coffee_ratio, avg_spending]
     *
     * @return array{0: float, 1: float, 2: float}
     */
    public function getKnnFeatures(): array
    {
        // Load details relation to avoid N+1 issues
        $transactions = $this->transactions()->with('details')->get();
        if ($transactions->isEmpty()) {
            return [3.0, 0.5, 0.0];
        }

        $totalSweetness = 0;
        $sweetnessCount = 0;
        $coffeeCount = 0;
        $totalItems = 0;
        $totalSpending = 0;

        foreach ($transactions as $tx) {
            $totalSpending += $tx->total_bayar;
            foreach ($tx->details as $detail) {
                if ($detail->kategori_menu === 'Coffee') {
                    $coffeeCount++;
                }
                $totalSweetness += $detail->rasa_manis;
                $sweetnessCount++;
                $totalItems++;
            }
        }

        $avgSweetness = $sweetnessCount > 0 ? ($totalSweetness / $sweetnessCount) : 3.0;
        $coffeeRatio = $totalItems > 0 ? ($coffeeCount / $totalItems) : 0.5;
        $avgSpending = $transactions->count() > 0 ? ($totalSpending / $transactions->count()) : 0.0;

        return [
            (float) round($avgSweetness, 2),
            (float) round($coffeeRatio, 2),
            (float) round($avgSpending, 2),
        ];
    }

    /**
     * Auto-classify this member into a behavior segment using KNN against the
     * labeled training members. Persists the predicted label to behavior_label
     * so the customer shows up in the cluster plot and KNN evaluation instead of
     * staying "Belum Terklasifikasi".
     *
     * Only members with at least one transaction are classified, because the
     * KNN features are meaningless without any order history.
     */
    public function classifyBehavior(bool $save = true): ?string
    {
        if ($this->role !== 'member') {
            return $this->behavior_label;
        }

        if ($this->transactions()->count() === 0) {
            return $this->behavior_label;
        }

        $knn = new KNearestNeighborsService(3);

        $trainingUsers = static::query()
            ->where('role', 'member')
            ->where('id', '!=', $this->id)
            ->whereNotNull('behavior_label')
            ->get();

        foreach ($trainingUsers as $trainer) {
            $label = $trainer->behavior_label;
            if ($label === null) {
                continue;
            }
            $knn->train($trainer->getKnnFeatures(), $label, $trainer->name);
        }

        if ($knn->getDatasetCount() === 0) {
            return $this->behavior_label;
        }

        $result = $knn->classify($this->getKnnFeatures());
        $predicted = $result['label'];

        if ($predicted === '' || $predicted === 'Unknown') {
            return $this->behavior_label;
        }

        $this->behavior_label = $predicted;
        if ($save) {
            $this->save();
        }

        return $predicted;
    }

    /**
     * Classify every member that has transactions but no behavior_label yet.
     * Used to backfill existing customers (e.g. members created before
     * auto-classification existed) so the cluster plot is complete.
     *
     * @return int Number of members newly classified.
     */
    public static function classifyUnlabeledMembers(): int
    {
        $members = static::query()
            ->where('role', 'member')
            ->whereNull('behavior_label')
            ->get();

        $count = 0;
        foreach ($members as $member) {
            $before = $member->behavior_label;
            $member->classifyBehavior(true);
            if ($member->behavior_label !== null && $member->behavior_label !== $before) {
                $count++;
            }
        }

        return $count;
    }

    /**
     * Recalculate loyalty status (points, spending, and tier)
     *
     * @return array{old_tier: string, new_tier: string, tier_changed: bool}
     */
    public function recalculateLoyalty(): array
    {
        $transactions = $this->transactions()->get();

        $totalSpending = $transactions->sum('total_bayar');
        $this->total_pengeluaran = $totalSpending;

        // Determine tier
        $oldTier = $this->tier_status;
        if ($totalSpending > 1500000) {
            $this->tier_status = 'Gold';
        } elseif ($totalSpending >= 500000) {
            $this->tier_status = 'Silver';
        } else {
            $this->tier_status = 'Bronze';
        }

        $this->save();

        return [
            'old_tier' => $oldTier,
            'new_tier' => $this->tier_status,
            'tier_changed' => $oldTier !== $this->tier_status,
        ];
    }
}
