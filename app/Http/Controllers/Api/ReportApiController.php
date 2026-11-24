<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SalesOrder;
use App\Models\VisitTask;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ReportApiController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $type = $request->get('type', 'sales'); // 'sales' or 'visits'
        $startDate = $request->get('start_date', now()->startOfMonth()->toDateString());
        $endDate = $request->get('end_date', now()->toDateString());

        if ($type === 'visits') {
            $query = VisitTask::with(['sales.team', 'customer'])
                ->whereDate('scheduled_at', '>=', $startDate)
                ->whereDate('scheduled_at', '<=', $endDate)
                ->latest('scheduled_at');

            if ($search = $request->get('search')) {
                $query->where(function ($q) use ($search) {
                    $q->where('task_code', 'like', "%{$search}%")
                        ->orWhereHas('sales', fn ($s) => $s->where('name', 'like', "%{$search}%"))
                        ->orWhereHas('customer', fn ($c) => $c->where('name', 'like', "%{$search}%"));
                });
            }

            $allTasks = $query->get();
            $completedTasks = $allTasks->where('status', 'SELESAI');

            // Calculate duration
            $totalMinutes = 0;
            $countWithDuration = 0;
            foreach ($completedTasks as $t) {
                if ($t->check_in_at && $t->check_out_at) {
                    $mins = abs((int) $t->check_out_at->diffInMinutes($t->check_in_at));
                    $totalMinutes += $mins;
                    $countWithDuration++;
                }
            }

            $avgMins = $countWithDuration > 0 ? round($totalMinutes / $countWithDuration) : 0;
            $h = floor($avgMins / 60);
            $m = $avgMins % 60;
            $avgDurationStr = $h > 0 ? "{$h}j {$m}m" : "{$avgMins} Menit";

            $stats = [
                'total_tasks' => $allTasks->count(),
                'completed' => $completedTasks->count(),
                'pending' => $allTasks->where('status', 'MENUNGGU_RESPONS')->count(),
                'avg_duration' => $avgDurationStr,
                'total_minutes' => $totalMinutes,
            ];

            $items = $allTasks->map(function ($t) {
                $dur = '-';
                if ($t->check_in_at && $t->check_out_at) {
                    $mins = abs((int) $t->check_out_at->diffInMinutes($t->check_in_at));
                    $h = floor($mins / 60);
                    $m = $mins % 60;
                    $dur = $h > 0 ? "{$h}j {$m}m" : "{$mins} Menit";
                }

                return [
                    'id' => $t->id,
                    'code' => $t->task_code,
                    'date' => $t->scheduled_at?->format('d/m/Y H:i') ?? '-',
                    'sales_name' => $t->sales?->name ?? 'Sales',
                    'team_name' => $t->sales?->team?->name ?? 'Semua',
                    'outlet_name' => $t->customer?->name ?? 'Outlet',
                    'status' => $t->status,
                    'check_in' => $t->check_in_at?->format('H:i') ?? '-',
                    'check_out' => $t->check_out_at?->format('H:i') ?? '-',
                    'duration' => $dur,
                ];
            });
        } else {
            // Sales Report
            $query = SalesOrder::with(['customer', 'user.team'])
                ->whereDate('order_date', '>=', $startDate)
                ->whereDate('order_date', '<=', $endDate)
                ->latest('order_date');

            if ($search = $request->get('search')) {
                $query->where(function ($q) use ($search) {
                    $q->where('order_number', 'like', "%{$search}%")
                        ->orWhereHas('user', fn ($u) => $u->where('name', 'like', "%{$search}%"))
                        ->orWhereHas('customer', fn ($c) => $c->where('name', 'like', "%{$search}%"));
                });
            }

            $allOrders = $query->get();
            $totalRevenue = $allOrders->sum('total_amount');
            $avgOrder = $allOrders->count() > 0 ? round($totalRevenue / $allOrders->count()) : 0;

            $stats = [
                'total_orders' => $allOrders->count(),
                'total_revenue' => (float) $totalRevenue,
                'formatted_revenue' => 'Rp '.number_format($totalRevenue, 0, ',', '.'),
                'avg_order' => 'Rp '.number_format($avgOrder, 0, ',', '.'),
            ];

            $items = $allOrders->map(fn ($o) => [
                'id' => $o->id,
                'code' => $o->order_number,
                'date' => $o->order_date?->format('d/m/Y') ?? '-',
                'sales_name' => $o->user?->name ?? 'Sales',
                'team_name' => $o->user?->team?->name ?? 'Umum',
                'outlet_name' => $o->customer?->name ?? 'Outlet',
                'status' => $o->status,
                'total' => (float) $o->total_amount,
                'formatted_total' => 'Rp '.number_format($o->total_amount, 0, ',', '.'),
            ]);
        }

        $nowStr = now()->timezone('Asia/Jakarta')->translatedFormat('d F Y - H:i').' WIB';

        return response()->json([
            'success' => true,
            'type' => $type,
            'generated_at' => $nowStr,
            'stats' => $stats,
            'items' => $items,
            'export_urls' => [
                'excel_sales' => url('/reports/export/excel?type=sales'),
                'pdf_sales' => url('/reports/export/pdf?type=sales'),
                'excel_visits' => url('/reports/export/excel?type=visits'),
                'pdf_visits' => url('/reports/export/pdf?type=visits'),
            ],
        ]);
    }
}
