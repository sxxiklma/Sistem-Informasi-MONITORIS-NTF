<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSalesOrderRequest;
use App\Models\Customer;
use App\Models\Product;
use App\Models\SalesOrder;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class SalesOrderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        $query = SalesOrder::with(['customer', 'user', 'items.product', 'payments'])->latest('order_date');
        if (auth()->user()->hasRole('Sales Motoris')) {
            $query->where('user_id', auth()->id());
        }

        return view('orders.index', ['orders' => $query->paginate(15)]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        return view('orders.create', [
            'customers' => Customer::where('is_active', true)->orderBy('name')->get(),
            'products' => Product::where('is_active', true)->orderBy('name')->get(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreSalesOrderRequest $request)
    {
        $order = DB::transaction(function () use ($request) {
            $data = $request->validated();
            $items = $data['items'] ?? [
                [
                    'product_id' => $data['product_id'],
                    'quantity' => $data['quantity'] ?? 1,
                    'discount_amount' => $data['discount_amount'] ?? 0,
                ],
            ];

            unset($data['items']);
            unset($data['product_id'], $data['quantity'], $data['discount_amount']);

            // Normalize payment method
            $rawMethod = strtolower($data['payment_method'] ?? 'tunai');
            if ($rawMethod === 'termin') {
                $rawMethod = 'tempo';
            }
            $data['payment_method'] = in_array($rawMethod, ['tunai', 'transfer', 'tempo', 'dp']) ? $rawMethod : 'tunai';

            $rawDp = (float) ($data['down_payment'] ?? 0);
            $rawDueDate = ! empty($data['due_date']) ? Carbon::parse($data['due_date']) : null;

            $data += [
                'user_id' => $request->user()->id,
                'order_number' => 'SO-'.now()->format('ymdHis').'-'.random_int(100, 999),
                'total_amount' => 0,
                'down_payment' => 0,
                'paid_amount' => 0,
                'remaining_amount' => 0,
            ];

            $order = SalesOrder::create($data);
            $total = 0;

            foreach ($items as $item) {
                $product = Product::findOrFail($item['product_id']);
                $discount = (float) ($item['discount_amount'] ?? 0);
                $subtotal = max(0, ($product->price * $item['quantity']) - $discount);

                $order->items()->create([
                    'product_id' => $product->id,
                    'quantity' => $item['quantity'],
                    'unit_price' => $product->price,
                    'discount_amount' => $discount,
                    'subtotal' => $subtotal,
                ]);

                $total += $subtotal;
            }

            // Calculate payments based on method
            if ($order->payment_method === 'tunai' || $order->payment_method === 'transfer') {
                $paid = $total;
                $remaining = 0;
                $status = 'lunas';
                $dueDate = null;
                $dp = 0;
            } elseif ($order->payment_method === 'dp') {
                $dp = min($total, max(0, $rawDp > 0 ? $rawDp : round($total * 0.3, -3)));
                $paid = $dp;
                $remaining = max(0, $total - $paid);
                $status = ($remaining <= 0) ? 'lunas' : 'sebagian';
                $dueDate = $rawDueDate ?? Carbon::today()->addDays(14);
            } else {
                // tempo
                $dp = 0;
                $paid = 0;
                $remaining = $total;
                $status = 'belum_lunas';
                $dueDate = $rawDueDate ?? Carbon::today()->addDays(14);
            }

            $order->update([
                'total_amount' => $total,
                'down_payment' => $dp,
                'paid_amount' => $paid,
                'remaining_amount' => $remaining,
                'payment_status' => $status,
                'due_date' => $dueDate,
            ]);

            return $order;
        });

        return to_route('orders.index')->with('status', "Pesanan {$order->order_number} berhasil dibuat.");
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
