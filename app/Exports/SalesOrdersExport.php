<?php

namespace App\Exports;

use App\Models\SalesOrder;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;

class SalesOrdersExport implements FromCollection
{
    /**
     * @return Collection
     */
    public function collection()
    {
        return SalesOrder::with(['customer', 'user'])->get()->map(fn (SalesOrder $order) => [
            'Nomor' => $order->order_number,
            'Tanggal' => $order->getRawOriginal('order_date'),
            'Pelanggan' => $order->customer->name,
            'Sales' => $order->user->name,
            'Status' => $order->status,
            'Total' => $order->total_amount,
        ]);
    }
}
