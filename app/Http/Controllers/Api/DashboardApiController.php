<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Product;
use App\Models\SalesOrder;
use App\Models\SalesTarget;
use App\Models\Team;
use App\Models\TrackingSession;
use App\Models\User;
use App\Models\VisitTask;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DashboardApiController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();
        $role = $user->roles->first()?->name ?? 'Sales Motoris';

        // 7-day trend (Single optimized query)
        $startDate = today()->subDays(6)->startOfDay();
        $endDate = today()->endOfDay();

        $trendQuery = VisitTask::whereBetween('scheduled_at', [$startDate, $endDate]);
        if ($role === 'Sales Motoris') {
            $trendQuery->where('sales_id', $user->id);
        } elseif ($role === 'Supervisor Sales' && $user->team_id) {
            $memberIds = User::where('team_id', $user->team_id)->pluck('id');
            $trendQuery->whereIn('sales_id', $memberIds);
        }

        $tasksInRange = $trendQuery->selectRaw('DATE(scheduled_at) as task_date, status')->get();

        $trend = collect(range(6, 0))->map(function (int $days) use ($tasksInRange) {
            $date = today()->subDays($days);
            $dateStr = $date->toDateString();
            $tasksForDay = $tasksInRange->where('task_date', $dateStr);

            return [
                'date' => $date->format('d/m'),
                'planned' => $tasksForDay->count(),
                'completed' => $tasksForDay->where('status', 'SELESAI')->count(),
            ];
        });

        // Common metrics & counts
        $totalCustomers = Customer::where('is_active', true)->count();
        $totalProducts = Product::where('is_active', true)->count();
        $activeTrackingCount = TrackingSession::where('status', 'active')->count();

        // Role-specific metrics
        $metrics = match ($role) {
            'Admin' => $this->getAdminMetrics(),
            'Pimpinan' => $this->getPimpinanMetrics(),
            'Supervisor Sales' => $this->getSupervisorMetrics($user),
            default => $this->getSalesMetrics($user),
        };

        // Recent tasks
        $taskQuery = VisitTask::with(['sales.team', 'customer'])->latest('scheduled_at');
        if ($role === 'Sales Motoris') {
            $taskQuery->where('sales_id', $user->id);
        } elseif ($role === 'Supervisor Sales') {
            $memberIds = User::where('team_id', $user->team_id)->pluck('id');
            $taskQuery->whereIn('sales_id', $memberIds);
        }
        $recentTasks = $taskQuery->limit(5)->get()->map(fn ($t) => [
            'id' => $t->id,
            'task_code' => $t->task_code,
            'status' => $t->status,
            'priority' => $t->priority,
            'scheduled_at' => $t->scheduled_at?->format('d M, H:i'),
            'sales_name' => $t->sales?->name ?? 'Sales',
            'customer_name' => $t->customer?->name ?? 'Outlet',
            'customer_address' => $t->customer?->address ?? '-',
        ]);

        return response()->json([
            'success' => true,
            'role' => $role,
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'team' => $user->team?->name ?? 'Tanpa Tim',
                'role' => $role,
            ],
            'common' => [
                'total_customers' => $totalCustomers,
                'total_products' => $totalProducts,
                'active_trackings' => $activeTrackingCount,
            ],
            'metrics' => $metrics,
            'trend' => $trend,
            'recent_tasks' => $recentTasks,
        ]);
    }

    private function getAdminMetrics(): array
    {
        $monthRevenue = SalesOrder::whereMonth('order_date', now()->month)
            ->whereYear('order_date', now()->year)
            ->whereNotIn('status', ['draft', 'cancelled'])
            ->sum('total_amount');

        return [
            'active_sales' => User::role('Sales Motoris')->where('is_active', true)->count(),
            'today_tasks' => VisitTask::whereDate('scheduled_at', today())->count(),
            'pending_tasks' => VisitTask::where('status', 'MENUNGGU_RESPONS')->count(),
            'completed_tasks' => VisitTask::where('status', 'SELESAI')->count(),
            'month_revenue' => (float) $monthRevenue,
            'total_orders' => SalesOrder::whereMonth('order_date', now()->month)->count(),
        ];
    }

    private function getPimpinanMetrics(): array
    {
        $totalTasks = VisitTask::count();
        $completedTasks = VisitTask::where('status', 'SELESAI')->count();
        $completionRate = $totalTasks > 0 ? round(($completedTasks / $totalTasks) * 100, 1) : 0;

        $monthRevenue = SalesOrder::whereMonth('order_date', now()->month)
            ->whereNotIn('status', ['draft', 'cancelled'])
            ->sum('total_amount');

        $teams = Team::withCount(['users as sales_count' => fn ($q) => $q->role('Sales Motoris')])
            ->get()
            ->map(function ($team) {
                $memberIds = User::where('team_id', $team->id)->pluck('id');
                $tasksCount = VisitTask::whereIn('sales_id', $memberIds)->count();
                $doneCount = VisitTask::whereIn('sales_id', $memberIds)->where('status', 'SELESAI')->count();

                return [
                    'team' => $team->name,
                    'sales' => $team->sales_count,
                    'tasks' => $tasksCount,
                    'completed' => $doneCount,
                    'rate' => $tasksCount > 0 ? round(($doneCount / $tasksCount) * 100) : 0,
                ];
            });

        return [
            'total_tasks' => $totalTasks,
            'completed_tasks' => $completedTasks,
            'completion_rate' => $completionRate,
            'month_revenue' => (float) $monthRevenue,
            'teams' => $teams,
        ];
    }

    private function getSupervisorMetrics(User $supervisor): array
    {
        $teamMemberIds = User::where('team_id', $supervisor->team_id)->pluck('id');

        return [
            'team_members' => $teamMemberIds->count(),
            'team_today_tasks' => VisitTask::whereIn('sales_id', $teamMemberIds)->whereDate('scheduled_at', today())->count(),
            'team_pending' => VisitTask::whereIn('sales_id', $teamMemberIds)->where('status', 'MENUNGGU_RESPONS')->count(),
            'team_completed' => VisitTask::whereIn('sales_id', $teamMemberIds)->where('status', 'SELESAI')->count(),
            'team_revenue' => (float) SalesOrder::whereIn('user_id', $teamMemberIds)->whereMonth('order_date', now()->month)->sum('total_amount'),
        ];
    }

    private function getSalesMetrics(User $sales): array
    {
        $myRevenue = SalesOrder::where('user_id', $sales->id)
            ->whereMonth('order_date', now()->month)
            ->whereNotIn('status', ['draft', 'cancelled'])
            ->sum('total_amount');

        $myTarget = SalesTarget::where('user_id', $sales->id)
            ->whereDate('period_start', '<=', today())
            ->whereDate('period_end', '>=', today())
            ->first();

        return [
            'my_new_tasks' => VisitTask::where('sales_id', $sales->id)->where('status', 'MENUNGGU_RESPONS')->count(),
            'my_today_tasks' => VisitTask::where('sales_id', $sales->id)->whereDate('scheduled_at', today())->count(),
            'my_completed' => VisitTask::where('sales_id', $sales->id)->where('status', 'SELESAI')->count(),
            'my_revenue' => (float) $myRevenue,
            'target_amount' => (float) ($myTarget?->target_amount ?? 15000000),
            'target_percent' => $myTarget && $myTarget->target_amount > 0 ? min(100, round(($myRevenue / $myTarget->target_amount) * 100)) : round(min(100, ($myRevenue / 15000000) * 100)),
        ];
    }
}
