<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>{{ $meta['title'] }}</title>
    <style>
        @page {
            size: A4 landscape;
            margin: 12mm 15mm 15mm 15mm;
        }
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            font-size: 9pt;
            color: #1e293b;
            line-height: 1.35;
        }
        .header-table {
            width: 100%;
            border-bottom: 2px solid #0f172a;
            padding-bottom: 8px;
            margin-bottom: 12px;
        }
        .company-name {
            font-size: 15pt;
            font-weight: 800;
            color: #0f172a;
            letter-spacing: 0.5px;
        }
        .company-sub {
            font-size: 8pt;
            color: #64748b;
        }
        .report-title-box {
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            border-left: 4px solid #f59e0b;
            padding: 8px 12px;
            margin-bottom: 12px;
        }
        .report-title {
            font-size: 13pt;
            font-weight: 700;
            color: #0f172a;
            margin: 0 0 4px 0;
            text-transform: uppercase;
        }
        .meta-table {
            width: 100%;
            font-size: 8.5pt;
        }
        .meta-table td {
            padding: 2px 0;
        }
        .meta-label {
            width: 130px;
            font-weight: 700;
            color: #475569;
        }
        .meta-val {
            color: #0f172a;
        }
        .kpi-row {
            width: 100%;
            margin-bottom: 14px;
        }
        .kpi-box {
            width: 32%;
            background-color: #f1f5f9;
            border: 1px solid #cbd5e1;
            border-radius: 4px;
            padding: 6px 10px;
            text-align: left;
        }
        .kpi-label {
            font-size: 7.5pt;
            font-weight: 700;
            color: #64748b;
            text-transform: uppercase;
        }
        .kpi-val {
            font-size: 12pt;
            font-weight: 800;
            color: #0f172a;
            margin-top: 2px;
        }
        table.data-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 8pt;
        }
        table.data-table th {
            background-color: #0f172a;
            color: #ffffff;
            font-weight: 700;
            padding: 6px 6px;
            text-align: left;
            border: 1px solid #0f172a;
            text-transform: uppercase;
            font-size: 7.5pt;
            letter-spacing: 0.3px;
        }
        table.data-table td {
            padding: 5px 6px;
            border: 1px solid #e2e8f0;
            vertical-align: middle;
        }
        table.data-table tr:nth-child(even) {
            background-color: #f8fafc;
        }
        .status-badge {
            display: inline-block;
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 7pt;
            font-weight: 700;
            text-transform: uppercase;
        }
        .status-completed { background-color: #d1fae5; color: #065f46; }
        .status-confirmed { background-color: #e0f2fe; color: #0369a1; }
        .status-draft { background-color: #f1f5f9; color: #475569; }
        .status-cancelled { background-color: #ffe4e6; color: #9f1239; }
        .footer-table {
            width: 100%;
            margin-top: 20px;
            padding-top: 8px;
            border-top: 1px solid #e2e8f0;
            font-size: 7.5pt;
            color: #94a3b8;
        }
    </style>
</head>
<body>
    {{-- Header Perusahaan --}}
    <table class="header-table">
        <tr>
            <td>
                <div class="company-name">Motoris<span style="color:#f59e0b;">Track</span></div>
                <div class="company-sub">Sistem Informasi & Manajemen Operasional Sales Motoris Lapangan</div>
            </td>
            <td style="text-align: right; vertical-align: bottom;">
                <span style="font-size: 8pt; color: #64748b; font-weight: 600;">DOKUMEN RESMI OPERASIONAL</span>
            </td>
        </tr>
    </table>

    {{-- Judul Laporan & Metadata Waktu Download --}}
    <div class="report-title-box">
        <h1 class="report-title">{{ $meta['title'] }}</h1>
        <table class="meta-table">
            <tr>
                <td class="meta-label">Tanggal & Jam Unduh:</td>
                <td class="meta-val" style="font-weight: 700; color: #0284c7;">{{ $meta['downloaded_at'] }}</td>
                <td class="meta-label">Diunduh Oleh:</td>
                <td class="meta-val">{{ $meta['downloaded_by'] }}</td>
            </tr>
            <tr>
                <td class="meta-label">Filter & Parameter:</td>
                <td class="meta-val" colspan="3">{{ $meta['filter_info'] }}</td>
            </tr>
        </table>
    </div>

    {{-- Ringkasan KPI --}}
    @php
        $grandTotal = $orders->sum('total_amount');
        $orderCount = $orders->count();
        $avgOrder = $orderCount > 0 ? $grandTotal / $orderCount : 0;
    @endphp
    <table class="kpi-row" style="border-collapse: separate; border-spacing: 6px 0;">
        <tr>
            <td class="kpi-box">
                <div class="kpi-label">Total Pesanan Terfilter</div>
                <div class="kpi-val">{{ $orderCount }} Transaksi</div>
            </td>
            <td class="kpi-box">
                <div class="kpi-label">Total Akumulasi Omzet</div>
                <div class="kpi-val" style="color: #b45309;">Rp {{ number_format($grandTotal, 0, ',', '.') }}</div>
            </td>
            <td class="kpi-box">
                <div class="kpi-label">Rata-Rata Nilai per Pesanan</div>
                <div class="kpi-val" style="color: #0369a1;">Rp {{ number_format($avgOrder, 0, ',', '.') }}</div>
            </td>
        </tr>
    </table>

    {{-- Tabel Data --}}
    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 25px; text-align: center;">No</th>
                <th style="width: 95px; text-align: center;">Kode Order</th>
                <th style="width: 75px; text-align: center;">Tanggal</th>
                <th style="width: 150px;">Outlet / Pelanggan</th>
                <th style="width: 120px;">Sales Motoris</th>
                <th style="width: 110px;">Tim Sales</th>
                <th style="width: 55px; text-align: center;">Item</th>
                <th style="width: 75px; text-align: center;">Status</th>
                <th style="width: 95px; text-align: right;">Total Nilai (Rp)</th>
            </tr>
        </thead>
        <tbody>
            @forelse($orders as $idx => $order)
                @php
                    $statusClass = match(strtolower($order->status)) {
                        'completed' => 'status-completed',
                        'confirmed' => 'status-confirmed',
                        'cancelled' => 'status-cancelled',
                        default => 'status-draft',
                    };
                @endphp
                <tr>
                    <td style="text-align: center;">{{ $idx + 1 }}</td>
                    <td style="text-align: center; font-weight: bold;">{{ $order->order_number }}</td>
                    <td style="text-align: center;">{{ $order->order_date ? \Carbon\Carbon::parse($order->order_date)->format('d/m/Y') : '-' }}</td>
                    <td><strong>{{ $order->customer->name ?? '-' }}</strong></td>
                    <td>{{ $order->user->name ?? '-' }}</td>
                    <td>{{ $order->user->team->name ?? '-' }}</td>
                    <td style="text-align: center;">{{ $order->items_count ?? $order->items->sum('quantity') }}</td>
                    <td style="text-align: center;">
                        <span class="status-badge {{ $statusClass }}">{{ $order->status }}</span>
                    </td>
                    <td style="text-align: right; font-weight: bold;">{{ number_format($order->total_amount, 0, ',', '.') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="9" style="text-align: center; padding: 15px; color: #94a3b8;">
                        Tidak ada transaksi penjualan yang sesuai dengan filter yang dipilih.
                    </td>
                </tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr style="background-color: #f1f5f9; font-weight: bold;">
                <td colspan="8" style="text-align: right; font-size: 8.5pt;">TOTAL KESELURUHAN OMZET:</td>
                <td style="text-align: right; font-size: 8.5pt; color: #b45309;">Rp {{ number_format($grandTotal, 0, ',', '.') }}</td>
            </tr>
        </tfoot>
    </table>

    {{-- Footer Dokumen --}}
    <table class="footer-table">
        <tr>
            <td>Dokumen ini digenerate secara otomatis oleh sistem MotorisTrack. Informasi bersifat rahasia untuk keperluan internal perusahaan.</td>
            <td style="text-align: right;">Halaman 1</td>
        </tr>
    </table>
</body>
</html>

