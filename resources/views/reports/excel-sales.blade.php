<table>
    <thead>
        <tr>
            <th colspan="9" style="font-size: 14pt; font-weight: bold; text-align: center;">MOTORISTRACK - SISTEM INFORMASI OPERASIONAL SALES MOTORIS</th>
        </tr>
        <tr>
            <th colspan="9" style="font-size: 12pt; font-weight: bold; text-align: center; color: #1e3a8a;">{{ $meta['title'] }}</th>
        </tr>
        <tr>
            <th colspan="2" style="font-weight: bold;">Waktu Download:</th>
            <th colspan="7">{{ $meta['downloaded_at'] }}</th>
        </tr>
        <tr>
            <th colspan="2" style="font-weight: bold;">Diunduh Oleh:</th>
            <th colspan="7">{{ $meta['downloaded_by'] }}</th>
        </tr>
        <tr>
            <th colspan="2" style="font-weight: bold;">Filter / Periode:</th>
            <th colspan="7">{{ $meta['filter_info'] }}</th>
        </tr>
        <tr>
            <th colspan="9"></th>
        </tr>
        <tr style="background-color: #1e293b; color: #ffffff; font-weight: bold;">
            <th style="border: 1px solid #cbd5e1; text-align: center; width: 50px;">No</th>
            <th style="border: 1px solid #cbd5e1; text-align: center; width: 150px;">Kode Pesanan</th>
            <th style="border: 1px solid #cbd5e1; text-align: center; width: 130px;">Tanggal Pesanan</th>
            <th style="border: 1px solid #cbd5e1; text-align: left; width: 200px;">Outlet / Pelanggan</th>
            <th style="border: 1px solid #cbd5e1; text-align: left; width: 170px;">Sales Motoris</th>
            <th style="border: 1px solid #cbd5e1; text-align: left; width: 150px;">Tim Sales</th>
            <th style="border: 1px solid #cbd5e1; text-align: center; width: 100px;">Total Item</th>
            <th style="border: 1px solid #cbd5e1; text-align: center; width: 130px;">Status</th>
            <th style="border: 1px solid #cbd5e1; text-align: right; width: 160px;">Total Omzet (Rp)</th>
        </tr>
    </thead>
    <tbody>
        @php $grandTotal = 0; @endphp
        @forelse($orders as $index => $order)
            @php $grandTotal += $order->total_amount; @endphp
            <tr>
                <td style="border: 1px solid #e2e8f0; text-align: center;">{{ $index + 1 }}</td>
                <td style="border: 1px solid #e2e8f0; text-align: center; font-weight: bold;">{{ $order->order_number }}</td>
                <td style="border: 1px solid #e2e8f0; text-align: center;">{{ $order->order_date ? \Carbon\Carbon::parse($order->order_date)->format('d/m/Y') : '-' }}</td>
                <td style="border: 1px solid #e2e8f0;">{{ $order->customer->name ?? '-' }}</td>
                <td style="border: 1px solid #e2e8f0;">{{ $order->user->name ?? '-' }}</td>
                <td style="border: 1px solid #e2e8f0;">{{ $order->user->team->name ?? '-' }}</td>
                <td style="border: 1px solid #e2e8f0; text-align: center;">{{ $order->items_count ?? $order->items->sum('quantity') }}</td>
                <td style="border: 1px solid #e2e8f0; text-align: center;">{{ strtoupper($order->status) }}</td>
                <td style="border: 1px solid #e2e8f0; text-align: right; font-weight: bold;">{{ number_format($order->total_amount, 0, ',', '.') }}</td>
            </tr>
        @empty
            <tr>
                <td colspan="9" style="border: 1px solid #e2e8f0; text-align: center; padding: 10px;">Tidak ada data pesanan penjualan yang sesuai dengan filter.</td>
            </tr>
        @endforelse
    </tbody>
    <tfoot>
        <tr style="background-color: #f1f5f9; font-weight: bold;">
            <td colspan="8" style="border: 1px solid #cbd5e1; text-align: right; font-weight: bold;">TOTAL OMZET:</td>
            <td style="border: 1px solid #cbd5e1; text-align: right; font-weight: bold; color: #b45309;">Rp {{ number_format($grandTotal, 0, ',', '.') }}</td>
        </tr>
    </tfoot>
</table>

