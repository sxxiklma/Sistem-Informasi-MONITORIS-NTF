<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\OrderPayment;
use App\Models\SalesOrder;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\Response;

class FinanceController extends Controller
{
    /**
     * Tampilan utama Laporan Keuangan & Piutang Kios
     */
    public function index(Request $request): View
    {
        $user = Auth::user();
        abort_unless($user instanceof User && ($user->can('lihat laporan') || $user->can('kelola pesanan')), 403);

        $today = Carbon::today();

        // 1. Base Query
        $baseQuery = SalesOrder::query()
            ->with(['customer', 'user', 'items.product', 'payments.receivedBy']);

        if ($user->hasRole('Sales Motoris')) {
            $baseQuery->where('user_id', $user->id);
        }

        // 2. Global / Unfiltered KPI Statistics (for accurate financial overview)
        $statsQuery = clone $baseQuery;
        $totalOrders = (clone $statsQuery)->count();
        $totalOmzet = (clone $statsQuery)->sum('total_amount');
        $totalKasMasuk = (clone $statsQuery)->sum('paid_amount');
        $totalPiutang = (clone $statsQuery)->sum('remaining_amount');

        $totalJatuhTempo = (clone $statsQuery)
            ->where('remaining_amount', '>', 0)
            ->whereNotNull('due_date')
            ->where('due_date', '<', $today)
            ->sum('remaining_amount');

        $countJatuhTempo = (clone $statsQuery)
            ->where('remaining_amount', '>', 0)
            ->whereNotNull('due_date')
            ->where('due_date', '<', $today)
            ->count();

        $countKiosPiutang = (clone $statsQuery)
            ->where('remaining_amount', '>', 0)
            ->distinct('customer_id')
            ->count('customer_id');

        $stats = [
            'total_orders' => $totalOrders,
            'total_omzet' => $totalOmzet,
            'total_kas_masuk' => $totalKasMasuk,
            'total_piutang' => $totalPiutang,
            'total_jatuh_tempo' => $totalJatuhTempo,
            'count_jatuh_tempo' => $countJatuhTempo,
            'count_kios_piutang' => $countKiosPiutang,
        ];

        // 3. Apply Filters for the Table
        $query = clone $baseQuery;

        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('order_number', 'like', "%{$search}%")
                    ->orWhere('notes', 'like', "%{$search}%")
                    ->orWhereHas('customer', function ($c) use ($search) {
                        $c->where('name', 'like', "%{$search}%")
                            ->orWhere('code', 'like', "%{$search}%")
                            ->orWhere('phone', 'like', "%{$search}%");
                    })
                    ->orWhereHas('user', fn ($u) => $u->where('name', 'like', "%{$search}%"));
            });
        }

        if ($customerId = $request->get('customer_id')) {
            $query->where('customer_id', $customerId);
        }

        if ($paymentStatus = $request->get('payment_status')) {
            if ($paymentStatus === 'lunas') {
                $query->where('remaining_amount', '<=', 0);
            } elseif ($paymentStatus === 'sebagian') {
                $query->where('remaining_amount', '>', 0)
                    ->where('paid_amount', '>', 0)
                    ->where(function ($q) use ($today) {
                        $q->whereNull('due_date')->orWhere('due_date', '>=', $today);
                    });
            } elseif ($paymentStatus === 'belum_lunas') {
                $query->where('remaining_amount', '>', 0)
                    ->where('paid_amount', '<=', 0)
                    ->where(function ($q) use ($today) {
                        $q->whereNull('due_date')->orWhere('due_date', '>=', $today);
                    });
            } elseif ($paymentStatus === 'jatuh_tempo') {
                $query->where('remaining_amount', '>', 0)
                    ->whereNotNull('due_date')
                    ->where('due_date', '<', $today);
            } elseif ($paymentStatus === 'piutang') {
                $query->where('remaining_amount', '>', 0);
            }
        }

        if ($paymentMethod = $request->get('payment_method')) {
            $query->where('payment_method', $paymentMethod);
        }

        if ($dateFrom = $request->get('date_from')) {
            $query->whereDate('order_date', '>=', $dateFrom);
        }

        if ($dateTo = $request->get('date_to')) {
            $query->whereDate('order_date', '<=', $dateTo);
        }

        $orders = $query->latest('order_date')
            ->latest('id')
            ->paginate(15)
            ->withQueryString();

        $customers = Customer::where('is_active', true)->orderBy('name')->get();

        return view('finance.index', compact('orders', 'stats', 'customers'));
    }

    /**
     * Catat pembayaran / cicilan piutang dari kios
     */
    public function recordPayment(Request $request, SalesOrder $order): RedirectResponse
    {
        $user = Auth::user();
        abort_unless($user instanceof User && ($user->can('kelola pesanan') || $user->can('lihat laporan')), 403);

        $maxAmount = (float) $order->remaining_amount;

        $validated = $request->validate([
            'payment_date' => ['required', 'date'],
            'amount' => ['required', 'numeric', 'min:100', 'max:'.max(1, $maxAmount)],
            'payment_method' => ['required', 'in:tunai,transfer'],
            'notes' => ['nullable', 'string', 'max:500'],
        ], [
            'amount.max' => 'Nominal pembayaran tidak boleh melebihi sisa piutang sebesar Rp '.number_format($maxAmount, 0, ',', '.'),
            'amount.min' => 'Nominal pembayaran minimal Rp 100',
        ]);

        DB::transaction(function () use ($order, $validated, $user) {
            $paymentCount = OrderPayment::where('sales_order_id', $order->id)->count() + 1;
            $paymentNumber = 'PAY-'.Carbon::parse($validated['payment_date'])->format('Ymd').'-'.str_pad($order->id, 3, '0', STR_PAD_LEFT).'-'.$paymentCount;

            OrderPayment::create([
                'sales_order_id' => $order->id,
                'payment_number' => $paymentNumber,
                'payment_date' => $validated['payment_date'],
                'amount' => $validated['amount'],
                'payment_method' => $validated['payment_method'],
                'notes' => $validated['notes'],
                'received_by_user_id' => $user->id,
            ]);

            $order->recalculatePaymentStatus();
        });

        return back()->with('status', 'Pembayaran sebesar Rp '.number_format($validated['amount'], 0, ',', '.').' untuk tagihan '.$order->order_number.' ('.$order->customer->name.') berhasil dicatat.');
    }

    /**
     * Ambil riwayat pembayaran tagihan via JSON
     */
    public function payments(SalesOrder $order): JsonResponse
    {
        $payments = $order->payments()
            ->with('receivedBy:id,name')
            ->orderBy('payment_date', 'asc')
            ->get();

        return response()->json([
            'order' => [
                'id' => $order->id,
                'order_number' => $order->order_number,
                'customer_name' => $order->customer?->name,
                'customer_phone' => $order->customer?->phone,
                'total_amount' => (float) $order->total_amount,
                'down_payment' => (float) $order->down_payment,
                'paid_amount' => (float) $order->paid_amount,
                'remaining_amount' => (float) $order->remaining_amount,
                'payment_status' => $order->payment_status,
                'due_date' => $order->due_date?->format('d/m/Y'),
            ],
            'payments' => $payments,
        ]);
    }

    /**
     * Cetak / Export Rekapitulasi Keuangan ke PDF
     */
    public function exportPdf(Request $request): Response
    {
        $user = Auth::user();
        abort_unless($user instanceof User && ($user->can('lihat laporan') || $user->can('kelola pesanan')), 403);

        $query = SalesOrder::query()->with(['customer', 'user', 'items.product', 'payments']);
        if ($user->hasRole('Sales Motoris')) {
            $query->where('user_id', $user->id);
        }

        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('order_number', 'like', "%{$search}%")
                    ->orWhereHas('customer', fn ($c) => $c->where('name', 'like', "%{$search}%"));
            });
        }

        if ($customerId = $request->get('customer_id')) {
            $query->where('customer_id', $customerId);
        }

        if ($paymentStatus = $request->get('payment_status')) {
            $today = Carbon::today();
            if ($paymentStatus === 'lunas') {
                $query->where('remaining_amount', '<=', 0);
            } elseif ($paymentStatus === 'sebagian') {
                $query->where('remaining_amount', '>', 0)->where('paid_amount', '>', 0);
            } elseif ($paymentStatus === 'belum_lunas') {
                $query->where('remaining_amount', '>', 0)->where('paid_amount', '<=', 0);
            } elseif ($paymentStatus === 'jatuh_tempo') {
                $query->where('remaining_amount', '>', 0)->whereNotNull('due_date')->where('due_date', '<', $today);
            }
        }

        $orders = $query->latest('order_date')->get();

        $stats = [
            'total_omzet' => $orders->sum('total_amount'),
            'total_kas_masuk' => $orders->sum('paid_amount'),
            'total_piutang' => $orders->sum('remaining_amount'),
            'count_piutang' => $orders->where('remaining_amount', '>', 0)->count(),
        ];

        $meta = [
            'title' => 'LAPORAN REKAPITULASI KEUANGAN & PIUTANG KIOS PELANGGAN',
            'downloaded_at' => now()->timezone('Asia/Jakarta')->translatedFormat('l, d F Y - H:i:s').' WIB',
            'downloaded_by' => $user->name.' ('.($user->roles->first()?->name ?? 'User').')',
        ];

        $pdf = Pdf::loadView('finance.pdf', compact('orders', 'stats', 'meta'))
            ->setPaper('a4', 'landscape');

        return $pdf->download('laporan-keuangan-piutang-'.now()->format('Y-m-d_His').'.pdf');
    }
}
