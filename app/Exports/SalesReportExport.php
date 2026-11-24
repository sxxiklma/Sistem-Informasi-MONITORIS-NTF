<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class SalesReportExport implements FromView, ShouldAutoSize, WithStyles
{
    public function __construct(
        protected Collection $orders,
        protected array $meta
    ) {}

    public function view(): View
    {
        return view('reports.excel-sales', [
            'orders' => $this->orders,
            'meta' => $this->meta,
        ]);
    }

    public function styles(Worksheet $sheet)
    {
        // Title row styling
        $sheet->getStyle('A1:H1')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A2:H2')->getFont()->setBold(true)->setSize(12)->getColor()->setRGB('1E3A8A');

        // Metadata rows
        $sheet->getStyle('A3:A5')->getFont()->setBold(true);

        return [];
    }
}
