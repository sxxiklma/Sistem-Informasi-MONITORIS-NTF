<?php

namespace App\Http\Controllers;

use App\Models\SalesOrder;
use App\Models\SalesTarget;
use App\Models\Team;
use App\Models\TrackingSession;
use App\Models\User;
use App\Models\VisitTask;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Spatie\Activitylog\Models\Activity;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        $user = Auth::user();
        abort_unless($user instanceof User, 401);

        $role = $user->roles->first()?->name ?? 'Sales Motoris';

        // 7-day trend calculation
        $trend = collect(range(6, 0))->map(function (int $days) use ($user, $role) {
            $date = today()->subDays($days);
            $query = VisitTask::whereDate('scheduled_at', $date);

            if ($role === 'Sales Motoris') {
                $query->where('sales_id', $user->id);
            } elseif ($role === 'Supervisor Sales') {
                $memberIds = User::where('team_id', $user->team_id)->pluck('id');
                $query->whereIn('sales_id', $memberIds);
            }

            return [
                'label' => $date->format('d/m'),
                'planned' => (clone $query)->count(),
                'completed' => (clone $query)->where('status', 'SELESAI')->count(),
            ];
        });

        // Common modals definitions
        $modals = [
            ['id' => 'dashboard-filter', 'title' => 'Filter Dashboard', 'submitLabel' => 'Terapkan filter', 'fields' => [
                ['label' => 'Tanggal mulai', 'name' => 'tanggal_mulai', 'type' => 'date'],
                ['label' => 'Tanggal akhir', 'name' => 'tanggal_akhir', 'type' => 'date'],
                ['label' => 'Tim sales', 'name' => 'tim_sales'],
                ['label' => 'Sales motoris', 'name' => 'sales_motoris'],
                ['label' => 'Status aktivitas', 'name' => 'status', 'type' => 'select', 'options' => ['Semua', 'MENUNGGU_RESPONS', 'DITERIMA', 'DALAM_PERJALANAN', 'SELESAI', 'DITOLAK']],
            ]],
            ['id' => 'activity-detail', 'title' => 'Detail Aktivitas', 'submitLabel' => 'Tutup', 'fields' => [
                ['label' => 'Nama sales', 'name' => 'nama_sales'],
                ['label' => 'Tanggal aktivitas', 'name' => 'tanggal_aktivitas', 'type' => 'date'],
                ['label' => 'Status', 'name' => 'status'],
            ]],
        ];

        // Specific metrics by role
        $metrics = match ($role) {
            'Admin' => $this->getAdminMetrics(),
            'Pimpinan' => $this->getPimpinanMetrics(),
            'Supervisor Sales' => $this->getSupervisorMetrics($user),
            default => $this->getSalesMetrics($user),
        };

        return view('dashboard', array_merge($metrics, compact('role', 'trend', 'modals')));
    }

    private function getAdminMetrics(): array
    {
        $activeSalesCount = User::role('Sales Motoris')->where('is_active', true)->count();
        $todayTasks = VisitTask::whereDate('scheduled_at', today())->count();
        $pendingTasks = VisitTask::where('status', 'MENUNGGU_RESPONS')->count();
        $acceptedTasks = VisitTask::where('status', 'DITERIMA')->count();
        $rejectedTasks = VisitTask::where('status', 'DITOLAK')->count();
        $completedTasks = VisitTask::where('status', 'SELESAI')->count();
        $activeTrackingCount = TrackingSession::where('status', 'active')->count();

        $monthRevenue = SalesOrder::whereMonth('order_date', now()->month)
            ->whereYear('order_date', now()->year)
            ->whereNotIn('status', ['draft', 'cancelled'])
            ->sum('total_amount');

        $monthOrdersCount = SalesOrder::whereMonth('order_date', now()->month)->count();

        $recentTasks = VisitTask::with(['sales', 'customer'])->latest('scheduled_at')->limit(6)->get();
        $recentAudits = Activity::with('causer')->latest()->limit(5)->get();

        return compact(
            'activeSalesCount', 'todayTasks', 'pendingTasks', 'acceptedTasks',
            'rejectedTasks', 'completedTasks', 'activeTrackingCount', 'monthRevenue',
            'monthOrdersCount', 'recentTasks', 'recentAudits'
        );
    }

    private function getPimpinanMetrics(): array
    {
        $totalTasks = VisitTask::count();
        $completedTasks = VisitTask::where('status', 'SELESAI')->count();
        $completionRate = $totalTasks > 0 ? round(($completedTasks / $totalTasks) * 100, 1) : 0;

        $monthRevenue = SalesOrder::whereMonth('order_date', now()->month)
            ->whereNotIn('status', ['draft', 'cancelled'])
            ->sum('total_amount');

        $activeTrackingCount = TrackingSession::where('status', 'active')->count();

        // Team productivity
        $teamProductivity = Team::withCount(['users as sales_count' => fn ($q) => $q->role('Sales Motoris')])
            ->get()
            ->map(function ($team) {
                $memberIds = User::where('team_id', $team->id)->pluck('id');
                $tasksCount = VisitTask::whereIn('sales_id', $memberIds)->count();
                $doneCount = VisitTask::whereIn('sales_id', $memberIds)->where('status', 'SELESAI')->count();
                $teamRevenue = SalesOrder::whereIn('user_id', $memberIds)->whereNotIn('status', ['draft', 'cancelled'])->sum('total_amount');

                return [
                    'team' => $team->name,
                    'sales' => $team->sales_count,
                    'tasks' => $tasksCount,
                    'completed' => $doneCount,
                    'rate' => $tasksCount > 0 ? round(($doneCount / $tasksCount) * 100) : 0,
                    'revenue' => $teamRevenue,
                ];
            });

        $recentTasks = VisitTask::with(['sales', 'customer'])->latest('scheduled_at')->limit(6)->get();

        return compact(
            'totalTasks', 'completedTasks', 'completionRate', 'monthRevenue',
            'activeTrackingCount', 'teamProductivity', 'recentTasks'
        );
    }

    private function getSupervisorMetrics(User $supervisor): array
    {
        $teamMemberIds = User::where('team_id', $supervisor->team_id)->pluck('id');
        $teamMembersCount = $teamMemberIds->count();

        $teamTodayTasks = VisitTask::whereIn('sales_id', $teamMemberIds)->whereDate('scheduled_at', today())->count();
        $teamPendingTasks = VisitTask::whereIn('sales_id', $teamMemberIds)->where('status', 'MENUNGGU_RESPONS')->count();
        $teamRejectedTasks = VisitTask::whereIn('sales_id', $teamMemberIds)->where('status', 'DITOLAK')->count();
        $teamMovingSales = VisitTask::whereIn('sales_id', $teamMemberIds)->where('status', 'DALAM_PERJALANAN')->count();
        $teamCheckedIn = VisitTask::whereIn('sales_id', $teamMemberIds)->whereIn('status', ['TIBA_DI_LOKASI', 'SEDANG_KUNJUNGAN'])->count();
        $teamCompleted = VisitTask::whereIn('sales_id', $teamMemberIds)->where('status', 'SELESAI')->count();

        $recentTasks = VisitTask::with(['sales', 'customer'])
            ->whereIn('sales_id', $teamMemberIds)
            ->latest('scheduled_at')->limit(6)->get();

        return compact(
            'teamMembersCount', 'teamTodayTasks', 'teamPendingTasks', 'teamRejectedTasks',
            'teamMovingSales', 'teamCheckedIn', 'teamCompleted', 'recentTasks'
        );
    }

    private function getSalesMetrics(User $sales): array
    {
        $myNewTasks = VisitTask::where('sales_id', $sales->id)->where('status', 'MENUNGGU_RESPONS')->count();
        $myTodayTasks = VisitTask::where('sales_id', $sales->id)->whereDate('scheduled_at', today())->count();
        $myAcceptedTasks = VisitTask::where('sales_id', $sales->id)->where('status', 'DITERIMA')->count();
        $myCompletedTasks = VisitTask::where('sales_id', $sales->id)->where('status', 'SELESAI')->count();

        $myRevenue = SalesOrder::where('user_id', $sales->id)
            ->whereMonth('order_date', now()->month)
            ->whereNotIn('status', ['draft', 'cancelled'])
            ->sum('total_amount');

        $myTarget = SalesTarget::where('user_id', $sales->id)
            ->whereDate('period_start', '<=', today())
            ->whereDate('period_end', '>=', today())
            ->first();

        $hasActiveTracking = $sales->activeTrackingSession()->exists();
        $recentTasks = VisitTask::with(['customer'])->where('sales_id', $sales->id)->latest('scheduled_at')->limit(6)->get();

        return compact(
            'myNewTasks', 'myTodayTasks', 'myAcceptedTasks', 'myCompletedTasks',
            'myRevenue', 'myTarget', 'hasActiveTracking', 'recentTasks'
        );
    }
}
