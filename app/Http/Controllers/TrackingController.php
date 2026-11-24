<?php

namespace App\Http\Controllers;

use App\Models\SalesLocation;
use App\Models\Team;
use App\Models\TrackingSession;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class TrackingController extends Controller
{
    /**
     * Live tracking map page (Admin, Pimpinan, Supervisor).
     */
    public function index(): View
    {
        $user = Auth::user();
        $teams = Team::where('is_active', true)->orderBy('name')->get();

        // Sales visible to current user
        if ($user->hasRole('Supervisor Sales')) {
            $salesUsers = User::role('Sales Motoris')
                ->where('team_id', $user->team_id)
                ->with('activeTrackingSession.latestLocation')
                ->orderBy('name')->get();
        } elseif ($user->hasAnyRole(['Admin', 'Pimpinan'])) {
            $salesUsers = User::role('Sales Motoris')
                ->with('activeTrackingSession.latestLocation')
                ->orderBy('name')->get();
        } else {
            $salesUsers = collect();
        }

        return view('tracking.index', compact('teams', 'salesUsers'));
    }

    /**
     * API: Get live positions of all visible sales (polling endpoint).
     */
    public function livePositions(Request $request): JsonResponse
    {
        $user = Auth::user();

        $query = User::role('Sales Motoris')
            ->with(['activeTrackingSession.latestLocation', 'team'])
            ->whereHas('activeTrackingSession');

        // RBAC restriction
        if ($user->hasRole('Supervisor Sales')) {
            $query->where('team_id', $user->team_id);
        } elseif ($user->hasRole('Sales Motoris')) {
            $query->where('id', $user->id);
        }
        // Admin & Pimpinan see all

        if ($request->filled('team_id')) {
            $query->where('team_id', $request->team_id);
        }

        $result = $query->get()->map(function (User $sales) {
            $session = $sales->activeTrackingSession;
            $loc = $session?->latestLocation;

            if (! $loc) {
                return null;
            }

            $minutesAgo = $loc->received_at
                ? now()->diffInMinutes($loc->received_at)
                : 999;

            $markerColor = match (true) {
                $minutesAgo <= 5 => 'green',
                $minutesAgo <= 15 => 'blue',
                $minutesAgo <= 30 => 'orange',
                default => 'gray',
            };

            return [
                'sales_id' => $sales->id,
                'name' => $sales->name,
                'team' => $sales->team?->name ?? '-',
                'latitude' => floatval($loc->latitude),
                'longitude' => floatval($loc->longitude),
                'accuracy' => $loc->accuracy ? floatval($loc->accuracy) : null,
                'recorded_at' => $loc->recorded_at?->toIso8601String(),
                'received_at' => $loc->received_at?->toIso8601String(),
                'minutes_ago' => $minutesAgo,
                'color' => $markerColor,
                'session_id' => $session->id,
                'session_started' => $session->started_at?->toIso8601String(),
            ];
        })->filter()->values();

        return response()->json($result);
    }

    /**
     * API: Get journey history for a specific session.
     */
    public function journeyHistory(Request $request, int $sessionId): JsonResponse
    {
        $user = Auth::user();
        $session = TrackingSession::with('sales.team')->findOrFail($sessionId);

        // RBAC validation at backend
        if ($user->hasRole('Sales Motoris')) {
            abort_unless($session->sales_id === $user->id, 403);
        } elseif ($user->hasRole('Supervisor Sales')) {
            abort_unless($session->sales->team_id === $user->team_id, 403);
        }

        $points = SalesLocation::where('session_id', $sessionId)
            ->orderBy('recorded_at')
            ->get(['latitude', 'longitude', 'accuracy', 'speed', 'recorded_at']);

        return response()->json([
            'session' => [
                'id' => $session->id,
                'sales' => $session->sales->name,
                'team' => $session->sales->team?->name,
                'started_at' => $session->started_at?->toIso8601String(),
                'ended_at' => $session->ended_at?->toIso8601String(),
                'status' => $session->status,
                'points_count' => $session->total_points,
            ],
            'path' => $points->map(fn ($p) => [
                'lat' => floatval($p->latitude),
                'lng' => floatval($p->longitude),
                'accuracy' => $p->accuracy ? floatval($p->accuracy) : null,
                'speed' => $p->speed ? floatval($p->speed) : null,
                'recorded_at' => $p->recorded_at?->toIso8601String(),
            ]),
        ]);
    }

    /**
     * Start tracking session (Sales Motoris only).
     */
    public function startSession(Request $request): JsonResponse
    {
        $user = Auth::user();
        abort_unless($user->hasRole('Sales Motoris'), 403, 'Hanya Sales Motoris yang dapat memulai tracking.');

        // End any existing active session
        TrackingSession::where('sales_id', $user->id)
            ->where('status', 'active')
            ->update(['status' => 'ended', 'ended_at' => now()]);

        $session = TrackingSession::create([
            'sales_id' => $user->id,
            'task_id' => $request->input('task_id'),
            'started_at' => now(),
            'status' => 'active',
        ]);

        return response()->json([
            'success' => true,
            'session_id' => $session->id,
            'message' => 'Sesi tracking GPS dimulai.',
        ]);
    }

    /**
     * Send a GPS location point (Sales Motoris during active session).
     */
    public function sendLocation(Request $request): JsonResponse
    {
        $user = Auth::user();
        abort_unless($user->hasRole('Sales Motoris'), 403);

        $validated = $request->validate([
            'session_id' => ['nullable', 'exists:tracking_sessions,id'],
            'task_id' => ['nullable', 'exists:visit_tasks,id'],
            'latitude' => ['required', 'numeric', 'between:-90,90'],
            'longitude' => ['required', 'numeric', 'between:-180,180'],
            'accuracy' => ['nullable', 'numeric', 'min:0', 'max:10000'],
            'speed' => ['nullable', 'numeric', 'min:0'],
            'heading' => ['nullable', 'numeric', 'min:0', 'max:360'],
            'recorded_at' => ['nullable', 'date'],
        ]);

        $session = null;
        if (! empty($validated['session_id'])) {
            $session = TrackingSession::where('id', $validated['session_id'])
                ->where('sales_id', $user->id)
                ->first();
        }

        if (! $session) {
            $session = TrackingSession::where('sales_id', $user->id)
                ->where('status', 'active')
                ->latest('id')
                ->first();
        }

        if (! $session) {
            $session = TrackingSession::create([
                'sales_id' => $user->id,
                'task_id' => $validated['task_id'] ?? null,
                'started_at' => now(),
                'status' => 'active',
            ]);
        }

        $lastLoc = $session->latestLocation;

        $location = SalesLocation::create([
            'sales_id' => $user->id,
            'session_id' => $session->id,
            'latitude' => $validated['latitude'],
            'longitude' => $validated['longitude'],
            'accuracy' => $validated['accuracy'] ?? null,
            'speed' => $validated['speed'] ?? null,
            'heading' => $validated['heading'] ?? null,
            'recorded_at' => $validated['recorded_at'] ?? now(),
            'received_at' => now(),
        ]);

        // Increment session counter
        $session->increment('total_points');

        if ($lastLoc) {
            $dist = $this->haversine(
                floatval($lastLoc->latitude), floatval($lastLoc->longitude),
                floatval($validated['latitude']), floatval($validated['longitude'])
            );
            $session->increment('total_distance_meters', $dist);
        }

        return response()->json([
            'success' => true,
            'location_id' => $location->id,
            'session_id' => $session->id,
        ]);
    }

    /**
     * End tracking session (Sales Motoris only).
     */
    public function endSession(Request $request): JsonResponse
    {
        $user = Auth::user();
        abort_unless($user->hasRole('Sales Motoris'), 403);

        $validated = $request->validate([
            'session_id' => ['required', 'exists:tracking_sessions,id'],
        ]);

        $session = TrackingSession::where('id', $validated['session_id'])
            ->where('sales_id', $user->id)
            ->where('status', 'active')
            ->firstOrFail();

        $session->update([
            'status' => 'ended',
            'ended_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => "Sesi tracking selesai. Total {$session->total_points} titik lokasi dicatat.",
        ]);
    }

    /**
     * Get current user's active session status.
     */
    public function sessionStatus(): JsonResponse
    {
        $user = Auth::user();
        $session = $user->activeTrackingSession()->first();

        if (! $session) {
            return response()->json(['active' => false]);
        }

        return response()->json([
            'active' => true,
            'session_id' => $session->id,
            'started_at' => $session->started_at?->toIso8601String(),
            'points' => $session->total_points,
        ]);
    }

    private function haversine(float $lat1, float $lon1, float $lat2, float $lon2): float
    {
        $earthRadius = 6371000; // meters
        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);
        $a = sin($dLat / 2) * sin($dLat / 2) +
             cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
             sin($dLon / 2) * sin($dLon / 2);
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $earthRadius * $c;
    }
}
