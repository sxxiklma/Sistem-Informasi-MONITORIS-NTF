<?php

namespace App\Http\Controllers;

use App\Models\AppNotification;
use App\Models\Customer;
use App\Models\OrderPayment;
use App\Models\Product;
use App\Models\SalesOrder;
use App\Models\Team;
use App\Models\User;
use App\Models\VisitTask;
use App\Models\VisitTaskHistory;
use App\Models\VisitTaskResponse;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class VisitTaskController extends Controller
{
    /**
     * Display visit tasks list with RBAC filtering.
     */
    public function index(Request $request): View
    {
        $user = Auth::user();
        $query = VisitTask::with(['sales', 'sales.team', 'customer', 'supervisor', 'latestResponse']);

        // RBAC data isolation
        if ($user->hasRole('Sales Motoris')) {
            $query->where('sales_id', $user->id);
        } elseif ($user->hasRole('Supervisor Sales')) {
            // Supervisor only sees their team's tasks
            $teamMemberIds = User::where('team_id', $user->team_id)->pluck('id');
            $query->whereIn('sales_id', $teamMemberIds);
        }
        // Admin & Pimpinan see all

        // Filters
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->whereHas('sales', fn ($s) => $s->where('name', 'like', "%{$search}%"))
                    ->orWhereHas('customer', fn ($s) => $s->where('name', 'like', "%{$search}%"))
                    ->orWhere('task_code', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('priority')) {
            $query->where('priority', $request->priority);
        }

        if ($request->filled('sales_id') && ! $user->hasRole('Sales Motoris')) {
            $query->where('sales_id', $request->sales_id);
        }

        if ($request->filled('team_id') && $user->hasRole('Admin')) {
            $teamMemberIds = User::where('team_id', $request->team_id)->pluck('id');
            $query->whereIn('sales_id', $teamMemberIds);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('scheduled_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('scheduled_at', '<=', $request->date_to);
        }

        $tasks = $query->orderBy('scheduled_at', 'desc')->paginate(20)->withQueryString();

        // Data for filters/forms
        $customers = Customer::where('is_active', true)->orderBy('name')->get();
        $teams = Team::where('is_active', true)->orderBy('name')->get();

        // Which sales the current user can assign tasks to
        if ($user->hasRole('Supervisor Sales')) {
            $salesUsers = User::role('Sales Motoris')
                ->where('team_id', $user->team_id)
                ->orderBy('name')->get();
        } elseif ($user->hasRole('Admin')) {
            $salesUsers = User::role('Sales Motoris')->orderBy('name')->get();
        } else {
            $salesUsers = collect();
        }

        $statusList = array_keys(VisitTask::STATUS_TRANSITIONS);

        return view('visit-tasks.index', compact(
            'tasks', 'customers', 'teams', 'salesUsers', 'statusList'
        ));
    }

    /**
     * Store a new visit task (Admin / Supervisor only).
     */
    public function store(Request $request): RedirectResponse
    {
        $user = Auth::user();
        abort_unless($user->hasAnyRole(['Admin', 'Supervisor Sales']), 403);

        $validated = $request->validate([
            'sales_id' => ['required', 'exists:users,id'],
            'customer_id' => ['required', 'exists:customers,id'],
            'scheduled_at' => ['required', 'date', 'after:now'],
            'due_at' => ['nullable', 'date', 'after:scheduled_at'],
            'purpose' => ['nullable', 'string', 'max:500'],
            'target_result' => ['nullable', 'string', 'max:500'],
            'instructions' => ['nullable', 'string', 'max:1000'],
            'priority' => ['required', 'in:rendah,normal,tinggi,mendesak'],
        ]);

        // Supervisor can only assign to their team members
        if ($user->hasRole('Supervisor Sales')) {
            $salesUser = User::findOrFail($validated['sales_id']);
            abort_unless($salesUser->team_id === $user->team_id, 403, 'Anda hanya dapat menugaskan sales dalam tim Anda.');
        }

        $task = VisitTask::create([
            ...$validated,
            'task_code' => VisitTask::generateCode(),
            'supervisor_id' => $user->hasRole('Supervisor Sales') ? $user->id : null,
            'created_by' => $user->id,
            'status' => 'MENUNGGU_RESPONS',
        ]);

        // Log history
        VisitTaskHistory::create([
            'task_id' => $task->id,
            'changed_by' => $user->id,
            'from_status' => null,
            'to_status' => 'MENUNGGU_RESPONS',
            'notes' => 'Tugas dibuat oleh '.$user->name,
            'changed_at' => now(),
        ]);

        // Notify the sales person
        AppNotification::send(
            recipientId: $validated['sales_id'],
            type: 'task_assigned',
            title: 'Tugas Kunjungan Baru',
            message: "Anda mendapat tugas kunjungan ke {$task->customer->name} pada ".$task->scheduled_at->format('d/m/Y H:i'),
            senderId: $user->id,
            entityType: 'visit_tasks',
            entityId: $task->id,
            actionUrl: route('visit-tasks.index')
        );

        return to_route('visit-tasks.index')
            ->with('status', "Tugas {$task->task_code} berhasil dibuat dan dikirim ke sales.");
    }

    /**
     * Accept a task (Sales Motoris only).
     */
    public function accept(Request $request, VisitTask $visitTask): RedirectResponse
    {
        $user = Auth::user();
        abort_unless($visitTask->sales_id === $user->id, 403);

        // Idempotency check (e.g. duplicate submission)
        if ($visitTask->status === 'DITERIMA') {
            return back()->with('status', "Tugas {$visitTask->task_code} sudah diterima.");
        }

        if (! $visitTask->canTransitionTo('DITERIMA')) {
            return back()->with('error', 'Status tugas tidak dapat diubah menjadi DITERIMA.');
        }

        $fromStatus = $visitTask->status;
        $visitTask->update([
            'status' => 'DITERIMA',
            'responded_at' => now(),
        ]);

        VisitTaskResponse::create([
            'task_id' => $visitTask->id,
            'user_id' => $user->id,
            'response' => 'DITERIMA',
            'responded_at' => now(),
        ]);

        VisitTaskHistory::create([
            'task_id' => $visitTask->id,
            'changed_by' => $user->id,
            'from_status' => $fromStatus,
            'to_status' => 'DITERIMA',
            'notes' => 'Tugas diterima oleh sales.',
            'changed_at' => now(),
        ]);

        // Notify supervisor/creator
        $this->notifyTaskChange($visitTask, $user, 'task_accepted', 'Tugas Diterima',
            "{$user->name} telah menerima tugas {$visitTask->task_code}.");

        return back()->with('status', "Tugas {$visitTask->task_code} berhasil diterima.");
    }

    /**
     * Reject a task (Sales Motoris only).
     */
    public function reject(Request $request, VisitTask $visitTask): RedirectResponse
    {
        $user = Auth::user();
        abort_unless($visitTask->sales_id === $user->id, 403);

        // Idempotency check (e.g. duplicate submission)
        if ($visitTask->status === 'DITOLAK') {
            return back()->with('status', "Tugas {$visitTask->task_code} sudah ditolak.");
        }

        if (! $visitTask->canTransitionTo('DITOLAK')) {
            return back()->with('error', 'Status tugas tidak dapat diubah menjadi DITOLAK.');
        }

        $validated = $request->validate([
            'rejection_category' => ['required', 'string', 'max:100'],
            'reason' => ['required', 'string', 'min:10', 'max:500'],
        ]);

        $fromStatus = $visitTask->status;
        $visitTask->update([
            'status' => 'DITOLAK',
            'responded_at' => now(),
        ]);

        VisitTaskResponse::create([
            'task_id' => $visitTask->id,
            'user_id' => $user->id,
            'response' => 'DITOLAK',
            'rejection_category' => $validated['rejection_category'],
            'reason' => $validated['reason'],
            'responded_at' => now(),
        ]);

        VisitTaskHistory::create([
            'task_id' => $visitTask->id,
            'changed_by' => $user->id,
            'from_status' => $fromStatus,
            'to_status' => 'DITOLAK',
            'notes' => "Ditolak: [{$validated['rejection_category']}] {$validated['reason']}",
            'changed_at' => now(),
        ]);

        // Notify supervisor/creator
        $this->notifyTaskChange($visitTask, $user, 'task_rejected', 'Tugas Ditolak',
            "{$user->name} menolak tugas {$visitTask->task_code}. Alasan: {$validated['reason']}");

        return back()->with('status', "Tugas {$visitTask->task_code} ditolak. Supervisor/Admin telah diberitahu.");
    }

    /**
     * Reschedule / reassign a rejected task (Admin/Supervisor only).
     */
    public function reschedule(Request $request, VisitTask $visitTask): RedirectResponse
    {
        $user = Auth::user();
        abort_unless($user->hasAnyRole(['Admin', 'Supervisor Sales']), 403);
        abort_unless($visitTask->canTransitionTo('DIJADWALKAN_ULANG'), 422, 'Tugas tidak dapat dijadwalkan ulang saat ini.');

        $validated = $request->validate([
            'sales_id' => ['nullable', 'exists:users,id'],
            'scheduled_at' => ['required', 'date', 'after:now'],
            'due_at' => ['nullable', 'date', 'after:scheduled_at'],
            'instructions' => ['nullable', 'string', 'max:1000'],
        ]);

        $fromStatus = $visitTask->status;
        $updates = [
            'status' => 'DIJADWALKAN_ULANG',
            'scheduled_at' => $validated['scheduled_at'],
            'due_at' => $validated['due_at'] ?? $visitTask->due_at,
            'instructions' => $validated['instructions'] ?? $visitTask->instructions,
            'responded_at' => null,
        ];

        if (! empty($validated['sales_id'])) {
            $updates['sales_id'] = $validated['sales_id'];
        }

        $visitTask->update($updates);

        VisitTaskHistory::create([
            'task_id' => $visitTask->id,
            'changed_by' => $user->id,
            'from_status' => $fromStatus,
            'to_status' => 'DIJADWALKAN_ULANG',
            'notes' => 'Tugas dijadwalkan ulang oleh '.$user->name,
            'changed_at' => now(),
        ]);

        // Re-notify sales
        AppNotification::send(
            recipientId: $visitTask->sales_id,
            type: 'task_rescheduled',
            title: 'Tugas Dijadwalkan Ulang',
            message: "Tugas {$visitTask->task_code} telah dijadwalkan ulang ke ".$visitTask->fresh()->scheduled_at->format('d/m/Y H:i'),
            senderId: $user->id,
            entityType: 'visit_tasks',
            entityId: $visitTask->id,
            actionUrl: route('visit-tasks.index')
        );

        // Move to MENUNGGU_RESPONS automatically
        $visitTask->update(['status' => 'MENUNGGU_RESPONS']);

        return back()->with('status', "Tugas {$visitTask->task_code} berhasil dijadwalkan ulang.");
    }

    /**
     * Cancel a task (Admin/Supervisor only).
     */
    public function cancel(Request $request, VisitTask $visitTask): RedirectResponse
    {
        $user = Auth::user();
        abort_unless($user->hasAnyRole(['Admin', 'Supervisor Sales']), 403);
        abort_unless($visitTask->canTransitionTo('DIBATALKAN'), 422, 'Tugas tidak dapat dibatalkan dari status saat ini.');

        $validated = $request->validate([
            'reason' => ['required', 'string', 'min:5', 'max:500'],
        ]);

        $fromStatus = $visitTask->status;
        $visitTask->update(['status' => 'DIBATALKAN']);

        VisitTaskHistory::create([
            'task_id' => $visitTask->id,
            'changed_by' => $user->id,
            'from_status' => $fromStatus,
            'to_status' => 'DIBATALKAN',
            'notes' => "Dibatalkan: {$validated['reason']}",
            'changed_at' => now(),
        ]);

        // Notify sales
        AppNotification::send(
            recipientId: $visitTask->sales_id,
            type: 'task_cancelled',
            title: 'Tugas Dibatalkan',
            message: "Tugas {$visitTask->task_code} telah dibatalkan. Alasan: {$validated['reason']}",
            senderId: $user->id,
            entityType: 'visit_tasks',
            entityId: $visitTask->id,
            actionUrl: route('visit-tasks.index')
        );

        return back()->with('status', "Tugas {$visitTask->task_code} berhasil dibatalkan.");
    }

    /**
     * Show task detail & history.
     */
    public function show(VisitTask $visitTask): View
    {
        $user = Auth::user();

        // RBAC: Sales can only see their own tasks
        if ($user->hasRole('Sales Motoris')) {
            abort_unless($visitTask->sales_id === $user->id, 403);
        } elseif ($user->hasRole('Supervisor Sales')) {
            $teamIds = User::where('team_id', $user->team_id)->pluck('id');
            abort_unless($teamIds->contains($visitTask->sales_id), 403);
        }

        $visitTask->load([
            'sales', 'sales.team', 'customer', 'supervisor', 'creator',
            'responses.user', 'histories.changedBy',
            'order.items.product', 'order.payments',
            'orders.items.product', 'orders.payments',
        ]);

        $products = Product::where('is_active', true)
            ->orderBy('name')
            ->get();

        return view('visit-tasks.show', compact('visitTask', 'products'));
    }

    /**
     * Update status for in-journey steps (Sales Motoris).
     */
    public function updateStatus(Request $request, VisitTask $visitTask): RedirectResponse
    {
        $user = Auth::user();
        abort_unless($visitTask->sales_id === $user->id, 403);

        $newStatus = $request->input('status');

        // Idempotency check: jika status tugas saat ini sudah sama dengan status yang diminta (misal duplicate submit/double-click)
        if ($visitTask->status === $newStatus) {
            return to_route('visit-tasks.show', $visitTask)
                ->with('status', 'Status tugas sudah diperbarui ke: '.str_replace('_', ' ', $newStatus));
        }

        if (! $visitTask->canTransitionTo($newStatus)) {
            return back()->with('error', 'Perubahan status dari '.str_replace('_', ' ', $visitTask->status).' ke '.str_replace('_', ' ', $newStatus).' tidak diizinkan.');
        }

        $validated = $request->validate([
            'status' => ['required', 'string'],
            'latitude' => ['nullable', 'numeric', 'between:-90,90'],
            'longitude' => ['nullable', 'numeric', 'between:-180,180'],
            'visit_result' => ['nullable', 'string', 'max:1000'],
            'visit_notes' => ['nullable', 'string', 'max:1000'],
            'documentation_image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:5120'], // Max 5MB
            'has_order' => ['nullable'],
            'order_payment_method' => ['nullable', 'string', 'in:tunai,transfer,tempo,dp'],
            'order_down_payment' => ['nullable', 'numeric', 'min:0'],
            'order_due_date' => ['nullable', 'date'],
            'order_tempo_days' => ['nullable', 'integer', 'min:1'],
            'order_notes' => ['nullable', 'string', 'max:500'],
            'order_items' => ['nullable', 'array'],
            'order_items.*.product_id' => ['nullable', 'exists:products,id'],
            'order_items.*.quantity' => ['nullable', 'integer', 'min:1'],
        ]);

        $fromStatus = $visitTask->status;
        $updateData = ['status' => $newStatus];

        if ($newStatus === 'TIBA_DI_LOKASI' && $validated['latitude'] && $validated['longitude']) {
            $updateData['check_in_at'] = now();
            $updateData['check_in_latitude'] = $validated['latitude'];
            $updateData['check_in_longitude'] = $validated['longitude'];

            // Calculate distance from outlet
            if ($visitTask->customer->latitude && $visitTask->customer->longitude) {
                $dist = $this->haversine(
                    floatval($validated['latitude']), floatval($validated['longitude']),
                    floatval($visitTask->customer->latitude), floatval($visitTask->customer->longitude)
                );
                $updateData['check_in_distance_meters'] = $dist;
            }
        }

        $createdOrder = null;

        if ($newStatus === 'SELESAI') {
            $updateData['check_out_at'] = now();
            $updateData['check_out_latitude'] = $validated['latitude'] ?? null;
            $updateData['check_out_longitude'] = $validated['longitude'] ?? null;
            $updateData['visit_result'] = $validated['visit_result'] ?? null;
            $updateData['visit_notes'] = $validated['visit_notes'] ?? null;

            if ($request->hasFile('documentation_image')) {
                if ($visitTask->documentation_image && Storage::disk('public')->exists($visitTask->documentation_image)) {
                    Storage::disk('public')->delete($visitTask->documentation_image);
                }
                $storedPath = $request->file('documentation_image')->store('documentation_photos', 'public');
                $updateData['documentation_image'] = $storedPath;
            }

            // Optional Taking Order Processing
            if ($request->boolean('has_order')) {
                $rawItems = $request->input('order_items', []);
                $validItems = [];
                foreach ($rawItems as $item) {
                    if (! empty($item['product_id']) && ! empty($item['quantity']) && (int) $item['quantity'] > 0) {
                        $validItems[] = [
                            'product_id' => (int) $item['product_id'],
                            'quantity' => (int) $item['quantity'],
                        ];
                    }
                }

                if (! empty($validItems)) {
                    $createdOrder = DB::transaction(function () use ($request, $user, $visitTask, $validItems) {
                        $method = strtolower($request->input('order_payment_method', 'tunai'));
                        if (! in_array($method, ['tunai', 'transfer', 'tempo', 'dp'])) {
                            $method = 'tunai';
                        }

                        // Due date calculation for tempo or dp
                        $dueDate = null;
                        if (in_array($method, ['tempo', 'dp'])) {
                            if ($request->filled('order_due_date')) {
                                $dueDate = Carbon::parse($request->input('order_due_date'));
                            } elseif ($request->filled('order_tempo_days')) {
                                $dueDate = Carbon::today()->addDays((int) $request->input('order_tempo_days'));
                            } else {
                                $dueDate = Carbon::today()->addDays(14);
                            }
                        }

                        $order = SalesOrder::create([
                            'order_number' => 'SO-'.now()->format('ymdHis').'-'.random_int(100, 999),
                            'user_id' => $user->id,
                            'customer_id' => $visitTask->customer_id,
                            'visit_task_id' => $visitTask->id,
                            'order_date' => Carbon::today(),
                            'status' => 'pending',
                            'payment_method' => $method,
                            'payment_status' => 'belum_lunas',
                            'total_amount' => 0,
                            'down_payment' => 0,
                            'paid_amount' => 0,
                            'remaining_amount' => 0,
                            'due_date' => $dueDate,
                            'notes' => $request->input('order_notes') ?: ('Pemesanan dari Kunjungan '.$visitTask->task_code),
                        ]);

                        $total = 0;
                        foreach ($validItems as $itemData) {
                            $product = Product::findOrFail($itemData['product_id']);
                            $qty = $itemData['quantity'];
                            $price = (float) $product->price;
                            $subtotal = $price * $qty;
                            $total += $subtotal;

                            $order->items()->create([
                                'product_id' => $product->id,
                                'quantity' => $qty,
                                'unit_price' => $price,
                                'discount_amount' => 0,
                                'subtotal' => $subtotal,
                            ]);

                            // Graceful stock reduction
                            if ($product->stock_carried >= $qty) {
                                $product->decrement('stock_carried', $qty);
                            } elseif ($product->stock_on_hand >= $qty) {
                                $product->decrement('stock_on_hand', $qty);
                            }
                        }

                        // Calculate payment status & breakdown
                        $rawDp = (float) ($request->input('order_down_payment') ?? 0);
                        if ($method === 'tunai' || $method === 'transfer') {
                            $paid = $total;
                            $remaining = 0;
                            $paymentStatus = 'lunas';
                            $dp = 0;
                            $finalDueDate = null;
                        } elseif ($method === 'dp') {
                            $dp = min($total, max(0, $rawDp > 0 ? $rawDp : round($total * 0.3, -3)));
                            $paid = $dp;
                            $remaining = max(0, $total - $paid);
                            $paymentStatus = ($remaining <= 0) ? 'lunas' : 'sebagian';
                            $finalDueDate = ($paymentStatus === 'lunas') ? null : $dueDate;
                        } else { // tempo
                            $dp = 0;
                            $paid = 0;
                            $remaining = $total;
                            $paymentStatus = 'belum_lunas';
                            $finalDueDate = $dueDate;
                        }

                        $order->update([
                            'total_amount' => $total,
                            'down_payment' => $dp,
                            'paid_amount' => $paid,
                            'remaining_amount' => $remaining,
                            'payment_status' => $paymentStatus,
                            'due_date' => $finalDueDate,
                        ]);

                        // Record initial payment if paid > 0 (Tunai, Transfer, or DP)
                        if ($paid > 0) {
                            OrderPayment::create([
                                'sales_order_id' => $order->id,
                                'payment_number' => 'PAY-'.now()->format('ymdHis').'-'.random_int(100, 999),
                                'payment_date' => Carbon::today(),
                                'amount' => $paid,
                                'payment_method' => ($method === 'dp' ? 'tunai' : $method),
                                'notes' => ($method === 'dp' ? 'Uang Muka (DP) Pemesanan Kunjungan' : 'Pembayaran Lunas saat Kunjungan'),
                                'received_by_user_id' => $user->id,
                            ]);
                        }

                        return $order;
                    });

                    // Append order summary to visit_result
                    $orderSummary = "[Pesanan {$createdOrder->order_number}: Rp ".number_format($createdOrder->total_amount, 0, ',', '.').' ('.strtoupper($createdOrder->payment_method).')]';
                    $updateData['visit_result'] = trim(($updateData['visit_result'] ?? '')."\n".$orderSummary);
                }
            }
        }

        $visitTask->update($updateData);

        VisitTaskHistory::create([
            'task_id' => $visitTask->id,
            'changed_by' => $user->id,
            'from_status' => $fromStatus,
            'to_status' => $newStatus,
            'notes' => null,
            'metadata' => isset($validated['latitude']) ? [
                'latitude' => $validated['latitude'],
                'longitude' => $validated['longitude'],
            ] : null,
            'changed_at' => now(),
        ]);

        // Notify supervisor when completed
        if ($newStatus === 'SELESAI' && $visitTask->supervisor_id) {
            AppNotification::send(
                recipientId: $visitTask->supervisor_id,
                type: 'task_completed',
                title: 'Tugas Selesai',
                message: "{$user->name} telah menyelesaikan kunjungan ke {$visitTask->customer->name}.",
                senderId: $user->id,
                entityType: 'visit_tasks',
                entityId: $visitTask->id,
                actionUrl: route('visit-tasks.show', $visitTask)
            );
        }

        $statusLabels = [
            'DALAM_PERJALANAN' => 'Dalam Perjalanan',
            'TIBA_DI_LOKASI' => 'Tiba di Lokasi (Check-in)',
            'SEDANG_KUNJUNGAN' => 'Sedang Kunjungan',
            'SELESAI' => 'Selesai',
        ];

        $msg = 'Status tugas diperbarui ke: '.($statusLabels[$newStatus] ?? $newStatus);
        if ($createdOrder) {
            $msg .= ". Pesanan {$createdOrder->order_number} berhasil dibuat & otomatis tersambung ke modul Penjualan & Laporan Keuangan.";
        }

        return back()->with('status', $msg);
    }

    /**
     * Catat pesanan produk (taking order) langsung dari halaman detail tugas.
     */
    public function storeOrder(Request $request, VisitTask $visitTask): RedirectResponse
    {
        $user = Auth::user();

        // RBAC check
        if ($user->hasRole('Sales Motoris')) {
            abort_unless($visitTask->sales_id === $user->id, 403);
        }

        $request->validate([
            'order_payment_method' => ['required', 'string', 'in:tunai,transfer,tempo,dp'],
            'order_down_payment' => ['nullable', 'numeric', 'min:0'],
            'order_due_date' => ['nullable', 'date'],
            'order_tempo_days' => ['nullable', 'integer', 'min:1'],
            'order_notes' => ['nullable', 'string', 'max:500'],
            'order_items' => ['required', 'array', 'min:1'],
            'order_items.*.product_id' => ['required', 'exists:products,id'],
            'order_items.*.quantity' => ['required', 'integer', 'min:1'],
        ]);

        $createdOrder = DB::transaction(function () use ($request, $user, $visitTask) {
            $method = strtolower($request->input('order_payment_method', 'tunai'));
            if (! in_array($method, ['tunai', 'transfer', 'tempo', 'dp'])) {
                $method = 'tunai';
            }

            $dueDate = null;
            if (in_array($method, ['tempo', 'dp'])) {
                if ($request->filled('order_due_date')) {
                    $dueDate = Carbon::parse($request->input('order_due_date'));
                } elseif ($request->filled('order_tempo_days')) {
                    $dueDate = Carbon::today()->addDays((int) $request->input('order_tempo_days'));
                } else {
                    $dueDate = Carbon::today()->addDays(14);
                }
            }

            $order = SalesOrder::create([
                'order_number' => 'SO-'.now()->format('ymdHis').'-'.random_int(100, 999),
                'user_id' => $visitTask->sales_id ?: $user->id,
                'customer_id' => $visitTask->customer_id,
                'visit_task_id' => $visitTask->id,
                'order_date' => Carbon::today(),
                'status' => 'pending',
                'payment_method' => $method,
                'payment_status' => 'belum_lunas',
                'total_amount' => 0,
                'down_payment' => 0,
                'paid_amount' => 0,
                'remaining_amount' => 0,
                'due_date' => $dueDate,
                'notes' => $request->input('order_notes') ?: ('Pemesanan dari Kunjungan '.$visitTask->task_code),
            ]);

            $total = 0;
            foreach ($request->input('order_items', []) as $itemData) {
                if (empty($itemData['product_id']) || empty($itemData['quantity'])) {
                    continue;
                }
                $product = Product::findOrFail($itemData['product_id']);
                $qty = (int) $itemData['quantity'];
                $price = (float) $product->price;
                $subtotal = $price * $qty;
                $total += $subtotal;

                $order->items()->create([
                    'product_id' => $product->id,
                    'quantity' => $qty,
                    'unit_price' => $price,
                    'discount_amount' => 0,
                    'subtotal' => $subtotal,
                ]);

                if ($product->stock_carried >= $qty) {
                    $product->decrement('stock_carried', $qty);
                } elseif ($product->stock_on_hand >= $qty) {
                    $product->decrement('stock_on_hand', $qty);
                }
            }

            $rawDp = (float) ($request->input('order_down_payment') ?? 0);
            if ($method === 'tunai' || $method === 'transfer') {
                $paid = $total;
                $remaining = 0;
                $paymentStatus = 'lunas';
                $dp = 0;
                $finalDueDate = null;
            } elseif ($method === 'dp') {
                $dp = min($total, max(0, $rawDp > 0 ? $rawDp : round($total * 0.3, -3)));
                $paid = $dp;
                $remaining = max(0, $total - $paid);
                $paymentStatus = ($remaining <= 0) ? 'lunas' : 'sebagian';
                $finalDueDate = ($paymentStatus === 'lunas') ? null : $dueDate;
            } else { // tempo
                $dp = 0;
                $paid = 0;
                $remaining = $total;
                $paymentStatus = 'belum_lunas';
                $finalDueDate = $dueDate;
            }

            $order->update([
                'total_amount' => $total,
                'down_payment' => $dp,
                'paid_amount' => $paid,
                'remaining_amount' => $remaining,
                'payment_status' => $paymentStatus,
                'due_date' => $finalDueDate,
            ]);

            if ($paid > 0) {
                OrderPayment::create([
                    'sales_order_id' => $order->id,
                    'payment_number' => 'PAY-'.now()->format('ymdHis').'-'.random_int(100, 999),
                    'payment_date' => Carbon::today(),
                    'amount' => $paid,
                    'payment_method' => ($method === 'dp' ? 'tunai' : $method),
                    'notes' => ($method === 'dp' ? 'Uang Muka (DP) Pemesanan Kunjungan' : 'Pembayaran Lunas saat Kunjungan'),
                    'received_by_user_id' => $user->id,
                ]);
            }

            return $order;
        });

        // Also update task visit_result if not already containing order info
        $orderSummary = "[Pesanan {$createdOrder->order_number}: Rp ".number_format($createdOrder->total_amount, 0, ',', '.').' ('.strtoupper($createdOrder->payment_method).')]';
        if (! str_contains($visitTask->visit_result ?? '', $createdOrder->order_number)) {
            $visitTask->update([
                'visit_result' => trim(($visitTask->visit_result ?? '')."\n".$orderSummary),
            ]);
        }

        return back()->with('status', "Pesanan {$createdOrder->order_number} berhasil dicatat & otomatis tersambung ke Penjualan dan Laporan Keuangan/Piutang.");
    }

    /**
     * Helper: notify creator & supervisor of task changes.
     */
    private function notifyTaskChange(VisitTask $task, User $actor, string $type, string $title, string $message): void
    {
        $recipientIds = array_filter(array_unique([
            $task->supervisor_id,
            $task->created_by,
        ]));

        foreach ($recipientIds as $recipientId) {
            if ($recipientId !== $actor->id) {
                AppNotification::send(
                    recipientId: $recipientId,
                    type: $type,
                    title: $title,
                    message: $message,
                    senderId: $actor->id,
                    entityType: 'visit_tasks',
                    entityId: $task->id,
                    actionUrl: route('visit-tasks.show', $task)
                );
            }
        }
    }

    /**
     * Haversine formula for distance in meters.
     */
    private function haversine(float $lat1, float $lng1, float $lat2, float $lng2): float
    {
        $R = 6371000;
        $rad = M_PI / 180;
        $dLat = ($lat2 - $lat1) * $rad;
        $dLng = ($lng2 - $lng1) * $rad;
        $a = sin($dLat / 2) ** 2
            + cos($lat1 * $rad) * cos($lat2 * $rad) * sin($dLng / 2) ** 2;

        return 2 * $R * asin(sqrt($a));
    }

    /**
     * API: get tasks data as JSON for Live Tracking map.
     */
    public function apiIndex(Request $request): JsonResponse
    {
        $user = Auth::user();
        $query = VisitTask::with(['sales', 'customer'])
            ->whereIn('status', ['DALAM_PERJALANAN', 'TIBA_DI_LOKASI', 'SEDANG_KUNJUNGAN', 'DITERIMA']);

        if ($user->hasRole('Sales Motoris')) {
            $query->where('sales_id', $user->id);
        } elseif ($user->hasRole('Supervisor Sales')) {
            $teamIds = User::where('team_id', $user->team_id)->pluck('id');
            $query->whereIn('sales_id', $teamIds);
        }

        return response()->json($query->get()->map(fn ($t) => [
            'id' => $t->id,
            'code' => $t->task_code,
            'sales' => $t->sales->name,
            'team' => $t->sales->team?->name,
            'customer' => $t->customer->name,
            'status' => $t->status,
            'scheduled' => $t->scheduled_at?->toIso8601String(),
            'outlet_lat' => $t->customer->latitude,
            'outlet_lng' => $t->customer->longitude,
        ]));
    }
}
