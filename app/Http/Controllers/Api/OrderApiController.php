<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\SalesOrder;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderApiController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();
        $query = SalesOrder::with(['customer', 'user', 'items.product'])->latest('order_date');

        if ($user->hasRole('Sales Motoris')) {
            $query->where('user_id', $user->id);
        } elseif ($user->hasRole('Supervisor Sales')) {
            $memberIds = User::where('team_id', $user->team_id)->pluck('id');
            $query->whereIn('user_id', $memberIds);
        }

        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('order_number', 'like', "%{$search}%")
                    ->orWhereHas('customer', fn ($c) => $c->where('name', 'like', "%{$search}%"))
                    ->orWhereHas('user', fn ($u) => $u->where('name', 'like', "%{$search}%"));
            });
        }

        $orders = $query->get()->map(fn ($o) => [
            'id' => $o->id,
            'order_number' => $o->order_number,
            'order_date' => $o->order_date?->format('d M Y') ?? '-',
            'status' => $o->status,
            'total_amount' => (float) $o->total_amount,
            'formatted_total' => 'Rp '.number_format($o->total_amount, 0, ',', '.'),
            'notes' => $o->notes,
            'customer' => [
                'id' => $o->customer?->id,
                'name' => $o->customer?->name ?? 'Outlet',
                'address' => $o->customer?->address ?? '-',
            ],
            'sales' => [
                'id' => $o->user?->id,
                'name' => $o->user?->name ?? 'Sales',
            ],
            'items_count' => $o->items->count(),
            'items' => $o->items->map(fn ($it) => [
                'id' => $it->id,
                'product_name' => $it->product?->name ?? 'Produk',
                'quantity' => $it->quantity,
                'unit_price' => (float) $it->unit_price,
                'subtotal' => (float) $it->subtotal,
            ]),
        ]);

        $summary = [
            'total_orders' => $orders->count(),
            'total_revenue' => (float) $orders->sum('total_amount'),
            'formatted_revenue' => 'Rp '.number_format($orders->sum('total_amount'), 0, ',', '.'),
        ];

        return response()->json([
            'success' => true,
            'summary' => $summary,
            'orders' => $orders,
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'customer_id' => ['required', 'exists:customers,id'],
            'notes' => ['nullable', 'string', 'max:500'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['required', 'exists:products,id'],
            'items.*.quantity' => ['required', 'integer', 'min:1'],
        ]);

        $order = DB::transaction(function () use ($request, $validated) {
            $user = $request->user();
            $orderNumber = 'SO-'.now()->format('ymdHis').'-'.random_int(100, 999);

            $order = SalesOrder::create([
                'order_number' => $orderNumber,
                'user_id' => $user->id,
                'customer_id' => $validated['customer_id'],
                'order_date' => today(),
                'status' => 'dikonfirmasi',
                'total_amount' => 0,
                'notes' => $validated['notes'] ?? null,
            ]);

            $total = 0;
            foreach ($validated['items'] as $item) {
                $product = Product::findOrFail($item['product_id']);
                $qty = (int) $item['quantity'];
                $subtotal = $product->price * $qty;

                $order->items()->create([
                    'product_id' => $product->id,
                    'quantity' => $qty,
                    'unit_price' => $product->price,
                    'discount_amount' => 0,
                    'subtotal' => $subtotal,
                ]);

                // Reduce stock
                $product->decrement('stock_on_hand', $qty);
                $total += $subtotal;
            }

            $order->update(['total_amount' => $total]);

            return $order;
        });

        return response()->json([
            'success' => true,
            'message' => "Pesanan {$order->order_number} berhasil dibuat!",
            'order' => $order->load(['customer', 'items.product']),
        ]);
    }
}
