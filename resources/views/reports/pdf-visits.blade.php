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
            border-left: 4px solid #10b981;
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
            padding: 6px 5px;
            text-align: left;
            border: 1px solid #0f172a;
            text-transform: uppercase;
            font-size: 7.5pt;
            letter-spacing: 0.3px;
        }
        table.data-table td {
            padding: 5px 5px;
            border: 1px solid #e2e8f0;
            vertical-align: middle;
        }
        table.data-table tr:nth-child(even) {
            background-color: #f8fafc;
        }
        .status-badge {
            display: inline-block;
            padding: 2px 5px;
            border-radius: 3px;
            font-size: 7pt;
            font-weight: 700;
            text-transform: uppercase;
        }
        .status-selesai { background-color: #d1fae5; color: #065f46; }
        .status-proses { background-color: #e0f2fe; color: #0369a1; }
        .status-menunggu { background-color: #fef3c7; color: #92400e; }
        .status-ditolak { background-color: #ffe4e6; color: #9f1239; }
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
                <div class="company-name">Motoris<span style="color:#10b981;">Track</span></div>
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
                <td class="meta-val" style="font-weight: 700; color: #059669;">{{ $meta['downloaded_at'] }}</td>
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
        $taskCount = $tasks->count();
        $completedCount = $tasks->where('status', 'SELESAI')->count();
        $totalMinutes = 0;
        foreach ($tasks as $t) {
            if ($t->check_in_at && $t->check_out_at) {
                $totalMinutes += $t->check_out_at->diffInMinutes($t->check_in_at);
            }
        }
        $totH = floor($totalMinutes / 60);
        $totM = $totalMinutes % 60;
        $totalDurStr = $totH > 0 ? "{$totH} Jam {$totM} Menit" : "{$totalMinutes} Menit";
    @endphp
    <table class="kpi-row" style="border-collapse: separate; border-spacing: 6px 0;">
        <tr>
            <td class="kpi-box">
                <div class="kpi-label">Total Jadwal Kunjungan</div>
                <div class="kpi-val">{{ $taskCount }} Kunjungan</div>
            </td>
            <td class="kpi-box">
                <div class="kpi-label">Kunjungan Berhasil Selesai</div>
                <div class="kpi-val" style="color: #059669;">{{ $completedCount }} Selesai ({{ $taskCount > 0 ? round(($completedCount/$taskCount)*100) : 0 }}%)</div>
            </td>
            <td class="kpi-box">
                <div class="kpi-label">Total Akumulasi Durasi Waktu</div>
                <div class="kpi-val" style="color: #0284c7;">{{ $totalDurStr }}</div>
            </td>
        </tr>
    </table>

    {{-- Tabel Data --}}
    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 25px; text-align: center;">No</th>
                <th style="width: 90px; text-align: center;">Kode Tugas</th>
                <th style="width: 75px; text-align: center;">Jadwal</th>
                <th style="width: 140px;">Outlet Tujuan</th>
                <th style="width: 110px;">Sales Motoris</th>
                <th style="width: 100px;">Tim Sales</th>
                <th style="width: 60px; text-align: center;">Jam In</th>
                <th style="width: 60px; text-align: center;">Jam Out</th>
                <th style="width: 85px; text-align: center;">Durasi Waktu</th>
                <th style="width: 60px; text-align: center;">GPS (m)</th>
                <th style="width: 85px; text-align: center;">Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($tasks as $idx => $task)
                @php
                    $durationStr = '-';
                    if ($task->check_in_at && $task->check_out_at) {
                        $mins = $task->check_out_at->diffInMinutes($task->check_in_at);
                        $h = floor($mins / 60);
                        $m = $mins % 60;
                        $durationStr = $h > 0 ? "{$h}j {$m}m" : "{$mins} Menit";
                    } elseif ($task->check_in_at) {
                        $mins = now()->diffInMinutes($task->check_in_at);
                        $durationStr = "{$mins}m (Aktif)";
                    }

                    $badgeClass = match($task->status) {
                        'SELESAI' => 'status-selesai',
                        'DALAM_PERJALANAN', 'TIBA_DI_LOKASI', 'SEDANG_KUNJUNGAN', 'DITERIMA' => 'status-proses',
                        'MENUNGGU_RESPONS' => 'status-menunggu',
                        'DITOLAK' => 'status-ditolak',
                        default => 'status-proses',
                    };
                @endphp
                <tr>
                    <td style="text-align: center;">{{ $idx + 1 }}</td>
                    <td style="text-align: center; font-weight: bold;">{{ $task->task_code }}</td>
                    <td style="text-align: center;">{{ $task->scheduled_at ? $task->scheduled_at->format('d/m/Y H:i') : '-' }}</td>
                    <td><strong>{{ $task->customer->name ?? '-' }}</strong></td>
                    <td>{{ $task->sales->name ?? '-' }}</td>
                    <td>{{ $task->sales->team->name ?? '-' }}</td>
                    <td style="text-align: center;">{{ $task->check_in_at ? $task->check_in_at->format('H:i') : '-' }}</td>
                    <td style="text-align: center;">{{ $task->check_out_at ? $task->check_out_at->format('H:i') : '-' }}</td>
                    <td style="text-align: center; font-weight: bold; color: #0f172a;">{{ $durationStr }}</td>
                    <td style="text-align: center;">{{ $task->check_in_distance_meters ? round($task->check_in_distance_meters) . 'm' : '-' }}</td>
                    <td style="text-align: center;">
                        <span class="status-badge {{ $badgeClass }}">{{ str_replace('_', ' ', $task->status) }}</span>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="11" style="text-align: center; padding: 15px; color: #94a3b8;">
                        Tidak ada data tugas kunjungan yang sesuai dengan filter yang dipilih.
                    </td>
                </tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr style="background-color: #f1f5f9; font-weight: bold;">
                <td colspan="8" style="text-align: right; font-size: 8.5pt;">TOTAL DURASI KUNJUNGAN SELESAI:</td>
                <td style="text-align: center; font-size: 8.5pt; color: #059669;">{{ $totalDurStr }}</td>
                <td colspan="2" style="text-align: center; font-size: 8pt;">{{ $completedCount }} Selesai</td>
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

