<?php

namespace App\Http\Controllers;

use App\Exports\SalesReportExport;
use App\Exports\VisitsReportExport;
use App\Models\SalesOrder;
use App\Models\Team;
use App\Models\User;
use App\Models\VisitTask;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Response;

class ReportController extends Controller
{
    /**
     * Tampilan utama halaman laporan operasional dengan filter, pencarian, dan tabs
     */
    public function index(Request $request): View
    {
        $user = Auth::user();
        abort_unless($user instanceof User && $user->can('lihat laporan'), 403);

        $type = $request->get('type', 'sales'); // 'sales' or 'visits'
        $teams = Team::orderBy('name')->get();
        $salesList = User::role('Sales Motoris')->where('is_active', true)->orderBy('name')->get();

        if ($type === 'visits') {
            $data = $this->queryVisits($request);
            $tasks = (clone $data['query'])->paginate(15)->appends($request->query());
            $stats = $data['stats'];
            $orders = null;
        } else {
            $data = $this->querySales($request);
            $orders = (clone $data['query'])->paginate(15)->appends($request->query());
            $stats = $data['stats'];
            $tasks = null;
        }

        return view('reports.index', compact(
            'type',
            'teams',
            'salesList',
            'orders',
            'tasks',
            'stats'
        ));
    }

    /**
     * Export data ke Excel (.xlsx) dengan metadata download & judul
     */
    public function exportExcel(Request $request): BinaryFileResponse
    {
        $user = Auth::user();
        abort_unless($user instanceof User && $user->can('lihat laporan'), 403);

        $type = $request->get('type', 'sales');
        $timestamp = now()->timezone('Asia/Jakarta');
        $timeStr = $timestamp->translatedFormat('l, d F Y - H:i:s').' WIB';
        $userStr = $user->name.' ('.($user->roles->first()?->name ?? 'User').')';

        if ($type === 'visits') {
            $data = $this->queryVisits($request);
            $tasks = $data['query']->get();
            $meta = [
                'title' => 'LAPORAN REKAPITULASI KUNJUNGAN & DURASI WAKTU SALES MOTORIS',
                'downloaded_at' => $timeStr,
                'downloaded_by' => $userStr,
                'filter_info' => $this->buildFilterInfo($request, 'visits'),
            ];
            $filename = 'laporan-kunjungan-durasi-'.$timestamp->format('Y-m-d_His').'.xlsx';

            return Excel::download(new VisitsReportExport($tasks, $meta), $filename);
        } else {
            $data = $this->querySales($request);
            $orders = $data['query']->get();
            $meta = [
                'title' => 'LAPORAN REKAPITULASI PENJUALAN & PESANAN SALES MOTORIS',
                'downloaded_at' => $timeStr,
                'downloaded_by' => $userStr,
                'filter_info' => $this->buildFilterInfo($request, 'sales'),
            ];
            $filename = 'laporan-penjualan-'.$timestamp->format('Y-m-d_His').'.xlsx';

            return Excel::download(new SalesReportExport($orders, $meta), $filename);
        }
    }

    /**
     * Export data ke PDF (.pdf) dengan metadata download & judul
     */
    public function exportPdf(Request $request): Response
    {
        $user = Auth::user();
        abort_unless($user instanceof User && $user->can('lihat laporan'), 403);

        $type = $request->get('type', 'sales');
        $timestamp = now()->timezone('Asia/Jakarta');
        $timeStr = $timestamp->translatedFormat('l, d F Y - H:i:s').' WIB';
        $userStr = $user->name.' ('.($user->roles->first()?->name ?? 'User').')';

        if ($type === 'visits') {
            $data = $this->queryVisits($request);
            $tasks = $data['query']->get();
            $meta = [
                'title' => 'LAPORAN REKAPITULASI KUNJUNGAN & DURASI WAKTU SALES MOTORIS',
                'downloaded_at' => $timeStr,
                'downloaded_by' => $userStr,
                'filter_info' => $this->buildFilterInfo($request, 'visits'),
            ];
            $filename = 'laporan-kunjungan-durasi-'.$timestamp->format('Y-m-d_His').'.pdf';
            $pdf = Pdf::loadView('reports.pdf-visits', compact('tasks', 'meta'))
                ->setPaper('a4', 'landscape');

            return $pdf->download($filename);
        } else {
            $data = $this->querySales($request);
            $orders = $data['query']->get();
            $meta = [
                'title' => 'LAPORAN REKAPITULASI PENJUALAN & PESANAN SALES MOTORIS',
                'downloaded_at' => $timeStr,
                'downloaded_by' => $userStr,
                'filter_info' => $this->buildFilterInfo($request, 'sales'),
            ];
            $filename = 'laporan-penjualan-'.$timestamp->format('Y-m-d_His').'.pdf';
            $pdf = Pdf::loadView('reports.pdf-sales', compact('orders', 'meta'))
                ->setPaper('a4', 'landscape');

            return $pdf->download($filename);
        }
    }

    /**
     * Backward-compatible endpoint for existing excel download route
     */
    public function salesOrders(): BinaryFileResponse
    {
        return $this->exportExcel(request());
    }

    /**
     * Helper query builder untuk Laporan Penjualan
     */
    private function querySales(Request $request): array
    {
        $query = SalesOrder::with(['customer', 'user.team', 'items'])
            ->latest('order_date');

        // Search text
        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('order_number', 'like', "%{$search}%")
                    ->orWhere('notes', 'like', "%{$search}%")
                    ->orWhereHas('customer', fn ($c) => $c->where('name', 'like', "%{$search}%"))
                    ->orWhereHas('user', fn ($u) => $u->where('name', 'like', "%{$search}%"));
            });
        }

        // Filter Tim
        if ($teamId = $request->get('team_id')) {
            $query->whereHas('user', fn ($u) => $u->where('team_id', $teamId));
        }

        // Filter Sales
        if ($salesId = $request->get('sales_id')) {
            $query->where('user_id', $salesId);
        }

        // Filter Status
        if ($status = $request->get('status')) {
            $query->where('status', $status);
        }

        // Filter Tanggal / Periode
        $period = $request->get('period');
        if ($period === 'today') {
            $query->whereDate('order_date', today());
        } elseif ($period === 'this_week') {
            $query->whereBetween('order_date', [now()->startOfWeek()->toDateString(), now()->endOfWeek()->toDateString()]);
        } elseif ($period === 'this_month') {
            $query->whereMonth('order_date', now()->month)->whereYear('order_date', now()->year);
        } else {
            if ($from = $request->get('date_from')) {
                $query->whereDate('order_date', '>=', $from);
            }
            if ($to = $request->get('date_to')) {
                $query->whereDate('order_date', '<=', $to);
            }
        }

        // Calculate statistics
        $all = (clone $query)->get();
        $totalOrders = $all->count();
        $totalRevenue = $all->sum('total_amount');
        $completedOrders = $all->where('status', 'completed')->count();
        $avgOrder = $totalOrders > 0 ? $totalRevenue / $totalOrders : 0;

        return [
            'query' => $query,
            'stats' => [
                'total_orders' => $totalOrders,
                'total_revenue' => $totalRevenue,
                'completed_count' => $completedOrders,
                'avg_order' => $avgOrder,
            ],
        ];
    }

    /**
     * Helper query builder untuk Laporan Kunjungan & Durasi Waktu
     */
    private function queryVisits(Request $request): array
    {
        $query = VisitTask::with(['customer', 'sales.team'])
            ->latest('scheduled_at');

        // Search text
        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('task_code', 'like', "%{$search}%")
                    ->orWhere('purpose', 'like', "%{$search}%")
                    ->orWhere('visit_result', 'like', "%{$search}%")
                    ->orWhereHas('customer', fn ($c) => $c->where('name', 'like', "%{$search}%"))
                    ->orWhereHas('sales', fn ($s) => $s->where('name', 'like', "%{$search}%"));
            });
        }

        // Filter Tim
        if ($teamId = $request->get('team_id')) {
            $query->whereHas('sales', fn ($s) => $s->where('team_id', $teamId));
        }

        // Filter Sales
        if ($salesId = $request->get('sales_id')) {
            $query->where('sales_id', $salesId);
        }

        // Filter Status
        if ($status = $request->get('status')) {
            $query->where('status', $status);
        }

        // Filter Tanggal / Periode
        $period = $request->get('period');
        if ($period === 'today') {
            $query->whereDate('scheduled_at', today());
        } elseif ($period === 'this_week') {
            $query->whereBetween('scheduled_at', [now()->startOfWeek(), now()->endOfWeek()]);
        } elseif ($period === 'this_month') {
            $query->whereMonth('scheduled_at', now()->month)->whereYear('scheduled_at', now()->year);
        } else {
            if ($from = $request->get('date_from')) {
                $query->whereDate('scheduled_at', '>=', $from);
            }
            if ($to = $request->get('date_to')) {
                $query->whereDate('scheduled_at', '<=', $to);
            }
        }

        // Calculate statistics & duration
        $all = (clone $query)->get();
        $totalTasks = $all->count();
        $completedTasks = $all->where('status', 'SELESAI')->count();
        $totalMinutes = 0;
        $countWithDuration = 0;

        foreach ($all as $t) {
            if ($t->check_in_at && $t->check_out_at) {
                $mins = $t->check_out_at->diffInMinutes($t->check_in_at);
                $totalMinutes += $mins;
                $countWithDuration++;
            }
        }

        $avgMins = $countWithDuration > 0 ? round($totalMinutes / $countWithDuration) : 0;
        $totH = floor($totalMinutes / 60);
        $totM = $totalMinutes % 60;
        $totalDurationFormatted = $totH > 0 ? "{$totH}j {$totM}m" : "{$totalMinutes} Menit";

        return [
            'query' => $query,
            'stats' => [
                'total_tasks' => $totalTasks,
                'completed_tasks' => $completedTasks,
                'total_minutes' => $totalMinutes,
                'total_duration' => $totalDurationFormatted,
                'avg_minutes' => $avgMins,
                'completion_rate' => $totalTasks > 0 ? round(($completedTasks / $totalTasks) * 100) : 0,
            ],
        ];
    }

    /**
     * Membangun teks ringkasan filter untuk dicantumkan di header file export
     */
    private function buildFilterInfo(Request $request, string $type): string
    {
        $parts = [];

        // Periode
        $period = $request->get('period');
        if ($period === 'today') {
            $parts[] = 'Periode: Hari Ini ('.today()->format('d/m/Y').')';
        } elseif ($period === 'this_week') {
            $parts[] = 'Periode: Minggu Ini';
        } elseif ($period === 'this_month') {
            $parts[] = 'Periode: Bulan Ini ('.now()->translatedFormat('F Y').')';
        } elseif ($request->filled('date_from') || $request->filled('date_to')) {
            $from = $request->get('date_from', 'Awal');
            $to = $request->get('date_to', 'Akhir');
            $parts[] = "Periode: {$from} s/d {$to}";
        } else {
            $parts[] = 'Periode: Semua Waktu';
        }

        // Tim
        if ($teamId = $request->get('team_id')) {
            $team = Team::find($teamId);
            $parts[] = 'Tim: '.($team ? $team->name : 'ID '.$teamId);
        } else {
            $parts[] = 'Tim: Semua Tim';
        }

        // Sales
        if ($salesId = $request->get('sales_id')) {
            $sales = User::find($salesId);
            $parts[] = 'Sales: '.($sales ? $sales->name : 'ID '.$salesId);
        }

        // Status
        if ($status = $request->get('status')) {
            $parts[] = 'Status: '.str_replace('_', ' ', strtoupper($status));
        }

        // Search
        if ($search = $request->get('search')) {
            $parts[] = 'Kata Kunci: "'.$search.'"';
        }

        return implode(' | ', $parts);
    }
}
