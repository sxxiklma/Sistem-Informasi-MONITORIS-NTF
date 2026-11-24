<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\VisitTask;
use App\Models\VisitTaskHistory;
use App\Models\VisitTaskResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    /**
     * List tasks for the authenticated sales motoris
     */
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();

        $query = VisitTask::with(['customer', 'sales.team']);

        // Role-based visibility
        if ($user->hasRole(['Admin', 'Pimpinan'])) {
            // Admin and Pimpinan can see all tasks across the company
        } elseif ($user->hasRole('Supervisor Sales')) {
            if ($user->team_id) {
                $salesIds = User::where('team_id', $user->team_id)->pluck('id');
                $query->whereIn('sales_id', $salesIds);
            }
        } else {
            // Sales Motoris only sees their own assigned tasks
            $query->where('sales_id', $user->id);
        }

        $query->latest('scheduled_at');

        // Optional filter
        if ($filter = $request->get('filter')) {
            if ($filter === 'today') {
                $query->whereDate('scheduled_at', today());
            }
        }

        if ($status = $request->get('status')) {
            $query->where('status', $status);
        }

        $tasks = $query->get()->map(function ($task) {
            $durationStr = '-';
            if ($task->check_in_at && $task->check_out_at) {
                $mins = abs((int) $task->check_out_at->diffInMinutes($task->check_in_at));
                $h = floor($mins / 60);
                $m = $mins % 60;
                $durationStr = $h > 0 ? "{$h}j {$m}m" : "{$mins} Menit";
            } elseif ($task->check_in_at) {
                $mins = abs((int) now()->diffInMinutes($task->check_in_at));
                $durationStr = "{$mins}m (Sedang Kunjungan)";
            }

            return [
                'id' => $task->id,
                'task_code' => $task->task_code,
                'status' => $task->status,
                'priority' => $task->priority,
                'purpose' => $task->purpose,
                'scheduled_at' => $task->scheduled_at ? $task->scheduled_at->format('d M Y, H:i') : null,
                'scheduled_date' => $task->scheduled_at ? $task->scheduled_at->format('d M Y') : null,
                'scheduled_time' => $task->scheduled_at ? $task->scheduled_at->format('H:i') : null,
                'check_in_at' => $task->check_in_at ? $task->check_in_at->format('H:i') : null,
                'check_out_at' => $task->check_out_at ? $task->check_out_at->format('H:i') : null,
                'duration' => $durationStr,
                'sales' => [
                    'id' => $task->sales?->id,
                    'name' => $task->sales?->name ?? 'Sales Motoris',
                    'team' => $task->sales?->team?->name ?? 'Tanpa Tim',
                ],
                'customer' => [
                    'id' => $task->customer?->id,
                    'name' => $task->customer?->name ?? 'Outlet',
                    'address' => $task->customer?->address ?? '-',
                    'phone' => $task->customer?->phone ?? '-',
                    'latitude' => $task->customer?->latitude,
                    'longitude' => $task->customer?->longitude,
                ],
                'documentation_url' => $task->documentation_image ? asset('storage/'.$task->documentation_image) : null,
            ];
        });

        // Summary count based on role's task scope
        $summaryQuery = VisitTask::query();
        if ($user->hasRole(['Admin', 'Pimpinan'])) {
            // All tasks
        } elseif ($user->hasRole('Supervisor Sales')) {
            if ($user->team_id) {
                $salesIds = User::where('team_id', $user->team_id)->pluck('id');
                $summaryQuery->whereIn('sales_id', $salesIds);
            }
        } else {
            $summaryQuery->where('sales_id', $user->id);
        }

        $summary = [
            'total' => (clone $summaryQuery)->count(),
            'pending' => (clone $summaryQuery)->where('status', 'MENUNGGU_RESPONS')->count(),
            'accepted' => (clone $summaryQuery)->whereIn('status', ['DITERIMA', 'DALAM_PERJALANAN', 'SEDANG_KUNJUNGAN'])->count(),
            'completed' => (clone $summaryQuery)->where('status', 'SELESAI')->count(),
        ];

        return response()->json([
            'success' => true,
            'summary' => $summary,
            'tasks' => $tasks,
        ]);
    }

    /**
     * Store new task (Admin & Supervisor)
     */
    public function store(Request $request): JsonResponse
    {
        $user = $request->user();
        if (! $user->hasAnyRole(['Admin', 'Supervisor Sales'])) {
            return response()->json(['success' => false, 'message' => 'Hanya Admin atau Supervisor yang dapat membuat tugas.'], 403);
        }

        $validated = $request->validate([
            'sales_id' => ['required', 'exists:users,id'],
            'customer_id' => ['required', 'exists:customers,id'],
            'scheduled_at' => ['required'],
            'purpose' => ['nullable', 'string', 'max:500'],
            'target_result' => ['nullable', 'string', 'max:500'],
            'instructions' => ['nullable', 'string', 'max:1000'],
            'priority' => ['required', 'in:rendah,normal,tinggi,mendesak'],
        ]);

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
            'notes' => 'Tugas dibuat via Mobile oleh '.$user->name,
            'changed_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => "Tugas {$task->task_code} berhasil dibuat dan ditugaskan.",
            'task' => $task->load(['customer', 'sales']),
        ]);
    }

    /**
     * Show single task detail
     */
    public function show(Request $request, VisitTask $task): JsonResponse
    {
        $user = $request->user();
        if ($task->sales_id !== $user->id && ! $user->hasRole(['Admin', 'Supervisor Sales', 'Pimpinan'])) {
            return response()->json(['success' => false, 'message' => 'Akses ditolak.'], 403);
        }

        $task->load('customer');

        return response()->json([
            'success' => true,
            'task' => [
                'id' => $task->id,
                'task_code' => $task->task_code,
                'status' => $task->status,
                'priority' => $task->priority,
                'purpose' => $task->purpose,
                'target_result' => $task->target_result,
                'instructions' => $task->instructions,
                'scheduled_at' => $task->scheduled_at ? $task->scheduled_at->format('d M Y, H:i') : null,
                'check_in_at' => $task->check_in_at ? $task->check_in_at->format('H:i') : null,
                'check_out_at' => $task->check_out_at ? $task->check_out_at->format('H:i') : null,
                'customer' => [
                    'id' => $task->customer?->id,
                    'name' => $task->customer?->name ?? 'Outlet',
                    'address' => $task->customer?->address ?? '-',
                    'phone' => $task->customer?->phone ?? '-',
                    'latitude' => $task->customer?->latitude,
                    'longitude' => $task->customer?->longitude,
                ],
                'visit_result' => $task->visit_result,
                'visit_notes' => $task->visit_notes,
                'documentation_url' => $task->documentation_image ? asset('storage/'.$task->documentation_image) : null,
            ],
        ]);
    }

    /**
     * Accept or reject task
     */
    public function respond(Request $request, VisitTask $task): JsonResponse
    {
        $user = $request->user();
        if ($task->sales_id !== $user->id) {
            return response()->json(['success' => false, 'message' => 'Tugas ini bukan untuk Anda.'], 403);
        }

        $request->validate([
            'response' => 'required|in:DITERIMA,DITOLAK',
            'reason' => 'nullable|string',
        ]);

        $newStatus = $request->response;

        $task->update([
            'status' => $newStatus,
            'responded_at' => now(),
        ]);

        VisitTaskResponse::create([
            'task_id' => $task->id,
            'user_id' => $user->id,
            'response' => $newStatus,
            'reason' => $request->reason,
            'responded_at' => now(),
        ]);

        VisitTaskHistory::create([
            'task_id' => $task->id,
            'changed_by' => $user->id,
            'from_status' => 'MENUNGGU_RESPONS',
            'to_status' => $newStatus,
            'notes' => $request->reason ?: 'Respons tugas oleh sales via mobile',
            'changed_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => $newStatus === 'DITERIMA' ? 'Tugas berhasil diterima!' : 'Tugas ditolak.',
            'task' => $task->fresh(),
        ]);
    }

    /**
     * Check-in at customer outlet
     */
    public function checkIn(Request $request, VisitTask $task): JsonResponse
    {
        $user = $request->user();
        if ($task->sales_id !== $user->id) {
            return response()->json(['success' => false, 'message' => 'Tugas ini bukan untuk Anda.'], 403);
        }

        $request->validate([
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
        ]);

        // Calculate distance if customer coordinates exist
        $dist = null;
        if ($task->customer && $task->customer->latitude && $task->customer->longitude) {
            $theta = $request->longitude - $task->customer->longitude;
            $dist = sin(deg2rad($request->latitude)) * sin(deg2rad($task->customer->latitude)) +
                    cos(deg2rad($request->latitude)) * cos(deg2rad($task->customer->latitude)) * cos(deg2rad($theta));
            $dist = acos(min(max($dist, -1.0), 1.0));
            $dist = rad2deg($dist);
            $dist = $dist * 60 * 1.1515 * 1.609344 * 1000; // in meters
        }

        $fromStatus = $task->status;
        $task->update([
            'status' => 'SEDANG_KUNJUNGAN',
            'check_in_at' => now(),
            'check_in_latitude' => $request->latitude,
            'check_in_longitude' => $request->longitude,
            'check_in_distance_meters' => $dist,
        ]);

        VisitTaskHistory::create([
            'task_id' => $task->id,
            'changed_by' => $user->id,
            'from_status' => $fromStatus,
            'to_status' => 'SEDANG_KUNJUNGAN',
            'notes' => 'Sales check-in tiba di lokasi via mobile',
            'changed_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Berhasil check-in di outlet!',
            'distance' => $dist ? round($dist).' meter' : null,
            'task' => $task->fresh(),
        ]);
    }

    /**
     * Complete visit with documentation photo
     */
    public function complete(Request $request, VisitTask $task): JsonResponse
    {
        $user = $request->user();
        if ($task->sales_id !== $user->id) {
            return response()->json(['success' => false, 'message' => 'Tugas ini bukan untuk Anda.'], 403);
        }

        $request->validate([
            'visit_result' => 'required|string',
            'visit_notes' => 'nullable|string',
            'image' => 'nullable|image|max:10240', // max 10MB
        ]);

        $imagePath = $task->documentation_image;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('visit-docs', 'public');
        }

        $fromStatus = $task->status;
        $task->update([
            'status' => 'SELESAI',
            'check_out_at' => now(),
            'visit_result' => $request->visit_result,
            'visit_notes' => $request->visit_notes,
            'documentation_image' => $imagePath,
        ]);

        VisitTaskHistory::create([
            'task_id' => $task->id,
            'changed_by' => $user->id,
            'from_status' => $fromStatus,
            'to_status' => 'SELESAI',
            'notes' => 'Kunjungan selesai dilaporkan via mobile',
            'changed_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Kunjungan berhasil diselesaikan dan laporan tersimpan!',
            'task' => $task->fresh(),
        ]);
    }
}
