<?php

namespace App\Http\Controllers;

use App\Models\CrmNotification;
use App\Models\Transaction;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\StreamedResponse;

class CrmAnalyticController extends Controller
{
    /**
     * Get JSON data for Chart.js scatter plot.
     */
    public function getClusterData(): JsonResponse
    {
        $members = User::where('role', 'member')->get();

        $data = $members->map(function (User $member): array {
            $features = $member->getKnnFeatures();

            return [
                'id' => $member->id,
                'name' => $member->name,
                'avg_sweetness' => $features[0],
                'coffee_ratio' => $features[1],
                'avg_spending' => $features[2],
                'label' => $member->behavior_label ?? 'Belum Terklasifikasi',
                'tier' => $member->tier_status,
            ];
        });

        return response()->json($data);
    }

    /**
     * Run Churn Prevention Scan.
     *
     * Detects members who haven't made a transaction in over 30 days
     * and generates simulated notification logs.
     */
    public function runChurnPrevention(): JsonResponse
    {
        $members = User::where('role', 'member')->get();
        $thirtyDaysAgo = Carbon::now()->subDays(30);
        $sentCount = 0;

        foreach ($members as $member) {
            $lastTx = $member->transactions()->latest()->first();

            if ($lastTx && $lastTx->created_at->lt($thirtyDaysAgo)) {
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

        return response()->json([
            'success' => true,
            'message' => "Scan selesai. Berhasil mendeteksi pelanggan tidak aktif dan mengirimkan {$sentCount} set notifikasi WhatsApp & Email.",
            'sent_count' => $sentCount,
        ]);
    }

    /**
     * Export member data as a CSV file.
     */
    public function exportMembers(): StreamedResponse
    {
        $filename = 'laporan-member-'.now()->format('Ymd-His').'.csv';

        $headers = [
            'No', 'Nama', 'Email', 'No. WhatsApp', 'Tier', 'Total Poin', 'Total Pengeluaran', 'Segmen KNN', 'Terdaftar',
        ];

        $members = User::where('role', 'member')->orderBy('name')->get();

        return response()->streamDownload(function () use ($headers, $members): void {
            $handle = fopen('php://output', 'w');
            if ($handle === false) {
                return;
            }
            // UTF-8 BOM for Excel compatibility
            fwrite($handle, "\xEF\xBB\xBF");
            fputcsv($handle, $headers);

            foreach ($members as $index => $member) {
                fputcsv($handle, [
                    $index + 1,
                    $member->name,
                    $member->email,
                    $member->no_hp ?? '-',
                    $member->tier_status,
                    $member->total_poin,
                    $member->total_pengeluaran,
                    $member->behavior_label ?? 'Belum Terklasifikasi',
                    optional($member->created_at)->format('Y-m-d H:i'),
                ]);
            }

            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    /**
     * Export transactions as an Excel-compatible (.xls) spreadsheet.
     */
    public function exportTransactions(): StreamedResponse
    {
        $filename = 'laporan-transaksi-'.now()->format('Ymd-His').'.xls';

        $transactions = Transaction::with('user')->latest()->get();

        return response()->streamDownload(function () use ($transactions): void {
            echo "<table border='1'>";
            echo '<tr>'
                .'<th>ID</th><th>Member</th><th>Email</th><th>Jumlah Item</th><th>Total Bayar</th><th>Tanggal</th>'
                .'</tr>';

            foreach ($transactions as $tx) {
                echo '<tr>'
                    .'<td>'.$tx->id.'</td>'
                    .'<td>'.e($tx->user->name ?? '-').'</td>'
                    .'<td>'.e($tx->user->email ?? '-').'</td>'
                    .'<td>'.$tx->jumlah_item.'</td>'
                    .'<td>'.$tx->total_bayar.'</td>'
                    .'<td>'.optional($tx->created_at)->format('Y-m-d H:i').'</td>'
                    .'</tr>';
            }

            echo '</table>';
        }, $filename, [
            'Content-Type' => 'application/vnd.ms-excel; charset=UTF-8',
        ]);
    }

    /**
     * Render a print-optimized sales report (Save as PDF from the browser).
     */
    public function exportSalesPdf(): View
    {
        $totalSales = (float) Transaction::sum('total_bayar');
        $totalTransactions = Transaction::count();
        $totalMembers = User::where('role', 'member')->count();
        $avgSpending = (float) (Transaction::avg('total_bayar') ?: 0);

        $tierCounts = [
            'Gold' => User::where('role', 'member')->where('tier_status', 'Gold')->count(),
            'Silver' => User::where('role', 'member')->where('tier_status', 'Silver')->count(),
            'Bronze' => User::where('role', 'member')->where('tier_status', 'Bronze')->count(),
        ];

        $segmentCounts = User::where('role', 'member')
            ->whereNotNull('behavior_label')
            ->selectRaw('behavior_label, COUNT(*) as total')
            ->groupBy('behavior_label')
            ->pluck('total', 'behavior_label')
            ->all();

        $recentTransactions = Transaction::with('user')->latest()->limit(20)->get();

        return view('reports.sales', [
            'totalSales' => $totalSales,
            'totalTransactions' => $totalTransactions,
            'totalMembers' => $totalMembers,
            'avgSpending' => $avgSpending,
            'tierCounts' => $tierCounts,
            'segmentCounts' => $segmentCounts,
            'recentTransactions' => $recentTransactions,
            'generatedAt' => now()->format('d M Y H:i'),
        ]);
    }
}
