<table>
    <thead>
        <tr>
            <th colspan="11" style="font-size: 14pt; font-weight: bold; text-align: center;">MOTORISTRACK - SISTEM INFORMASI OPERASIONAL SALES MOTORIS</th>
        </tr>
        <tr>
            <th colspan="11" style="font-size: 12pt; font-weight: bold; text-align: center; color: #1e3a8a;">{{ $meta['title'] }}</th>
        </tr>
        <tr>
            <th colspan="2" style="font-weight: bold;">Waktu Download:</th>
            <th colspan="9">{{ $meta['downloaded_at'] }}</th>
        </tr>
        <tr>
            <th colspan="2" style="font-weight: bold;">Diunduh Oleh:</th>
            <th colspan="9">{{ $meta['downloaded_by'] }}</th>
        </tr>
        <tr>
            <th colspan="2" style="font-weight: bold;">Filter / Periode:</th>
            <th colspan="9">{{ $meta['filter_info'] }}</th>
        </tr>
        <tr>
            <th colspan="11"></th>
        </tr>
        <tr style="background-color: #1e293b; color: #ffffff; font-weight: bold;">
            <th style="border: 1px solid #cbd5e1; text-align: center; width: 50px;">No</th>
            <th style="border: 1px solid #cbd5e1; text-align: center; width: 150px;">Kode Tugas</th>
            <th style="border: 1px solid #cbd5e1; text-align: center; width: 140px;">Jadwal Kunjungan</th>
            <th style="border: 1px solid #cbd5e1; text-align: left; width: 200px;">Outlet Tujuan</th>
            <th style="border: 1px solid #cbd5e1; text-align: left; width: 170px;">Sales Motoris</th>
            <th style="border: 1px solid #cbd5e1; text-align: left; width: 150px;">Tim Sales</th>
            <th style="border: 1px solid #cbd5e1; text-align: center; width: 120px;">Jam Masuk (In)</th>
            <th style="border: 1px solid #cbd5e1; text-align: center; width: 120px;">Jam Keluar (Out)</th>
            <th style="border: 1px solid #cbd5e1; text-align: center; width: 140px;">Durasi Waktu</th>
            <th style="border: 1px solid #cbd5e1; text-align: center; width: 100px;">Akurasi GPS</th>
            <th style="border: 1px solid #cbd5e1; text-align: center; width: 140px;">Status Kunjungan</th>
        </tr>
    </thead>
    <tbody>
        @php 
            $totalMins = 0; 
            $completedCount = 0;
        @endphp
        @forelse($tasks as $index => $task)
            @php
                $durationStr = '-';
                if ($task->check_in_at && $task->check_out_at) {
                    $mins = $task->check_out_at->diffInMinutes($task->check_in_at);
                    $totalMins += $mins;
                    $h = floor($mins / 60);
                    $m = $mins % 60;
                    $durationStr = $h > 0 ? "{$h}j {$m}m" : "{$mins} Menit";
                    $completedCount++;
                } elseif ($task->check_in_at) {
                    $mins = now()->diffInMinutes($task->check_in_at);
                    $durationStr = "{$mins}m (Aktif)";
                }
            @endphp
            <tr>
                <td style="border: 1px solid #e2e8f0; text-align: center;">{{ $index + 1 }}</td>
                <td style="border: 1px solid #e2e8f0; text-align: center; font-weight: bold;">{{ $task->task_code }}</td>
                <td style="border: 1px solid #e2e8f0; text-align: center;">{{ $task->scheduled_at ? $task->scheduled_at->format('d/m/Y H:i') : '-' }}</td>
                <td style="border: 1px solid #e2e8f0;">{{ $task->customer->name ?? '-' }}</td>
                <td style="border: 1px solid #e2e8f0;">{{ $task->sales->name ?? '-' }}</td>
                <td style="border: 1px solid #e2e8f0;">{{ $task->sales->team->name ?? '-' }}</td>
                <td style="border: 1px solid #e2e8f0; text-align: center;">{{ $task->check_in_at ? $task->check_in_at->format('H:i') : '-' }}</td>
                <td style="border: 1px solid #e2e8f0; text-align: center;">{{ $task->check_out_at ? $task->check_out_at->format('H:i') : '-' }}</td>
                <td style="border: 1px solid #e2e8f0; text-align: center; font-weight: bold;">{{ $durationStr }}</td>
                <td style="border: 1px solid #e2e8f0; text-align: center;">{{ $task->check_in_distance_meters ? round($task->check_in_distance_meters) . 'm' : '-' }}</td>
                <td style="border: 1px solid #e2e8f0; text-align: center;">{{ str_replace('_', ' ', $task->status) }}</td>
            </tr>
        @empty
            <tr>
                <td colspan="11" style="border: 1px solid #e2e8f0; text-align: center; padding: 10px;">Tidak ada data tugas kunjungan yang sesuai dengan filter.</td>
            </tr>
        @endforelse
    </tbody>
    <tfoot>
        @php
            $totH = floor($totalMins / 60);
            $totM = $totalMins % 60;
            $totDurationStr = $totH > 0 ? "{$totH} Jam {$totM} Menit" : "{$totalMins} Menit";
        @endphp
        <tr style="background-color: #f1f5f9; font-weight: bold;">
            <td colspan="8" style="border: 1px solid #cbd5e1; text-align: right; font-weight: bold;">TOTAL DURASI KUNJUNGAN SELESAI:</td>
            <td style="border: 1px solid #cbd5e1; text-align: center; font-weight: bold; color: #059669;">{{ $totDurationStr }}</td>
            <td colspan="2" style="border: 1px solid #cbd5e1; text-align: center; font-weight: bold;">{{ $completedCount }} Selesai / {{ count($tasks) }} Total</td>
        </tr>
    </tfoot>
</table>

