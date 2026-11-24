<?php

namespace App\Http\Controllers;

use App\Models\AppNotification;
use App\Models\Customer;
use App\Models\Team;
use App\Models\User;
use App\Models\Visit;
use App\Models\VisitTask;
use App\Models\VisitTaskHistory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class VisitController extends Controller
{
    /**
     * Unified Rute & Live Tracking Kunjungan Page.
     */
    public function index(Request $request): View
    {
        $user = Auth::user();

        // 1. Teams & Customers
        $teams = Team::where('is_active', true)->orderBy('name')->get();
        $customers = Customer::where('is_active', true)->orderBy('name')->get();

        // 2. Sales Users visible according to RBAC
        if ($user->hasRole('Supervisor Sales')) {
            $salesUsers = User::role('Sales Motoris')
                ->where('team_id', $user->team_id)
                ->with(['team', 'activeTrackingSession.latestLocation'])
                ->orderBy('name')->get();
        } else {
            $salesUsers = User::role('Sales Motoris')
                ->with(['team', 'activeTrackingSession.latestLocation'])
                ->orderBy('name')->get();
        }

        // 3. Query VisitTasks as the primary visiting dataset (automatically populated)
        $query = VisitTask::with([
            'customer',
            'sales.team',
            'sales.activeTrackingSession.latestLocation',
            'trackingSession.locations',
            'histories',
            'supervisor',
            'creator',
        ]);

        // RBAC filtering
        if ($user->hasRole('Sales Motoris')) {
            $query->where('sales_id', $user->id);
        } elseif ($user->hasRole('Supervisor Sales')) {
            $teamSalesIds = $salesUsers->pluck('id');
            $query->whereIn('sales_id', $teamSalesIds);
        }
        // Admin and Pimpinan see all

        // Request filters
        if ($request->filled('team_id')) {
            $query->whereHas('sales', fn ($q) => $q->where('team_id', $request->team_id));
        }
        if ($request->filled('sales_id')) {
            $query->where('sales_id', $request->sales_id);
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('priority')) {
            $query->where('priority', $request->priority);
        }
        if ($request->filled('search')) {
            $s = trim($request->search);
            $query->where(function ($q) use ($s) {
                $q->where('task_code', 'like', "%{$s}%")
                    ->orWhere('purpose', 'like', "%{$s}%")
                    ->orWhereHas('customer', fn ($cq) => $cq->where('name', 'like', "%{$s}%"))
                    ->orWhereHas('sales', fn ($sq) => $sq->where('name', 'like', "%{$s}%"));
            });
        }

        $visitTasks = $query->orderByDesc('scheduled_at')->paginate(15);

        return view('visits.index', compact(
            'visitTasks',
            'teams',
            'customers',
            'salesUsers'
        ));
    }

    public function create(): RedirectResponse
    {
        return to_route('visits.index', ['create' => 1]);
    }

    /**
     * Store new visit task from Rute & Navigasi Peta page.
     */
    public function store(Request $request): RedirectResponse
    {
        $user = Auth::user();
        abort_unless($user->can('kelola kunjungan'), 403);

        $validated = $request->validate([
            'sales_id' => ['required', 'exists:users,id'],
            'customer_id' => ['required', 'exists:customers,id'],
            'scheduled_at' => ['required', 'date'],
            'due_at' => ['nullable', 'date', 'after:scheduled_at'],
            'priority' => ['required', 'in:rendah,normal,tinggi,mendesak'],
            'purpose' => ['nullable', 'string', 'max:500'],
            'instructions' => ['nullable', 'string', 'max:1000'],
        ]);

        if ($user->hasRole('Supervisor Sales')) {
            $sales = User::findOrFail($validated['sales_id']);
            abort_unless($sales->team_id === $user->team_id, 403, 'Anda hanya dapat menugaskan sales dalam tim Anda.');
        }

        $task = VisitTask::create([
            ...$validated,
            'task_code' => VisitTask::generateCode(),
            'supervisor_id' => $user->hasRole('Supervisor Sales') ? $user->id : null,
            'created_by' => $user->id,
            'status' => 'MENUNGGU_RESPONS',
        ]);

        VisitTaskHistory::create([
            'task_id' => $task->id,
            'changed_by' => $user->id,
            'from_status' => null,
            'to_status' => 'MENUNGGU_RESPONS',
            'notes' => 'Tugas dibuat dari menu Rute & Navigasi Peta oleh '.$user->name,
            'changed_at' => now(),
        ]);

        // Send In-App notification to sales
        AppNotification::send(
            recipientId: $validated['sales_id'],
            type: 'task_assigned',
            title: 'Tugas Kunjungan Baru',
            message: "Anda mendapat tugas kunjungan ke {$task->customer->name} pada ".$task->scheduled_at->format('d/m/Y H:i'),
            senderId: $user->id,
            entityType: 'visit_tasks',
            entityId: $task->id,
            actionUrl: route('visits.index')
        );

        return to_route('visits.index')->with('status', "Rencana kunjungan {$task->task_code} berhasil dibuat dan otomatis terdaftar.");
    }

    /**
     * Update visit task.
     */
    public function update(Request $request, $id): RedirectResponse
    {
        $user = Auth::user();
        abort_unless($user->can('kelola kunjungan'), 403);

        $task = VisitTask::find($id) ?? Visit::findOrFail($id);

        $validated = $request->validate([
            'sales_id' => ['nullable', 'exists:users,id'],
            'customer_id' => ['required', 'exists:customers,id'],
            'scheduled_at' => ['required', 'date'],
            'priority' => ['nullable', 'in:rendah,normal,tinggi,mendesak'],
            'purpose' => ['nullable', 'string', 'max:500'],
            'instructions' => ['nullable', 'string', 'max:1000'],
        ]);

        $task->update($validated);

        return to_route('visits.index')->with('status', 'Data kunjungan berhasil diperbarui.');
    }

    /**
     * Delete or cancel visit task.
     */
    public function destroy(Request $request, $id): RedirectResponse
    {
        $user = Auth::user();
        abort_unless($user->can('kelola kunjungan'), 403);

        $task = VisitTask::find($id);
        if ($task) {
            $code = $task->task_code;

            // Also delete matching legacy visit so it never resurrects
            Visit::where('customer_id', $task->customer_id)
                ->where('user_id', $task->sales_id)
                ->delete();

            $task->delete();

            return to_route('visits.index')->with('status', "Kunjungan {$code} berhasil dihapus permanen.");
        }

        $visit = Visit::find($id);
        if ($visit) {
            $visit->delete();

            return to_route('visits.index')->with('status', 'Data kunjungan berhasil dihapus permanen.');
        }

        return to_route('visits.index')->with('status', 'Data kunjungan tidak ditemukan.');
    }

    /**
     * GPS Check-in.
     */
    public function checkIn(Request $request, $id): RedirectResponse
    {
        $user = Auth::user();
        $task = VisitTask::findOrFail($id);
        abort_unless($task->sales_id === $user->id || $user->hasAnyRole(['Admin', 'Supervisor Sales']), 403);

        $data = $request->validate([
            'latitude' => ['required', 'numeric', 'between:-90,90'],
            'longitude' => ['required', 'numeric', 'between:-180,180'],
        ]);

        $task->update([
            'status' => 'SEDANG_KUNJUNGAN',
            'check_in_at' => now(),
            'check_in_latitude' => $data['latitude'],
            'check_in_longitude' => $data['longitude'],
        ]);

        VisitTaskHistory::create([
            'task_id' => $task->id,
            'changed_by' => $user->id,
            'from_status' => $task->status,
            'to_status' => 'SEDANG_KUNJUNGAN',
            'notes' => 'Check-in GPS di lokasi outlet oleh '.$user->name,
            'changed_at' => now(),
        ]);

        return back()->with('status', 'Check-in GPS berhasil dicatat.');
    }

    /**
     * GPS Check-out.
     */
    public function checkOut(Request $request, $id): RedirectResponse
    {
        $user = Auth::user();
        $task = VisitTask::findOrFail($id);
        abort_unless($task->sales_id === $user->id || $user->hasAnyRole(['Admin', 'Supervisor Sales']), 403);

        $data = $request->validate([
            'latitude' => ['nullable', 'numeric', 'between:-90,90'],
            'longitude' => ['nullable', 'numeric', 'between:-180,180'],
            'outcome' => ['nullable', 'string', 'max:500'],
        ]);

        $task->update([
            'status' => 'SELESAI',
            'check_out_at' => now(),
            'check_out_latitude' => $data['latitude'] ?? null,
            'check_out_longitude' => $data['longitude'] ?? null,
            'visit_result' => $data['outcome'] ?? 'Kunjungan selesai',
        ]);

        VisitTaskHistory::create([
            'task_id' => $task->id,
            'changed_by' => $user->id,
            'from_status' => $task->status,
            'to_status' => 'SELESAI',
            'notes' => 'Kunjungan diselesaikan oleh '.$user->name,
            'changed_at' => now(),
        ]);

        return back()->with('status', 'Check-out berhasil dicatat. Kunjungan selesai.');
    }

    /**
     * API: Get detailed journey & tracking payload for a specific visit task.
     */
    public function journey(VisitTask $visitTask): JsonResponse
    {
        $visitTask->load([
            'customer',
            'sales.team',
            'sales.activeTrackingSession.latestLocation',
            'trackingSession.locations',
            'histories',
        ]);

        return response()->json($visitTask->getJourneyPayload());
    }
}
