<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Laporan Penjualan - Smart Coffee</title>
    <style>
        * { box-sizing: border-box; }
        body { font-family: 'Segoe UI', Arial, sans-serif; color: #2b1a12; margin: 32px; }
        h1 { font-size: 22px; margin: 0; }
        .muted { color: #7a6a5f; font-size: 12px; }
        .header { border-bottom: 3px solid #2b1a12; padding-bottom: 12px; margin-bottom: 20px; display: flex; justify-content: space-between; align-items: flex-end; }
        .metrics { display: grid; grid-template-columns: repeat(4, 1fr); gap: 12px; margin-bottom: 24px; }
        .card { border: 2px solid #2b1a12; border-radius: 8px; padding: 12px; }
        .card .label { font-size: 10px; text-transform: uppercase; letter-spacing: 1px; color: #7a6a5f; font-weight: 700; }
        .card .value { font-size: 18px; font-weight: 800; margin-top: 4px; }
        h2 { font-size: 14px; text-transform: uppercase; border-left: 4px solid #b9472f; padding-left: 8px; margin: 24px 0 10px; }
        table { width: 100%; border-collapse: collapse; font-size: 12px; }
        th, td { border: 1px solid #ccc; padding: 6px 8px; text-align: left; }
        th { background: #2b1a12; color: #fff; }
        .two-col { display: grid; grid-template-columns: 1fr 1fr; gap: 24px; }
        .print-btn { background: #b9472f; color: #fff; border: none; padding: 10px 18px; border-radius: 6px; font-weight: 700; cursor: pointer; }
        @media print { .no-print { display: none; } body { margin: 0; } }
    </style>
</head>
<body>
    <div class="no-print" style="margin-bottom:16px;">
        <button class="print-btn" onclick="window.print()">🖨️ Cetak / Simpan sebagai PDF</button>
    </div>

    <div class="header">
        <div>
            <h1>☕ Smart Coffee</h1>
            <p class="muted">Laporan Penjualan & Loyalitas Pelanggan</p>
        </div>
        <div class="muted">Dibuat: {{ $generatedAt }}</div>
    </div>

    <div class="metrics">
        <div class="card"><div class="label">Total Penjualan</div><div class="value">Rp {{ number_format($totalSales, 0, ',', '.') }}</div></div>
        <div class="card"><div class="label">Total Transaksi</div><div class="value">{{ number_format($totalTransactions, 0, ',', '.') }}</div></div>
        <div class="card"><div class="label">Total Member</div><div class="value">{{ number_format($totalMembers, 0, ',', '.') }}</div></div>
        <div class="card"><div class="label">Rata-rata Belanja</div><div class="value">Rp {{ number_format($avgSpending, 0, ',', '.') }}</div></div>
    </div>

    <div class="two-col">
        <div>
            <h2>Distribusi Tier</h2>
            <table>
                <tr><th>Tier</th><th>Jumlah Member</th></tr>
                @foreach($tierCounts as $tier => $count)
                    <tr><td>{{ $tier }}</td><td>{{ $count }}</td></tr>
                @endforeach
            </table>
        </div>
        <div>
            <h2>Segmentasi KNN</h2>
            <table>
                <tr><th>Segmen</th><th>Jumlah Member</th></tr>
                @forelse($segmentCounts as $segment => $count)
                    <tr><td>{{ $segment }}</td><td>{{ $count }}</td></tr>
                @empty
                    <tr><td colspan="2">Belum ada data segmentasi.</td></tr>
                @endforelse
            </table>
        </div>
    </div>

    <h2>20 Transaksi Terbaru</h2>
    <table>
        <tr><th>ID</th><th>Member</th><th>Jumlah Item</th><th>Total Bayar</th><th>Tanggal</th></tr>
        @forelse($recentTransactions as $tx)
            <tr>
                <td>#{{ str_pad((string) $tx->id, 5, '0', STR_PAD_LEFT) }}</td>
                <td>{{ $tx->user->name ?? '-' }}</td>
                <td>{{ $tx->jumlah_item }}</td>
                <td>Rp {{ number_format($tx->total_bayar, 0, ',', '.') }}</td>
                <td>{{ optional($tx->created_at)->format('d M Y, H:i') }}</td>
            </tr>
        @empty
            <tr><td colspan="5">Belum ada transaksi.</td></tr>
        @endforelse
    </table>

    <script>
        window.addEventListener('load', function () { setTimeout(function () { window.print(); }, 400); });
    </script>
</body>
</html>
