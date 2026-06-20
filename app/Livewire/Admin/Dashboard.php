<?php

namespace App\Livewire\Admin;

use App\Models\CrmNotification;
use App\Models\Transaction;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class Dashboard extends Component
{
    public string $successMsg = '';

    public int $churnCount = 0;

    // Broadcast Campaign State
    public string $campaignText = '';

    public string $campaignSuccessMsg = '';

    public function mount(): void
    {
        // Ensure members with order history are classified so they appear in the cluster plot
        User::classifyUnlabeledMembers();
    }

    public function broadcastCampaign(): void
    {
        $this->validate([
            'campaignText' => 'required|string|min:5|max:500',
        ]);

        $members = User::where('role', 'member')->get();
        $sentCount = 0;

        foreach ($members as $member) {
            CrmNotification::create([
                'user_id' => $member->id,
                'type' => 'WhatsApp',
                'message' => '📢 PROMO CAFE: '.$this->campaignText,
            ]);

            CrmNotification::create([
                'user_id' => $member->id,
                'type' => 'Email',
                'message' => "Halo {$member->name},\n\n".$this->campaignText."\n\nKunjungi kami dan nikmati kopinya!",
            ]);

            $sentCount++;
        }

        $this->campaignText = '';
        $this->campaignSuccessMsg = "📢 Kampanye berhasil dibroadcast ke {$sentCount} member (WhatsApp & Email)!";
    }

    /**
     * Run Churn Prevention Scan directly in Livewire.
     */
    public function scanChurnPrevention(): void
    {
        $members = User::where('role', 'member')->get();
        $thirtyDaysAgo = Carbon::now()->subDays(30);
        $sentCount = 0;

        foreach ($members as $member) {
            $lastTx = $member->transactions()->latest()->first();

            // Member is inactive if their last transaction is older than 30 days
            // or if they registered more than 30 days ago and have no transactions at all.
            $isInactive = false;
            if ($lastTx) {
                if ($lastTx->created_at->lt($thirtyDaysAgo)) {
                    $isInactive = true;
                }
            } elseif ($member->created_at !== null && $member->created_at->lt($thirtyDaysAgo)) {
                $isInactive = true;
            }

            if ($isInactive) {
                $alreadySent = CrmNotification::where('user_id', $member->id)
                    ->where('message', 'like', '%kami rindu Anda%')
                    ->where('created_at', '>=', Carbon::now()->subDays(7))
                    ->exists();

                if (! $alreadySent) {
                    CrmNotification::create([
                        'user_id' => $member->id,
                        'type' => 'WhatsApp',
                        'message' => "Halo {$member->name}, kami rindu Anda! Ini kupon diskon 20% khusus untukmu [VOUCHER: MISSYOU20], yuk kunjungi coffee shop kami lagi!",
                    ]);

                    CrmNotification::create([
                        'user_id' => $member->id,
                        'type' => 'Email',
                        'message' => "Kami Rindu Anda, {$member->name}! Dapatkan diskon 20% untuk semua menu dengan voucher MISSYOU20.",
                    ]);

                    $sentCount++;
                }
            }
        }

        $this->successMsg = "Scan Churn Prevention selesai. Berhasil mengirimkan WhatsApp & Email penawaran khusus ke {$sentCount} pelanggan pasif.";
    }

    public function render(): View
    {
        // 1. Operational CRM Metrics
        $totalSales = (float) Transaction::sum('total_bayar');
        $totalMembers = User::where('role', 'member')->count();
        $avgSpending = (float) (Transaction::avg('total_bayar') ?: 0);
        $totalTransactions = Transaction::count();

        // 2. Tier status counts
        $goldCount = User::where('role', 'member')->where('tier_status', 'Gold')->count();
        $silverCount = User::where('role', 'member')->where('tier_status', 'Silver')->count();
        $bronzeCount = User::where('role', 'member')->where('tier_status', 'Bronze')->count();

        // 3. Find Churn Customers (no transaction in > 30 days)
        $members = User::where('role', 'member')->get();
        $thirtyDaysAgo = Carbon::now()->subDays(30);
        $sevenDaysAgo = Carbon::now()->subDays(7);
        $churnMembersList = [];

        foreach ($members as $member) {
            $lastTx = $member->transactions()->latest()->first();
            $isInactive = false;

            if ($lastTx) {
                if ($lastTx->created_at->lt($thirtyDaysAgo)) {
                    $isInactive = true;
                }
            } elseif ($member->created_at !== null && $member->created_at->lt($thirtyDaysAgo)) {
                $isInactive = true;
            }

            if ($isInactive) {
                $voucherSent = CrmNotification::where('user_id', $member->id)
                    ->where('message', 'like', '%kami rindu Anda%')
                    ->where('created_at', '>=', $sevenDaysAgo)
                    ->exists();

                $daysInactive = $lastTx
                    ? (int) $lastTx->created_at->diffInDays(Carbon::now())
                    : (int) ($member->created_at?->diffInDays(Carbon::now()) ?? 0);

                $churnMembersList[] = [
                    'id' => $member->id,
                    'name' => $member->name,
                    'email' => $member->email,
                    'no_hp' => $member->no_hp,
                    'last_visited' => $lastTx ? $lastTx->created_at->diffForHumans() : 'Belum pernah bertransaksi',
                    'days_inactive' => $daysInactive,
                    'voucher_sent' => $voucherSent,
                ];
            }
        }

        // Sort by longest inactive
        usort($churnMembersList, fn (array $a, array $b): int => $b['days_inactive'] <=> $a['days_inactive']);

        $this->churnCount = count($churnMembersList);

        // 4. Gather Scatter Plot Cluster data
        $scatterPoints = [];
        foreach ($members as $m) {
            $features = $m->getKnnFeatures();
            $scatterPoints[] = [
                'x' => (float) $features[0],
                'y' => (float) $features[1],
                'name' => $m->name,
                'label' => $m->behavior_label ?? 'Belum Terklasifikasi',
                'spending' => (float) $features[2],
            ];
        }

        // 5. Recent Transactions & Notifications
        $recentTransactions = Transaction::with('user')->latest()->limit(5)->get();
        $recentNotifications = CrmNotification::with('user')->latest()->limit(8)->get();

        return view('livewire.admin.dashboard', [
            'totalSales' => $totalSales,
            'totalMembers' => $totalMembers,
            'avgSpending' => $avgSpending,
            'totalTransactions' => $totalTransactions,
            'goldCount' => $goldCount,
            'silverCount' => $silverCount,
            'bronzeCount' => $bronzeCount,
            'churnMembers' => $churnMembersList,
            'scatterPointsJson' => json_encode($scatterPoints),
            'recentTransactions' => $recentTransactions,
            'recentNotifications' => $recentNotifications,
        ])->layout('layouts.app');
    }
}
