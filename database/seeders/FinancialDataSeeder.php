<?php

namespace Database\Seeders;

use App\Models\Customer;
use App\Models\OrderPayment;
use App\Models\Product;
use App\Models\SalesOrder;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class FinancialDataSeeder extends Seeder
{
    public function run(): void
    {
        $salesUser = User::whereHas('roles', fn ($q) => $q->where('name', 'Sales Motoris'))->first() ?? User::first();
        $adminUser = User::whereHas('roles', fn ($q) => $q->where('name', 'Admin'))->first() ?? $salesUser;
        $customers = Customer::all();
        $products = Product::where('is_active', true)->get();

        if ($customers->isEmpty() || $products->isEmpty()) {
            return;
        }

        // 1. Backfill existing orders if any
        $existingOrders = SalesOrder::all();
        foreach ($existingOrders as $idx => $order) {
            if ($order->total_amount <= 0) {
                $order->total_amount = 750000;
            }

            if ($idx === 0) {
                // Tunai Lunas
                $order->update([
                    'payment_method' => 'tunai',
                    'payment_status' => 'lunas',
                    'down_payment' => 0,
                    'paid_amount' => $order->total_amount,
                    'remaining_amount' => 0,
                    'due_date' => null,
                ]);
            } elseif ($idx === 1) {
                // DP Sebagian
                $dp = round($order->total_amount * 0.3, -3);
                $remaining = $order->total_amount - $dp;
                $order->update([
                    'payment_method' => 'dp',
                    'payment_status' => 'sebagian',
                    'down_payment' => $dp,
                    'paid_amount' => $dp,
                    'remaining_amount' => $remaining,
                    'due_date' => Carbon::today()->addDays(7),
                ]);
            } else {
                // Tempo belum lunas
                $order->update([
                    'payment_method' => 'tempo',
                    'payment_status' => 'belum_lunas',
                    'down_payment' => 0,
                    'paid_amount' => 0,
                    'remaining_amount' => $order->total_amount,
                    'due_date' => Carbon::today()->addDays(14),
                ]);
            }
        }

        // 2. Add realistic orders with specific Kiosk scenarios
        $scenarios = [
            [
                'customer_index' => 0,
                'method' => 'dp',
                'status' => 'sebagian',
                'items' => [
                    ['qty' => 15, 'disc' => 0],
                    ['qty' => 10, 'disc' => 15000],
                ],
                'dp_percent' => 0.4,
                'due_days' => 10,
                'payment_history' => [
                    ['amount_percent' => 0.2, 'days_ago' => 2, 'method' => 'tunai', 'notes' => 'Cicilan tempo ke-1 via sales motoris'],
                ],
                'notes' => 'Kios DP 40%, sisa dibayar bertahap 2 minggu',
            ],
            [
                'customer_index' => 1 % $customers->count(),
                'method' => 'tempo',
                'status' => 'jatuh_tempo',
                'items' => [
                    ['qty' => 25, 'disc' => 25000],
                ],
                'dp_percent' => 0,
                'due_days' => -4, // Overdue by 4 days
                'payment_history' => [],
                'notes' => 'Pembelian tempo 14 hari, konfirmasi jatuh tempo lewat 4 hari',
            ],
            [
                'customer_index' => 2 % $customers->count(),
                'method' => 'dp',
                'status' => 'sebagian',
                'items' => [
                    ['qty' => 30, 'disc' => 50000],
                    ['qty' => 20, 'disc' => 0],
                ],
                'dp_percent' => 0.5,
                'due_days' => 5,
                'payment_history' => [],
                'notes' => 'DP 50% ditransfer, sisa pelunasan saat kunjungan minggu depan',
            ],
            [
                'customer_index' => 3 % $customers->count(),
                'method' => 'tempo',
                'status' => 'belum_lunas',
                'items' => [
                    ['qty' => 12, 'disc' => 0],
                ],
                'dp_percent' => 0,
                'due_days' => 12,
                'payment_history' => [],
                'notes' => 'Kios langganan tempo 2 minggu',
            ],
            [
                'customer_index' => 4 % $customers->count(),
                'method' => 'tunai',
                'status' => 'lunas',
                'items' => [
                    ['qty' => 8, 'disc' => 10000],
                ],
                'dp_percent' => 1.0,
                'due_days' => 0,
                'payment_history' => [],
                'notes' => 'Pembelian tunai lunas di tempat',
            ],
            [
                'customer_index' => 0,
                'method' => 'tempo',
                'status' => 'jatuh_tempo',
                'items' => [
                    ['qty' => 40, 'disc' => 40000],
                ],
                'dp_percent' => 0.25,
                'due_days' => -8, // Overdue by 8 days
                'payment_history' => [
                    ['amount_percent' => 0.25, 'days_ago' => 10, 'method' => 'transfer', 'notes' => 'Cicilan pertama via transfer BCA'],
                ],
                'notes' => 'Jatuh tempo terlewat 8 hari, perlu penagihan sales',
            ],
        ];

        foreach ($scenarios as $idx => $sc) {
            $customer = $customers[$sc['customer_index']] ?? $customers->first();
            $orderDate = Carbon::today()->subDays(abs($sc['due_days']) + 5);
            $dueDate = Carbon::today()->addDays($sc['due_days']);

            $order = SalesOrder::create([
                'order_number' => 'SO-'.$orderDate->format('ymd').'-'.str_pad($idx + 10, 3, '0', STR_PAD_LEFT),
                'user_id' => $salesUser->id,
                'customer_id' => $customer->id,
                'order_date' => $orderDate,
                'status' => 'confirmed',
                'payment_method' => $sc['method'],
                'payment_status' => $sc['status'],
                'total_amount' => 0,
                'down_payment' => 0,
                'paid_amount' => 0,
                'remaining_amount' => 0,
                'due_date' => $sc['method'] === 'tunai' ? null : $dueDate,
                'notes' => $sc['notes'],
            ]);

            $totalAmount = 0;
            foreach ($sc['items'] as $itemIdx => $itemConf) {
                $product = $products[$itemIdx % $products->count()];
                $qty = $itemConf['qty'];
                $disc = $itemConf['disc'];
                $subtotal = max(0, ($product->price * $qty) - $disc);

                $order->items()->create([
                    'product_id' => $product->id,
                    'quantity' => $qty,
                    'unit_price' => $product->price,
                    'discount_amount' => $disc,
                    'subtotal' => $subtotal,
                ]);

                $totalAmount += $subtotal;
            }

            // Calculate DP and payments
            $dp = round($totalAmount * $sc['dp_percent'], -2);
            $totalPaid = $dp;

            foreach ($sc['payment_history'] as $hIdx => $history) {
                $payAmount = round($totalAmount * $history['amount_percent'], -2);
                $totalPaid += $payAmount;

                OrderPayment::create([
                    'sales_order_id' => $order->id,
                    'payment_number' => 'PAY-'.Carbon::today()->subDays($history['days_ago'])->format('Ymd').'-'.str_pad($order->id * 10 + $hIdx, 3, '0', STR_PAD_LEFT),
                    'payment_date' => Carbon::today()->subDays($history['days_ago']),
                    'amount' => $payAmount,
                    'payment_method' => $history['method'],
                    'notes' => $history['notes'],
                    'received_by_user_id' => $salesUser->id,
                ]);
            }

            $remaining = max(0, $totalAmount - $totalPaid);
            $status = $sc['status'];
            if ($remaining <= 0) {
                $status = 'lunas';
            } elseif ($dueDate->lt(Carbon::today())) {
                $status = 'jatuh_tempo';
            } elseif ($totalPaid > 0) {
                $status = 'sebagian';
            } else {
                $status = 'belum_lunas';
            }

            $order->update([
                'total_amount' => $totalAmount,
                'down_payment' => $dp,
                'paid_amount' => $totalPaid,
                'remaining_amount' => $remaining,
                'payment_status' => $status,
            ]);
        }
    }
}
