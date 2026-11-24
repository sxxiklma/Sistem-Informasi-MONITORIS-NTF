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
            font-size: 8.5pt;
            color: #1e293b;
            line-height: 1.3;
        }
        .header-table {
            width: 100%;
            border-bottom: 2px solid #0f172a;
            padding-bottom: 8px;
            margin-bottom: 10px;
        }
        .company-name {
            font-size: 14pt;
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
            font-size: 12pt;
            font-weight: 700;
            color: #0f172a;
            margin: 0 0 4px 0;
            text-transform: uppercase;
        }
        .meta-table {
            width: 100%;
            font-size: 8pt;
        }
        .meta-table td {
            padding: 2px 0;
        }
        .meta-label {
            width: 140px;
            font-weight: 700;
            color: #475569;
        }
        .meta-val {
            color: #0f172a;
        }
        .kpi-row {
            width: 100%;
            margin-bottom: 12px;
        }
        .kpi-box {
            width: 24%;
            background-color: #f8fafc;
            border: 1px solid #cbd5e1;
            border-radius: 4px;
            padding: 6px 8px;
            text-align: left;
        }
        .kpi-label {
            font-size: 7pt;
            font-weight: 700;
            color: #64748b;
            text-transform: uppercase;
        }
        .kpi-val {
            font-size: 11pt;
            font-weight: 800;
            color: #0f172a;
            margin-top: 2px;
            font-family: 'Courier New', Courier, monospace;
        }
        .data-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 8pt;
            margin-bottom: 15px;
        }
        .data-table th {
            background-color: #0f172a;
            color: #ffffff;
            font-weight: 700;
            text-transform: uppercase;
            font-size: 7pt;
            padding: 6px 6px;
            text-align: left;
            border: 1px solid #0f172a;
        }
        .data-table td {
            padding: 5px 6px;
            border: 1px solid #cbd5e1;
            vertical-align: middle;
        }
        .data-table tr:nth-child(even) {
            background-color: #f8fafc;
        }
        .data-table tr.overdue {
            background-color: #fff1f2;
        }
        .font-mono {
            font-family: 'Courier New', Courier, monospace;
        }
        .text-right {
            text-align: right;
        }
        .text-center {
            text-align: center;
        }
        .badge {
            display: inline-block;
            padding: 2px 5px;
            font-size: 6.5pt;
            font-weight: 700;
            border-radius: 3px;
            text-transform: uppercase;
        }
        .badge-lunas { background-color: #dcfce7; color: #166534; }
        .badge-sebagian { background-color: #e0f2fe; color: #075985; }
        .badge-tempo { background-color: #fef3c7; color: #92400e; }
        .badge-overdue { background-color: #fee2e2; color: #991b1b; }
        .footer-table {
            width: 100%;
            margin-top: 20px;
            page-break-inside: avoid;
        }
        .sign-box {
            text-align: center;
            width: 25%;
        }
        .sign-space {
            height: 50px;
        }
        .sign-name {
            font-weight: 700;
            border-top: 1px solid #0f172a;
            padding-top: 3px;
            display: inline-block;
            min-width: 130px;
        }
    </style>
</head>
<body>
    {{-- Header --}}
    <table class="header-table">
        <tr>
            <td style="width: 60%;">
                <div class="company-name">MOTORISTRACK INDONESIA</div>
                <div class="company-sub">Sistem Operasional Distribusi & Keuangan Sales Motoris Lapangan</div>
            </td>
            <td style="width: 40%; text-align: right;">
                <div style="font-size: 8pt; color: #64748b;">Dokumen Resmi Finansial & Piutang</div>
                <div style="font-size: 8pt; font-weight: 700; color: #0f172a;">Tanggal Unduh: {{ $meta['downloaded_at'] }}</div>
            </td>
        </tr>
    </table>

    {{-- Title Box --}}
    <div class="report-title-box">
        <div class="report-title">{{ $meta['title'] }}</div>
        <table class="meta-table">
            <tr>
                <td class="meta-label">Diunduh Oleh:</td>
                <td class="meta-val">{{ $meta['downloaded_by'] }}</td>
                <td class="meta-label">Total Data Tagihan:</td>
                <td class="meta-val">{{ count($orders) }} Transaksi</td>
            </tr>
        </table>
    </div>

    {{-- KPI Stat Boxes --}}
    <table class="kpi-row" cellpadding="0" cellspacing="4">
        <tr>
            <td class="kpi-box">
                <div class="kpi-label">Total Nilai Penjualan</div>
                <div class="kpi-val">Rp {{ number_format($stats['total_omzet'], 0, ',', '.') }}</div>
            </td>
            <td class="kpi-box" style="background-color: #f0fdf4; border-color: #86efac;">
                <div class="kpi-label" style="color: #166534;">Total Kas Masuk (Lunas / DP)</div>
                <div class="kpi-val" style="color: #166534;">Rp {{ number_format($stats['total_kas_masuk'], 0, ',', '.') }}</div>
            </td>
            <td class="kpi-box" style="background-color: #eff6ff; border-color: #93c5fd;">
                <div class="kpi-label" style="color: #1e40af;">Total Piutang Berjalan</div>
                <div class="kpi-val" style="color: #1e40af;">Rp {{ number_format($stats['total_piutang'], 0, ',', '.') }}</div>
            </td>
            <td class="kpi-box" style="background-color: #fef2f2; border-color: #fca5a5;">
                <div class="kpi-label" style="color: #991b1b;">Kios dengan Piutang</div>
                <div class="kpi-val" style="color: #991b1b;">{{ $stats['count_piutang'] }} Tagihan</div>
            </td>
        </tr>
    </table>

    {{-- Table of Orders --}}
    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 4%;" class="text-center">No</th>
                <th style="width: 14%;">No. Faktur</th>
                <th style="width: 9%;">Tgl Pesan</th>
                <th style="width: 18%;">Kios Pelanggan</th>
                <th style="width: 11%;">Sales Motoris</th>
                <th style="width: 10%;">Metode</th>
                <th style="width: 9%;" class="text-right">Total Transaksi</th>
                <th style="width: 9%;" class="text-right">DP / Terbayar</th>
                <th style="width: 9%;" class="text-right">Sisa Piutang</th>
                <th style="width: 7%;" class="text-center">Jatuh Tempo</th>
            </tr>
        </thead>
        <tbody>
            @forelse($orders as $index => $order)
                @php
                    $isOverdue = $order->is_overdue;
                @endphp
                <tr class="{{ $isOverdue ? 'overdue' : '' }}">
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td class="font-mono" style="font-weight: 700;">{{ $order->order_number }}</td>
                    <td>{{ $order->order_date->format('d/m/Y') }}</td>
                    <td>
                        <strong>{{ $order->customer?->name ?? '-' }}</strong>
                        <div style="font-size: 7pt; color: #64748b;">{{ $order->customer?->phone ?? '-' }}</div>
                    </td>
                    <td>{{ $order->user?->name ?? '-' }}</td>
                    <td>
                        <span class="badge {{ $order->remaining_amount <= 0 ? 'badge-lunas' : ($isOverdue ? 'badge-overdue' : ($order->paid_amount > 0 ? 'badge-sebagian' : 'badge-tempo')) }}">
                            {{ strtoupper($order->payment_method) }} - {{ $order->remaining_amount <= 0 ? 'LUNAS' : ($isOverdue ? 'JATUH TEMPO' : ($order->paid_amount > 0 ? 'DP/SEBAGIAN' : 'TEMPO')) }}
                        </span>
                    </td>
                    <td class="text-right font-mono font-bold">
                        Rp {{ number_format($order->total_amount, 0, ',', '.') }}
                    </td>
                    <td class="text-right font-mono" style="color: #166534;">
                        Rp {{ number_format($order->paid_amount, 0, ',', '.') }}
                    </td>
                    <td class="text-right font-mono" style="font-weight: 700; color: {{ $order->remaining_amount > 0 ? ($isOverdue ? '#991b1b' : '#b45309') : '#64748b' }};">
                        Rp {{ number_format($order->remaining_amount, 0, ',', '.') }}
                    </td>
                    <td class="text-center font-mono">
                        {{ $order->due_date ? $order->due_date->format('d/m/Y') : '-' }}
                        @if($isOverdue)
                            <div style="font-size: 6.5pt; font-weight: 700; color: #991b1b;">(Telat {{ $order->days_overdue }} hari)</div>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="10" class="text-center" style="padding: 20px; color: #94a3b8;">
                        Tidak ada data keuangan yang ditemukan.
                    </td>
                </tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr style="background-color: #0f172a; color: #ffffff; font-weight: 700;">
                <td colspan="6" class="text-right" style="padding: 6px 8px; font-size: 8pt; text-transform: uppercase;">
                    TOTAL REKAPITULASI :
                </td>
                <td class="text-right font-mono" style="padding: 6px 8px; color: #fef08a;">
                    Rp {{ number_format($orders->sum('total_amount'), 0, ',', '.') }}
                </td>
                <td class="text-right font-mono" style="padding: 6px 8px; color: #86efac;">
                    Rp {{ number_format($orders->sum('paid_amount'), 0, ',', '.') }}
                </td>
                <td class="text-right font-mono" style="padding: 6px 8px; color: #fca5a5;">
                    Rp {{ number_format($orders->sum('remaining_amount'), 0, ',', '.') }}
                </td>
                <td></td>
            </tr>
        </tfoot>
    </table>

    {{-- Signature Section --}}
    <table class="footer-table">
        <tr>
            <td class="sign-box">
                <div style="font-size: 7.5pt; color: #64748b;">Dibuat Oleh:</div>
                <div class="sign-space"></div>
                <div class="sign-name">{{ $meta['downloaded_by'] }}</div>
                <div style="font-size: 7pt; color: #64748b; margin-top: 2px;">Petugas Pelapor</div>
            </td>
            <td style="width: 50%;"></td>
            <td class="sign-box">
                <div style="font-size: 7.5pt; color: #64748b;">Mengetahui & Menyetujui:</div>
                <div class="sign-space"></div>
                <div class="sign-name">Supervisor / Finance Manager</div>
                <div style="font-size: 7pt; color: #64748b; margin-top: 2px;">Manajemen MotorisTrack</div>
            </td>
        </tr>
    </table>
</body>
</html>

